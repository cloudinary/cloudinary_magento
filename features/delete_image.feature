Feature: Deleting images from the image provider
  In order to keep the image gallery content relevant
  As a store admin
  I want to be able to delete images from the image provider

  Scenario: Administrator deletes image from image provider
    Given the image provider has an image "blue-shirt.jpg"
    When I delete the "blue-shirt.jpg" image
    Then the image "blue-shirt.jpg" should no longer be available in the image provider