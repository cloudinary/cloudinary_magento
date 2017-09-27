<?php

namespace spec\Cloudinary\Cloudinary\Model;

use Cloudinary\Cloudinary\Model\SynchronisationRepository;
use Magento\Framework\Api\SearchResults;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SynchronisationCheckerSpec extends ObjectBehavior
{
    function let(SynchronisationRepository $synchronisationRepository)
    {
        $this->beConstructedWith($synchronisationRepository);
    }

    function it_validates_not_synchronized_for_null_image_name()
    {
        $imageName = '';
        $this->isSynchronized($imageName)->shouldBe(false);
    }

    function it_validates_not_synchronized_if_collection_for_given_image_name_is_empty(
        SynchronisationRepository $synchronisationRepository,
        SearchResults $searchResults
    )
    {
        $imageName = 'pink_dress.gif';
        $searchResults->getTotalCount()->willReturn(0);

        $synchronisationRepository->getListByImagePath($imageName)
            ->shouldBeCalled()
            ->willReturn($searchResults);

        $this->isSynchronized($imageName)->shouldBe(false);
    }

    function it_validates_synchronized_for_a_valid_image_name(
        SynchronisationRepository $synchronisationRepository,
        SearchResults $searchResults
    )
    {
        $imageName = 'pink_dress.gif';
        $searchResults->getTotalCount()->willReturn(1);

        $synchronisationRepository->getListByImagePath($imageName)
            ->shouldBeCalled()
            ->willReturn($searchResults);

        $this->isSynchronized($imageName)->shouldBe(true);
    }
}