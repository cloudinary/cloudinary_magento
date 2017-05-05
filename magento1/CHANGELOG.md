
1.2.1 / 2016-08-06
==================

  * Refactor is syncable check in cms uploader
  * Fix coding standard issue
  * Remove needless debug code and fix specs for the uploader
  * Add validation to cloudinary url config value before creating configuration object
  * Update composer.json so behat would run
  * Optimize synced image check performance
  * Add type check to image upload
  * Merge pull request #1 from grantkemp/feature/copy_changes

1.2.0 / 2016-02-29
==================

  * Use URL model to retrieve Cloudinary configuration URL
  * amend class member variable name to comply with naming standards
  * gitignore Cloudinary library symlink
  * Remove PHP 5.5 specific class determination in BatchUploader
  * Remove reference to unimplemented exception class
  * Remove TODO comment from cron model
  * set folder option for uploading product files when foldered migration is enabled
  * fix wrong cron setting
