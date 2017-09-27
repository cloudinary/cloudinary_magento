<?php

namespace Page;

use CloudinaryExtension\Image;
use Helpers\PageObjectHelperMethods;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Product extends Page
{
    use PageObjectHelperMethods;

    protected $path = '/{url_key}.html';

    protected $elements = [
        'Main Image' => ['css' => '.fotorama__img']
    ];

    function getMainImageUrl()
    {
        return $this->getElementWithWait('Main Image')->getAttribute('src');
    }

    function hasCloudinaryImageUrl(Image $image)
    {
        return (strpos($this->getMainImageUrl(), 'cloudinary.com') !== false)
        && (strpos($this->getMainImageUrl(), $image->getId()) !== false);
    }
}
