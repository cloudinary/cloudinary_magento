<?php

namespace spec;

use CloudinaryExtension\Image\Synchronizable;
use CloudinaryExtension\Migration\SynchronizedMediaRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Cloudinary_Cloudinary_Model_SynchronisedMediaUnifierSpec extends ObjectBehavior
{

    function let(
        SynchronizedMediaRepository $repositoryOne,
        SynchronizedMediaRepository $repositoryTwo
    )
    {
        $this->beConstructedWith(array($repositoryOne, $repositoryTwo));
    }

    function it_should_be_a_repository_of_synchronised_media()
    {
        $this->shouldHaveType('CloudinaryExtension\Migration\SynchronizedMediaRepository');
    }

    function it_should_combine_multiple_synchronised_media_repositories(
        $repositoryOne,
        $repositoryTwo,
        Synchronizable $synchronisableImageOne,
        Synchronizable $synchronisableImageTwo,
        Synchronizable $synchronisableImageThree,
        Synchronizable $synchronisableImageFour
    )
    {
        $repositoryOne->findUnsynchronisedImages()->willReturn(
            array(
                $synchronisableImageOne,
                $synchronisableImageTwo,
            )
        );
        $repositoryTwo->findUnsynchronisedImages()->willReturn(
            array(
                $synchronisableImageThree,
                $synchronisableImageFour,
            )
        );

        $this->findUnsynchronisedImages()->shouldReturn(
            array(
                $synchronisableImageOne,
                $synchronisableImageTwo,
                $synchronisableImageThree,
                $synchronisableImageFour
            )
        );
    }

    function it_should_return_no_items_if_all_repositories_have_been_synchronised(
        $repositoryOne,
        $repositoryTwo
    )
    {
        $repositoryOne->findUnsynchronisedImages()->willReturn(array());
        $repositoryTwo->findUnsynchronisedImages()->willReturn(array());

        $this->findUnsynchronisedImages()->shouldReturn(array());
    }

}