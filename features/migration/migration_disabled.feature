Feature: Migration is disabled when module is disabled

  Scenario: Seeing a disabled Migrate button when the Cloudinary module is disabled
    Given the Cloudinary module is disabled
     When I load the Cloudinary admin page
     Then the Migrate button should be disabled

  Scenario: Seeing an enabled Migrate button when the Cloudinary module is enabled
    Given the Cloudinary module is enabled
     When I load the Cloudinary admin page
      And there is no migration in progress
      And there are images that have not yet been migrated
     Then the Migrate button should be enabled