<?php

namespace spec\Cloudinary\Cloudinary\Plugin;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Uploader;
use CloudinaryExtension\Image;
use CloudinaryExtension\CloudinaryImageManager;
use Cloudinary\Cloudinary\Plugin\FileUploader;
use PhpSpec\ObjectBehavior;

class FileUploaderSpec extends ObjectBehavior
{
    function let(
        CloudinaryImageManager $cloudinaryImageManager,
        DirectoryList $directoryList
    ) {
        $directoryList->getPath('media')->willReturn('/var/app/media');

        $this->beConstructedWith(
            $cloudinaryImageManager,
            $directoryList
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FileUploader::class);
    }

    function it_uploads_wysiwyg_file(
        Uploader $uploader,
        CloudinaryImageManager $cloudinaryImageManager
    ) {
        $image = Image::fromPath('/var/app/media/wysiwyg/foo.jpg', 'wysiwyg/foo.jpg');

        $cloudinaryImageManager->uploadAndSynchronise($image)->shouldBeCalled();

        $this->afterSave($uploader, ['path' => '/var/app/media/wysiwyg', 'file' => 'foo.jpg']);
    }

    function it_does_not_upload_tmp_file(
        Uploader $uploader,
        CloudinaryImageManager $cloudinaryImageManager
    ) {
        $image = Image::fromPath('/var/app/media/tmp/foo.jpg', 'tmp/foo.jpg');

        $cloudinaryImageManager->uploadAndSynchronise($image)->shouldNotBeCalled();

        $this->afterSave($uploader, ['path' => '/var/app/media/tmp', 'file' => 'foo.jpg']);
    }
}
