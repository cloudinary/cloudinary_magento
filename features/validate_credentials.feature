Feature: Validating credentials used for image provider
  In order to interact with the image provider
  As a store admin
  I want to know that the credentials I have provided are valid

  Scenario: Validate correct credentials are being used
    Given I have configured the "session-digital" cloud using valid credentials
    And the image provider has a "session-digital" cloud
    When I ask the provider to validate my credentials
    Then I should be informed my credentials are valid

  Scenario: Report an error if incorrect credentials are used
    Given I have configured the "session-digital" cloud using using invalid credentials
    And the image provider has a "session-digital" cloud
    When I ask the provider to validate my credentials
    Then I should be informed that my credentials are not valid