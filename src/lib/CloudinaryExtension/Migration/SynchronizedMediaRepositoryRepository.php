<?php

namespace CloudinaryExtension\Migration;

class SynchronizedMediaRepositoryRepository implements SynchronizedMediaRepository
{

    private $synchronisedMediaRepositories;
    private $unsychronisedImages = array();

    public function __construct(array $synchronisedMediaRepositories)
    {
        $this->synchronisedMediaRepositories = $synchronisedMediaRepositories;
    }

    public function findUnsynchronisedImages($limit = 200)
    {
        foreach ($this->synchronisedMediaRepositories as $synchronisedMediaRepository) {
            $this->unsychronisedImages = array_merge(
                $this->unsychronisedImages,
                $synchronisedMediaRepository->findUnsynchronisedImages()
            );
        }
        return array_slice($this->unsychronisedImages, 0, $limit);
    }

}