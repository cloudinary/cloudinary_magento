<?php

namespace spec\Cloudinary;

use Cloudinary\Credentials\Key;
use Cloudinary\Credentials\Secret;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CredentialsSpec extends ObjectBehavior
{
    private $key = 'aKey';
    private $secret = 'aSecret';

    function let()
    {
        $this->beConstructedWith(Key::fromString($this->key), Secret::fromString($this->secret));
    }

    function it_returns_the_correct_key()
    {
        $this->getKey()->shouldBeLike($this->key);
    }

    function it_returns_the_correct_secret()
    {
        $this->getSecret()->shouldBeLike($this->secret);
    }
}
