Feature: Configuring the default image gravity

  As a Store Admin
  I need to be able to set the default image gravity

  @javascript @critical
  Scenario: Not having selected a default gravity
    Given I have not set a default image gravity
     When I go to the cloudinary configuration
     Then no gravity should be selected yet

  @javascript @critical
  Scenario: Having selected a default gravity
    Given I have set a the default image gravity to "Center"
     When I go to the cloudinary configuration
     Then the default gravity should be set to "Center"