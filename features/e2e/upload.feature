Feature: Uploading images to the image provider
  In order to keep the image gallery content relevant
  As a store admin
  I want new product images to be uploaded to the image provider

  @e2e
  Scenario: Administrator adds image to product
    Given the product "Apple" exists
    And the Cloudinary module credentials are set
    And the Cloudinary module integration is enabled
    And the Cloudinary module foldered mode is inactive
    When image "apple.jpg" is added to product "Apple"
    Then the image for product "Apple" can be seen in the image provider root folder
