Feature: Uploading images to an image provider
    In order to optimise images for specific web clients
    As a Store Admin
    I want to be able to upload images to the image provider

    @javascript
    Scenario: Uploading an image using the correct keys
      Given I have an image "pink_dress.gif"
      And the image provider is aware of credentials with the API key "ABC123" and the secret "DEF456"
      When I upload the image "pink_dress.gif" using the correct credentials
      Then the image should be available through the image provider
