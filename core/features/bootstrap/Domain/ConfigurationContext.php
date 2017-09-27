<?php

namespace Domain;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use CloudinaryExtension\Cloud;
use CloudinaryExtension\Configuration;
use CloudinaryExtension\ConfigurationInterface;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\Image;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;
use ImageProviders\ConfigImageProvider;
use Prophecy\Prophet;

require_once 'PHPUnit/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class ConfigurationContext implements Context
{
    private $configuration;
    private $imageProvider;

    public function __construct()
    {
        $this->configuration = Doubles::getConfiguration();
    }

    /**
     * @Given I have a configuration to use multiple sub-domains
     */
    public function iHaveAConfigurationToUseMultipleSubDomains()
    {
        Doubles::getConfigurationProphecy()->getCdnSubdomainStatus()->willReturn(true);
    }

    /**
     * @When I apply the configuration to the image provider
     */
    public function iApplyTheConfigurationToTheImageProvider()
    {
        $this->imageProvider = new ConfigImageProvider($this->configuration);
    }

    /**
     * @Then the image provider should use multiple sub-domains
     */
    public function theImageProviderShouldUseMultipleSubDomains()
    {
        $request1 = $this->imageProvider->retrieveTransformed(Image::fromPath('somePath'), Transformation::builder());
        $request2 = $this->imageProvider->retrieveTransformed(Image::fromPath('someOtherPath'), Transformation::builder());

        expect($this->requestPrefixIsTheSame($request1, $request2))->toBe(false);
    }

    /**
     * @Given the cloudinary module is disabled
     */
    public function theCloudinaryModuleIsDisabled()
    {
        Doubles::getConfigurationProphecy()->isEnabled()->willReturn(false);
    }

    /**
     * @Given the cloudinary module is enabled
     */
    public function theCloudinaryModuleIsEnabled()
    {
        Doubles::getConfigurationProphecy()->isEnabled()->willReturn(true);
    }

    private function requestPrefixIsTheSame($request1, $request2)
    {
        return substr($request1, 0, 4) === substr($request2, 0, 4);
    }
}