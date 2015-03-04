Feature: Configuring the default image gravity

  As a Store Admin
  I need to be able to set the default image gravity

  @javascript @critical
  Scenario: Not having selected a default gravity
    Given the default gravity is not set
     When I go to the cloudinary configuration
     Then no gravity should be selected yet

  @javascript @critical
  Scenario: Having selected a default gravity
    Given the default gravity is set to "g_center"
     When I go to the cloudinary configuration
     Then the default gravity should be set to "Center"