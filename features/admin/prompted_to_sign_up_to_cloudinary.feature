Feature: Admin is prompted to sign up to Cloudinary
  In order to register for a Cloudinary account after installing the extension
  As a store admin
  I should be prompted to sign up to Cloudinary

  @javascript @ui
  Scenario: Being prompted to sign up to Cloudinary
    Given I have not configured my cloud and credentials
    When I go to the Cloudinary configuration
    Then I should be prompted to sign up to Cloudinary

  @javascript @ui
  Scenario: Not being prompted to sign up to Cloudinary
    Given I have configured my cloud and credentials
    When I go to the Cloudinary configuration
    Then I should not be prompted to sign up to Cloudinary