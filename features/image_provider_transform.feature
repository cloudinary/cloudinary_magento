Feature: Getting transformed images from the provider
    In order to reduce load times
    As a Store Admin
    I want provide transformed versions of the images

    Scenario: Getting a cropped image from the provider
      Given my image provider has an image "pink_dress.gif"
      When I ask the image provider for "pink_dress.gif" transformed to "100x150"
      Then I should receive that image with the dimensions "100x150"

    Scenario: Getting a gravity transformed image from the provider
      Given my image provider has an image "pink_dress.gif"
       And I have set the default image gravity to "Center"
       When I ask the image provider for "pink_dress.gif"
       Then I should receive that image with gravity "Center"