<?php

namespace spec\CloudinaryExtension;

use CloudinaryExtension\Cloud;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;
use CloudinaryExtension\Configuration;
use CloudinaryExtension\ConfigurationInterface;

use CloudinaryExtension\UploadConfig;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ConfigurationBuilderSpec extends ObjectBehavior
{
    function let(ConfigurationInterface $configuration)
    {
        $this->beConstructedWith($configuration);

        $configuration->getCloud()->willReturn(Cloud::fromName('testCloud'));
        $configuration->getCredentials()->willReturn(Credentials::fromKeyAndSecret(
            Key::fromString('apiKey'),
            Secret::fromString('apiSecret')
        ));
        $configuration->getDefaultTransformation()->willReturn(Transformation::builder());
        $configuration->getUserPlatform()->willReturn('');
        $configuration->getUploadConfig()->willReturn(UploadConfig::fromBooleanValues(false, false, false));
    }

    function it_should_build_configuration_with_all_values(ConfigurationInterface $configuration)
    {
        $configuration->getCdnSubdomainStatus()->willReturn(true);

        $expected  = [
            'cloud_name' => 'testCloud',
            'api_key' => 'apiKey',
            'api_secret' => 'apiSecret',
            'cdn_subdomain' => true
        ];

        $this->build()->shouldReturn($expected);
    }

    function it_should_build_configuration_with_out_cdn(ConfigurationInterface $configuration)
    {
        $configuration->getCdnSubdomainStatus()->willReturn(false);

        $expected  = [
            'cloud_name' => 'testCloud',
            'api_key' => 'apiKey',
            'api_secret' => 'apiSecret'
        ];

        $this->build()->shouldReturn($expected);
    }
}
