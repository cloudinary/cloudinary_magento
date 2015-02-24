<?php

use CloudinaryExtension\Migration\SynchronizedMediaRepository;

class Cloudinary_Cloudinary_Model_SynchronisedMediaUnifier implements SynchronizedMediaRepository
{

    private $_synchronisedMediaRepositories;
    private $_unsychronisedImages = array();

    public function __construct(array $synchronisedMediaRepositories)
    {
        $this->_synchronisedMediaRepositories = $synchronisedMediaRepositories;
    }

    public function findUnsynchronisedImages($limit = 200)
    {
        foreach ($this->_synchronisedMediaRepositories as $synchronisedMediaRepository) {
            $this->_unsychronisedImages = array_merge(
                $this->_unsychronisedImages,
                $synchronisedMediaRepository->findUnsynchronisedImages()
            );
        }
        return array_slice($this->_unsychronisedImages, 0, $limit);
    }

}