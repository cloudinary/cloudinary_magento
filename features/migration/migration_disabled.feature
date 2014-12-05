Feature: Migration is disabled when module is disabled

  Scenario: Cloudinary module is disabled
    Given the Cloudinary module is disabled
    When I access the Cloudinary configuration
    Then I should not be able to trigger the migration of images

  Scenario: Cloudinary module is enabled no migration in progress
    Given the Cloudinary module is enabled
    When I access the Cloudinary configuration
    And there is no migration in progress
    And there are images that have not yet been migrated
    Then I should be able to trigger the migration of images
    
  Scenario: Cloudinary module is enabled and migration is in progress
    Given the Cloudinary module is enabled
    When I access the Cloudinary configuration
    And there is a migration in progress
    And there are images that have not yet been migrated
    Then I should not be able to trigger the migration of images
