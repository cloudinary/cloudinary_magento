Feature: Image migration process
  As an Administrator
  So that I do not need to upload each product image individually
  I need the process of migration to be automated

  Background:
    Given the Cloudinary module is enabled

  Scenario: Seeing an enabled Migrate button when there are images to migrate
    Given the following catalog images exist:
      | Image     | Migrated |
      | chair.jpg | true     |
      | table.jpg | false    |
      | house.jpg | false    |
     When I press the Migrate button
     Then the following images should be migrated to Cloudinary:
      | chair.jpg |
      | table.jpg |
      And all images should be flagged as migrated

  Scenario: Seeing a disabled Migrate button when there are no images to migrate
    Given there is no migration in progress
      And the following catalog images exist:
      | Image     | Migrated |
      | chair.jpg | true     |
      | table.jpg | true     |
      | house.jpg | true     |
     When I load the Cloudinary admin page
     Then the Migrate button should be disabled

  Scenario: Seeing a disabled Migrate button when migration is in progress
    Given there is a migration in progress
     When I load the Cloudinary admin page
     Then the Migrate button should be disabled

  Scenario: Seeing an enabled Migrate button when there is no migration in progress and there are unmigrated images
    Given there is a migration in progress
      And there are images that have not been migrated yet
     When I load the Cloudinary admin page
     Then the Migrate button should be enabled

  Scenario: Seeing the current progress of the migration process
    Given the following catalog images exist:
      | Image     | Migrated |
      | chair.jpg | true     |
      | table.jpg | true     |
      | house.jpg | false    |
     And the migration is in progress
    When I load the Cloudinary admin page
    Then I should see a progress message saying "66% complete"
