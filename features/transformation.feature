Feature: Transforming an image
  In order to customise images for various purposes
  As an application provider
  I want to be able apply transformation to images

  Scenario: Getting an optimised image from the image provider
    Given there's an image "white_and_gold_dress.jpg" in the image provider
    When I request the image from the image provider
    Then I should get an optimised image from the image provider

  Scenario: Getting the original image from the image provider
    Given there's an image "blue_and_black_dress.jpg" in the image provider
    And image optimisation is disabled
    When I request the image from the image provider
    Then I should get the original image from the image provider

  Scenario: Getting an image at the default quality of 80 percent
    Given there's an image "red-shirt.jpg" in the image provider
    When I request the image from the image provider
    Then I should get an image with 80 percent quality from the image provider

  Scenario: Changing image quality to 60 percent
    Given there's an image "red-shirt.jpg" in the image provider
    And I transform the image to have 60 percent quality
    When I request the image from the image provider
    Then I should get an image with 60 percent quality from the image provider
