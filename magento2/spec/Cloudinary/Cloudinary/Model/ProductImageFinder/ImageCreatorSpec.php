<?php

namespace spec\Cloudinary\Cloudinary\Model\ProductImageFinder;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;


use CloudinaryExtension\Image;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Catalog\Model\Product\Media\Config as MediaConfig;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

class ImageCreatorSpec extends ObjectBehavior
{
    function let(Filesystem $filesystem, MediaConfig $mediaConfig)
    {
        $this->beConstructedWith($filesystem, $mediaConfig);
    }

    function it_should_return_instance_of_an_image(
        Filesystem $filesystem,
        ReadInterface $mediaDirectory
    ) {

        $filesystem->getDirectoryRead(DirectoryList::MEDIA)
            ->shouldBeCalled()
            ->willReturn($mediaDirectory);

        $imageData = ['file' => '/p/i/pink_dress.gif'];

        $this->__invoke($imageData)->shouldBeAnInstanceOf('CloudinaryExtension\Image');
    }
}