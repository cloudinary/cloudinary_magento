<?php
namespace CloudinaryExtension;

/**
 * Class CloudinaryImageManager
 * @package CloudinaryExtension
 */
class CloudinaryImageManager
{
    /**
     * @var ImageProvider
     */
    private $cloudinaryImageProvider;

    /**
     * @var SynchroniseAssetsRepositoryInterface
     */
    private $synchronisationRepository;

    /**
     * CloudinaryImageManager constructor.
     *
     * @param ImageProvider $cloudinaryImageProvider
     * @param SynchroniseAssetsRepositoryInterface $synchronisationRepository
     */
    public function __construct(
        ImageProvider $cloudinaryImageProvider,
        SynchroniseAssetsRepositoryInterface $synchronisationRepository
    ) {
        $this->cloudinaryImageProvider = $cloudinaryImageProvider;
        $this->synchronisationRepository = $synchronisationRepository;
    }

    /**
     * @param Image $image
     */
    public function uploadAndSynchronise(Image $image)
    {
        $this->cloudinaryImageProvider->upload($image);
        $this->synchronisationRepository->saveAsSynchronized($image->getRelativePath());

    }

    /**
     * @param Image $image
     */
    public function removeAndUnSynchronise(Image $image)
    {
        $this->cloudinaryImageProvider->delete($image);
        $this->synchronisationRepository->removeSynchronised($image->getRelativePath());
    }
}