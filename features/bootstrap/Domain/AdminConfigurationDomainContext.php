<?php

namespace Domain;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use CloudinaryExtension\Cloud;
use CloudinaryExtension\Configuration;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\Image\Gravity;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;

class AdminConfigurationDomainContext implements Context
{
    /**
     * @var Configuration
     */
    private $config;

    /**
     * @Given I have not set a default image gravity
     */
    public function iHaveNotSetADefaultImageGravity()
    {
        $this->config = $this->buildConfig();
    }

    /**
     * @When I go to the cloudinary configuration
     */
    public function iGoToTheCloudinaryConfiguration()
    {
    }

    /**
     * @Then no gravity should be selected yet
     */
    public function noGravityShouldBeSelectedYet()
    {
        $defaultTransformation =  $this->config->getDefaultTransformation();
        expect($defaultTransformation)->toBeAnInstanceOf('CloudinaryExtension\Image\Transformation');

        $gravity = $defaultTransformation->getGravity();

        expect($gravity)->toBeAnInstanceOf('CloudinaryExtension\Image\Gravity');
        expect($gravity->getValue())->toBe(null);
    }

    /**
     * @Given I have set a the default image gravity to :gravity
     */
    public function iHaveSetATheDefaultImageGravityTo($gravity)
    {
        $this->config = $this->buildConfig();

        $transformation = new Transformation();

        $this->config->setDefaultTransformation(
            $transformation->withGravity(Gravity::fromString($gravity))
        );
    }

    /**
     * @Then the default gravity should be set to :gravity
     */
    public function theDefaultGravityShouldBeSetTo($gravityValue)
    {
        $defaultTransformation =  $this->config->getDefaultTransformation();
        expect($defaultTransformation)->toBeAnInstanceOf('CloudinaryExtension\Image\Transformation');

        $gravity = $defaultTransformation->getGravity();

        expect($gravity)->toBeAnInstanceOf('CloudinaryExtension\Image\Gravity');
        expect($gravity->getValue())->toBe($gravityValue);

    }

    private function buildConfig()
    {
        return Configuration::fromCloudAndCredentials(
            new Credentials(Key::fromString(''), Secret::fromString('')),
            Cloud::fromName('')
        );
    }
}