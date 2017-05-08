<?php

namespace Cloudinary\Cloudinary\Model;

use CloudinaryExtension\Image\SynchronizationChecker as SynchronisationCheckerInterface;
use Cloudinary\Cloudinary\Api\SynchronisationRepositoryInterface;

class SynchronisationChecker implements SynchronisationCheckerInterface
{
    /**
     * @var SynchronisationRepositoryInterface
     */
    private $synchronisationRepository;

    /**
     * @param SynchronisationRepositoryInterface $synchronisationRepository
     */
    public function __construct(SynchronisationRepositoryInterface $synchronisationRepository)
    {
        $this->synchronisationRepository = $synchronisationRepository;
    }

    /**
     * @param $imageName
     * @return bool
     */
    public function isSynchronized($imageName)
    {
        if (!$imageName) {
            return false;
        }
        
        return $this->synchronisationRepository->getListByImagePath($imageName)->getTotalCount() > 0;
    }
}
