Feature: Validating environment variable used for image provider
  In order to interact with the image provider
  As a store admin
  I want to know that the environment variable I have configured is valid

  @javascript @critical
  Scenario: Validate correct environment variable is being used
    Given I have used a valid environment variable in the configuration
    When I ask the provider to validate my credentials
    Then I should be informed my credentials are valid

  @javascript @critical
  Scenario: Report an error if incorrect environment variable is used
    Given I have used an invalid environment variable in the configuration
    When I ask the provider to validate my credentials
    Then I should be informed that my credentials are not valid