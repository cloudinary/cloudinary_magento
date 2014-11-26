Feature: Product image migration
  In order to easily install and use the Cloudinary module
  As an integrator
  I need an easy mechanism to migrate all existing catalogue images to Cloudinary

  Scenario: Integrator triggers the migration
    Given the media gallery contains the images "chair.png", "table.png" and "house.png"
    And those images have not been migrated to cloudinary
    When the integrator triggers the migration
    Then the images should be migrated to cloudinary

  Scenario: Integrator enables the extension
    Given the cloudinary media gallery contains the image "lolcat.png"
    When the integrator enables the module
    Then the image should be provided by cloudinay

  Scenario: Integrator is unable to start the migration when a process is already running
    Given the cloudinary migration has been triggered
    And the cloudinary migration is still in progress
    When the integrator tries to trigger the migration
    Then they should not be able to start the migration
    And there should be feedback that triggering a migration is currently disabled

  Scenario: Integrator is unable to start the migration when there are no images to migrate
    Given there are no images to migrate
    When the integrator tries to trigger the migration
    Then they should not be able to start the migration
    And there should be feedback that triggering a migration is currently disabled

  Scenario: Integrator receives feedback of the migration progress
    Given the media gallery contains the images "chair.png", "table.png" and "house.png"
    When a migration is triggered
    And the images "chair.png" and "table.png" have been migrated
    But the image "house.png" haven not been migrated yet
    Then the integrator should receive feedback saying that the migration is at "66%"
