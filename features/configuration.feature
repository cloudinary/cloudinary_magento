Feature: Configuring the image provider
  In order to fit the image provider to my needs
  As an image provider user
  I want to be able to provide configuration to it

  Scenario: Configuring the image provider to use multiple sub-domains
    Given I have a configuration to use multiple sub-domain
    When I apply the configuration to the image provider
    Then the image provider should use multiple sub-domains
