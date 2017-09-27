Feature: Removing images from the image provider
  In order to keep the image gallery content relevant
  As a store admin
  I want deleted product images to be removed from the image provider

  @e2e
  Scenario: Administrator deletes product image
    Given the product "Apple" exists
    And the Cloudinary module credentials are set
    And the Cloudinary module integration is enabled
    And the Cloudinary module foldered mode is inactive
    And the product "Apple" has an image "apple.jpg" on the image provider
    When I delete the images from product "Apple"
    Then there are no images for the "Apple" product in the image provider root folder
