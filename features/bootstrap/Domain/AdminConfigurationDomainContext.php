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
     * @Transform :gravity
     */
    public function transformStringToGravity($string)
    {
        return Gravity::fromString($string);
    }

    /**
     * @beforeScenario
     */
    public function setup()
    {
        $this->config = $this->buildConfig();
    }

    /**
     * @Given I have not set a default image gravity
     */
    public function iHaveNotSetADefaultImageGravity()
    {
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
    public function iHaveSetATheDefaultImageGravityTo(Gravity $gravity)
    {
        $this->config->setDefaultTransformation(
            Transformation::build()->withGravity($gravity)
        );
    }

    /**
     * @Then the default gravity should be set to :gravity
     */
    public function theDefaultGravityShouldBeSetTo(Gravity $gravity)
    {
        $defaultTransformation =  $this->config->getDefaultTransformation();
        expect($defaultTransformation)->toBeAnInstanceOf('CloudinaryExtension\Image\Transformation');

        $transformationGravity = $defaultTransformation->getGravity();

        expect($transformationGravity)->toBeLike($gravity);
    }

    private function buildConfig()
    {
        return Configuration::fromCloudAndCredentials(
            new Credentials(Key::fromString(''), Secret::fromString('')),
            Cloud::fromName('')
        );
    }
}
