<?php

use CloudinaryExtension\Migration\SynchronizedMediaRepository;

class Cloudinary_Cloudinary_Model_SynchronisedMediaUnifier implements SynchronizedMediaRepository
{
    /**
     * @var [SynchronizedMediaRepository]
     */
    private $_synchronisedMediaRepositories;

    /**
     * Cloudinary_Cloudinary_Model_SynchronisedMediaUnifier constructor.
     * @param [SynchronizedMediaRepository]
     */
    public function __construct(array $synchronisedMediaRepositories)
    {
        $this->_synchronisedMediaRepositories = $synchronisedMediaRepositories;
    }

    /**
     * @param int $limit
     * @return [Cloudinary_Cloudinary_Model_Synchronisation]
     */
    public function findUnsynchronisedImages($limit = 200)
    {
        return array_slice($this->findUnlimitedUnsynchronisedImages(), 0, $limit);
    }

    /**
     * @return [Cloudinary_Cloudinary_Model_Synchronisation]
     */
    private function findUnlimitedUnsynchronisedImages()
    {
        return array_reduce(
            $this->_synchronisedMediaRepositories,
            function($carry, $synchronisedMediaRepository) {
                return array_merge($carry, $synchronisedMediaRepository->findUnsynchronisedImages());
            },
            array()
        );
    }

    /**
     * @return [Cloudinary_Cloudinary_Model_Synchronisation]
     */
    public function findOrphanedSynchronisedImages()
    {
        return array_reduce(
            $this->_synchronisedMediaRepositories,
            function($carry, $synchronisedMediaRepository) {
                return array_merge($carry, $synchronisedMediaRepository->findOrphanedSynchronisedImages());
            },
            array()
        );
    }
}
