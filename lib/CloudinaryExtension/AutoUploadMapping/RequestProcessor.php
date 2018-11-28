<?php

namespace CloudinaryExtension\AutoUploadMapping;

class RequestProcessor
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ApiClient
     */
    private $apiClient;

    /**
     * RequestProcessor constructor.
     * @param Configuration $configuration
     * @param ApiClient $apiClient
     * @param string $url
     */
    public function __construct(
        Configuration $configuration,
        ApiClient $apiClient
    ) {
        $this->configuration = $configuration;
        $this->apiClient = $apiClient;
    }

    /**
     * @param string $folder
     * @param string $url
     * @return bool
     */
    public function handle($folder, $url)
    {
        if ($this->configuration->isActive() == $this->configuration->getRequestState()) {
            return true;
        }

        if ($this->configuration->getRequestState() == Configuration::ACTIVE) {
            return $this->handleActiveRequest($folder, $url);
        }

        $this->configuration->setState(Configuration::INACTIVE);

        return true;
    }

    /**
     * @param string $folder
     * @param string $url
     * @return bool
     */
    private function handleActiveRequest($folder, $url)
    {
        $result = $this->apiClient->prepareMapping($folder, $url);

        if ($result) {
            $this->configuration->setState(Configuration::ACTIVE);
        } else {
            $this->configuration->setRequestState(Configuration::INACTIVE);
        }

        return $result;
    }
}
