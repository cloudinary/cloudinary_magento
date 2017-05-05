Feature: Maintain original image folder path
  In order to maintain good SEO and allow identical filenames for different folders
  As a store admin
  I want the option for the folder path of product images to be mirrored by the image provider

  @e2e
  Scenario: Administrator adds image to product
    Given the product "Apple" exists
    And the Cloudinary module credentials are set
    And the Cloudinary module integration is enabled
    And the Cloudinary module foldered mode is active
    When image "apple.jpg" is added to product "Apple"
    Then the image can be seen on the image provider in the correct folder for product "Apple"

  @e2e
  Scenario: Administrator deletes product image
    Given the product "Apple" exists
    And the Cloudinary module credentials are set
    And the Cloudinary module integration is enabled
    And the Cloudinary module foldered mode is active
    And the image provider has an image "apple.jpg" in the correct folder for product "Apple"
    When I delete the images from product "Apple"
    Then the image can not be seen on the image provider in the correct folder for product "Apple"
