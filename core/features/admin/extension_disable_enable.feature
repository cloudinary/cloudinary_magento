@not-automated
Feature: Enabling and disabling the Cloudinary extension

  As an Integrator


  Scenario: Integrator enables the extension but image has not been migrated
    Given the cloudinary media gallery contains the image "lolcat.png"
    And this image has not yet been migrated to cloudinary
    When the integrator enables the module
    Then the image should be provided locally

  Scenario: Integrator enables the extension and image has been migrated
    Given the cloudinary media gallery contains the image "lolcat.png"
    And this image has already been migrated to cloudinary
    When the integrator enables the module
    Then the image should be provided by cloudinary

  Scenario: Integrator disables the extension
    Given the cloudinary media gallery contains the image "lolcat.png"
    When the integrator disables the module
    Then the image should be provided locally