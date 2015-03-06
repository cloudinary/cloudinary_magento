Feature: Getting transformed images from the provider
  In order to reduce load times
  As a Store Admin
  I want provide transformed versions of the images

  Scenario: Getting a cropped image from the provider
    Given the image provider has an image "pink_dress.gif"
    When I ask the image provider for "pink_dress.gif" transformed to "100x150"
    Then I should receive that image with the dimensions "100x150"

  @not-automated
  Scenario: Getting a image without gravity transformation from the provider
    Given my image provider has an image "pink_dress.gif"
    When I ask the image provider for "pink_dress.gif"
    Then I should receive that image with no gravity set

  @not-automated
  Scenario: Getting a gravity transformed image from the provider
    Given my image provider has an image "pink_dress.gif"
    And I have set the default image gravity to "Center"
    When I ask the image provider for "pink_dress.gif"
    Then I should receive that image with gravity "Center"

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