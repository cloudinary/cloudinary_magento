<?php

namespace spec\Cloudinary;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class OnlineImageSpec extends ObjectBehavior
{
    function it_should_have_an_url()
    {
        $this->getUrl()->shouldNotBeNull();
    }
}
