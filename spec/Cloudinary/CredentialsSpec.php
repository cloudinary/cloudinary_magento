<?php

namespace spec\Cloudinary;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CredentialsSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Cloudinary\Credentials');
    }
}
