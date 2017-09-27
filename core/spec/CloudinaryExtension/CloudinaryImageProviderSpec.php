<?php

namespace spec\CloudinaryExtension;

use Cloudinary;
use CloudinaryExtension\ConfigurationBuilder;
use CloudinaryExtension\ConfigurationInterface;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\CredentialValidator;
use CloudinaryExtension\UploadResponseValidator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CloudinaryImageProviderSpec extends ObjectBehavior
{
    function let(
        ConfigurationInterface $configuration,
        ConfigurationBuilder $configurationBuilder,
        UploadResponseValidator $uploadResponseValidator,
        CredentialValidator $credentialValidator,
        Credentials $credentials
    )
    {
        $credentials->getKey()->willReturn('apiKey');
        $credentials->getSecret()->willReturn('apiSecret');

        $configuration->getCloud()->willReturn('testCloud');
        $configuration->getCredentials()->willReturn($credentials);
        $configuration->getCdnSubdomainStatus()->willReturn(true);

        $this->beConstructedThrough('fromConfiguration', [
            $configuration,
            $configurationBuilder,
            $uploadResponseValidator,
            $credentialValidator
        ]);
    }

    function it_sets_user_agent_string(ConfigurationInterface $configuration)
    {
        $configuration->getUserPlatform()->willReturn('Test User Agent String');

        $this->getWrappedObject();
        expect(Cloudinary::$USER_PLATFORM)->toBe('Test User Agent String');
    }
}
