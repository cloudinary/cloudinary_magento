@javascript @critical
Feature: Cloudinary can be enabled or disabled

  Background:
    Given I am logged in as an administrator

  Scenario: Being able to enable cloudinary when its disabled
    Given the Cloudinary module is disabled
    When I access the Cloudinary configuration
    Then I should be able to enable the module

  Scenario: Being able to disable cloudinary when its enabled
    Given the Cloudinary module is enabled
    When I access the Cloudinary configuration
    Then I should be able to disable the module

