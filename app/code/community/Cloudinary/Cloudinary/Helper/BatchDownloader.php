<?php

use CloudinaryExtension\Image;
use Cloudinary\Api;
use CloudinaryExtension\ConfigurationInterface;
use Cloudinary;
use Cloudinary_Cloudinary_Model_SynchronizationChecker as SynchronizationChecker;

class Cloudinary_Cloudinary_Helper_BatchDownloader extends Mage_Core_Helper_Abstract
{
    const MESSAGE_STATUS = 'Cloudinary migration: %s images migrated, %s failed';
    const MESSAGE_UPLOADED = 'Cloudinary migration: downloaded %s';
    const MESSAGE_UPLOAD_ERROR = 'Cloudinary migration: %s trying to download %s';
    const MAXIMUM_RETRY_ATTEMPTS = 3;
    const MESSAGE_UPLOADED_EXISTS = 'Cloudinary migration: %s exists - tagged as synchronized';

    const API_REQUEST_MAX_RESULTS = 250;
    const API_REQUESTS_SLEEP_BEFORE_NEXT_CALL = 3; //Seconds
    const API_REQUEST_STOP_ON_REMAINING_RATE_LIMIT = 10;
    const WAIT_FOR_RATE_LIMIT_RESET_MESSAGE = "Cloudinary API - calls rate limit reached, sleeping until %s ...";
    const ERROR_MIGRATION_ALREADY_RUNNING = 'Cannot start download - a migration is in progress or was interrupted. If you are sure a migration is not running elsewhere run the cloudinary:download:stop command before attempting another download.';
    const MESSAGE_DOWNLOAD_INTERRUPTED = 'Download manually stopped.';
    const DONE_MESSAGE = "Done :)";

    /**
     * @var Task
     */
    private $migrationTask;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var SynchronizationChecker
     */
    private $synchronizationChecker;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var string
     */
    private $baseMediaPath;

    /**
     * @var bool
     */
    private $override = false;

    /**
     * @var Cloudinary\Api
     */
    private $api;

    /**
     * @var callable
     */
    private $_exceptionCallback;

    private $_rateLimitResetAt = null;
    private $_rateLimitAllowed = null;
    private $_rateLimitRemaining = null;
    private $_info = array(
        "iteration" => 0,
        "next_cursor" => null,
        "resources_count" => 0,
        "resources_processed" => 0,
        "resources_downloaded" => 0,
        "resources_skipped" => 0,
        "resources_failed" => 0,
        "resources_processed_total" => 0,
        "resources_downloaded_total" => 0,
        "resources_skipped_total" => 0,
        "resources_failed_total" => 0,
        "resources_count_total" => 0,
        "more_expected" => true,
    );

    /**
     * @method __construct
     */
    public function __construct()
    {
        $this->migrationTask = Mage::getModel('cloudinary_cloudinary/migration')->loadType(Cloudinary_Cloudinary_Model_Migration::DOWNLOAD_MIGRATION_TYPE);
        $this->configuration = Mage::getModel('cloudinary_cloudinary/configuration');
        $this->synchronizationChecker = Mage::getModel('cloudinary_cloudinary/SynchronizationChecker');
        $this->logger = Mage::getModel('cloudinary_cloudinary/logger');
        $this->baseMediaPath = Mage::getBaseDir('media');
        $this->override = false;
        $this->api = new Api();
    }

    private function _authorise()
    {
        Cloudinary::config($this->getCredentialsFromEnvironmentVariable($this->configuration->getEnvironmentVariable()->__toString()));
        Cloudinary::$USER_PLATFORM = $this->configuration->getUserPlatform();
    }

    public function downloadImages()
    {
        if ($this->configuration->isEnabled()) {
            $this->_authorise();
        } else {
            return $this->log("Cloudinary module have been disabled, please enable in order to proceed...");
        }

        try {
            $this->saveInfo($this->migrationTask->getInfo());

            $this->_info["iteration"]++;
            $this->log('Iteration #' . $this->_info["iteration"]);
            $this->migrationTask->setBatchCount($this->_info["iteration"]);
            $this->saveInfo(array(
                "iteration" => $this->_info["iteration"],
                "resources_count" => 0,
                "resources_processed" => 0,
                "resources_downloaded" => 0,
                "resources_skipped" => 0,
                "resources_failed" => 0,
            ));

            $response = $this->getResources($this->_info["next_cursor"]);
            $response->setResourcesCount(count($response->getResources()));
            $this->saveInfo(array(
                "next_cursor" => $response->getNextCursor(),
                "more_expected" => ($response->getNextCursor())? true : false,
                "resources_count" => $this->_info["resources_count"] + $response->getResourcesCount(),
                "resources_count_total" => $this->_info["resources_count_total"] + $response->getResourcesCount(),
            ));
            if ($response->getResourcesCount() > 0) {
                $this->log('Found ' . $response->getResourcesCount() . ' image(s) to sync on this round. ' . (($response->getNextCursor()) ? '*More Rounds Expected*' : '*Last Round*'));
                foreach ($response->getResources() as $i => &$resource) {
                    try {
                        //= Checking migration status
                        if ($this->migrationTask->hasBeenStopped()) {
                            $this->log(self::MESSAGE_DOWNLOAD_INTERRUPTED);
                            return false;
                        }

                        //= Preparations & Validations
                        $resource = new Varien_Object($resource);
                        $this->log('= [Processing] Image ' . ($i+1) . '/' . $response->getResourcesCount());
                        $this->saveInfo(array("resources_processed" => $this->_info["resources_processed"]+1, "resources_processed_total" => $this->_info["resources_processed_total"]+1));
                        $resource->setPublicId(preg_replace('/^' . preg_quote('media' . DIRECTORY_SEPARATOR, '/') . '/', '', $resource->getPublicId()) . '.' . $resource->getFormat());
                        $this->log('=== [Processing] Public ID: ' . $resource->getPublicId());
                        $remoteFileUrl = $resource->getSecureUrl();
                        $localFileName = $resource->getPublicId();
                        $localFilePath = $this->baseMediaPath . DIRECTORY_SEPARATOR . $localFileName;
                        $this->validateRemoteFileExtensions($localFilePath);

                        //= Checking if already exists
                        $skipDownload = false;
                        $this->log('=== [Processing] Local path: ' . $localFilePath);
                        if (@file_exists($localFilePath)) {
                            $this->log('=== [Processing] Image already exists locally.');
                            if ($this->override) {
                                $this->log('=== [Processing] *Overriding*');
                            } else {
                                $skipDownload = true;
                            }
                        }

                        //= Downloading image / Skipping
                        if ($skipDownload) {
                            $this->log('=== [Processing] Skipping download.');
                            $this->saveInfo(array("resources_skipped" => $this->_info["resources_skipped"]+1, "resources_skipped_total" => $this->_info["resources_skipped_total"]+1));
                        } else {
                            $this->log('=== [Processing] Downloading image...');
                            $res = Cloudinary_Cloudinary_Helper_Data::curlGetContents($remoteFileUrl);
                            if (!$res || $res->getError() || empty(($image = $res->getBody()))) {
                                throw new Mage_Core_Exception(
                                        __('The preview image information is unavailable. Check your connection and try again.')
                                    );
                            }
                            $this->log('=== [Processing] Saving...');
                            Mage::getSingleton('core/file_storage_file')->saveFile(array('filename' => $localFileName, 'content' => $image), true);
                            if (!@file_exists($localFilePath)) {
                                throw new Mage_Core_Exception(__("Image not saved."));
                            }
                            $this->log('=== [Processing] Saved.');
                            $this->saveInfo(array("resources_downloaded" => $this->_info["resources_downloaded"]+1, "resources_downloaded_total" => $this->_info["resources_downloaded_total"]+1));
                        }

                        //Flagging as syncronized
                        $resource->setImage(Image::fromPath($localFilePath, $localFileName));
                        if ($resource->getImage()->getRelativePath() && !$this->synchronizationChecker->isSynchronized($resource->getImage()->getRelativePath())) {
                            $this->log('=== [Processing] Flagging As Syncronized...');
                            Mage::getModel('cloudinary_cloudinary/cms_synchronisation')
                                    ->setFilename($resource->getImage()->getRelativePath())
                                    ->tagAsSynchronized();
                        } else {
                            $this->log('=== [Processing] Image already syncronized or auto-upload-mapping is enabled.');
                        }

                        //= Success
                        $this->log('= [Success]');
                    } catch (\Exception $e) {
                        Cloudinary_Cloudinary_Model_MigrationError::saveFromNormalException($e, Cloudinary_Cloudinary_Model_Migration::DOWNLOAD_MIGRATION_TYPE);
                        $this->log('= [Error] ' . $e->getMessage(), "error");
                        $this->saveInfo(array("resources_failed" => $this->_info["resources_failed"]+1, "resources_failed_total" => $this->_info["resources_failed_total"]+1));
                        continue;
                    }
                }
            } else {
                $this->log(self::DONE_MESSAGE);
            }

            if (!$this->_info["next_cursor"]) {
                $this->migrationTask->stop();
            }
        } catch (\Exception $e) {
            Cloudinary_Cloudinary_Model_MigrationError::saveFromNormalException($e, Cloudinary_Cloudinary_Model_Migration::DOWNLOAD_MIGRATION_TYPE);
            $this->log($e->getMessage(), "error");
        }

        return true;
    }

    /**
     * @method getResources
     * @param  mixed       $nextCursor
     * @return DataObject
     */
    private function getResources($nextCursor = null)
    {
        $response = $this->api->resources(array(
            "resource_type" => 'image',
            "type" => "upload",
            "prefix" => 'media' . DIRECTORY_SEPARATOR,
            "max_results" => self::API_REQUEST_MAX_RESULTS,
            "next_cursor" => $nextCursor,
        ));
        $this->_rateLimitResetAt = $response->rate_limit_reset_at;
        $this->_rateLimitAllowed = $response->rate_limit_allowed;
        $this->_rateLimitRemaining = $response->rate_limit_remaining;
        $response->resources = array_values($response['resources']);
        return new Varien_Object((array)$response);
    }

    /**
     * @param string          $message
     */
    private function log($message, $type = 'notice')
    {
        switch ($type) {
            case 'error':
                $this->logger->error($message);
                break;
            default:
                $this->logger->notice($message);
                break;
        }
        return $this;
    }

    /**
     * Invalidates files that have script extensions.
     *
     * @param string $filePath
     * @throws Mage_Core_Exception
     * @return void
     */
    private function validateRemoteFileExtensions($filePath)
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        if (!in_array($extension, ['jpg','jpeg','gif','png'])) {
            throw new Mage_Core_Exception(__('Disallowed file type.'));
        }
    }

    /**
     * @param string $environmentVariable
     * @throws Mage_Core_Exception
     * @return array
     */
    private function getCredentialsFromEnvironmentVariable($environmentVariable)
    {
        try {
            Cloudinary::config_from_url(str_replace('CLOUDINARY_URL=', '', $environmentVariable));
            $credentials = array(
                "cloud_name" => Cloudinary::config_get('cloud_name'),
                "api_key" => Cloudinary::config_get('api_key'),
                "api_secret" => Cloudinary::config_get('api_secret')
            );
            if (Cloudinary::config_get('private_cdn')) {
                $credentials["private_cdn"] = Cloudinary::config_get('private_cdn');
            }
            return $credentials;
        } catch (\Exception $e) {
            throw new Mage_Core_Exception(__(self::CREDENTIALS_CHECK_FAILED));
        }
    }

    private function saveInfo(array $info = [])
    {
        $this->_info = array_merge($this->_info, $info);
        $this->migrationTask->setInfo($this->_info)->save();
        return $this;
    }
}
