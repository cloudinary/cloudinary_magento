@javascript @critical
Feature: Uploading images to an image provider
  In order to optimise images for specific web clients
  As a Store Admin
  I want to be able to upload images to the image provider

  Background:
    Given I am logged in as an administrator
    And the cloudinary module is disabled
    And the image "pink_dress.gif" does not exist on the provider

  Scenario: Image is provided locally when module is disabled
    Given the cloudinary module is disabled
    When I upload the image "pink_dress.gif"
    Then the image "pink_dress.gif" will be provided locally

  Scenario: Image is provided remotely when module is enabled
    Given the cloudinary module is enabled
    And the image "pink_dress.gif" does not exist on the provider
    When I upload the image "pink_dress.gif"
    Then the image "pink_dress.gif" will be provided remotely

  Scenario: Image with same ID already exists in the provider
    Given the cloudinary module is enabled
    But the image "pink_dress.gif" has already been uploaded
    When I upload the image "pink_dress.gif"
    Then I should see an error image already exists
