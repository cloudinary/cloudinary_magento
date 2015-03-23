Feature: Uploading images to an image provider
  In order to optimise images for specific web clients
  As a Store Admin
  I want to be able to upload images to the image provider

  @javascript @critical
  Scenario: Uploading an image
    Given I have an image "pink_dress.gif"
    When I upload the image "pink_dress.gif"
    Then the image should be available through the image provider