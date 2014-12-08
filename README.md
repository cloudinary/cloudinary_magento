# Cloudinary Extension

Cloudinary Magento extension.

# Enabling/Disabling
The enable/disable button in the cloudinary admin section determines whether the application will request its media from the remote filesystem (Cloudinary) or from the local server. Since the media will end up being store both locally and remotely, the extension can be enabled/disabled without a major impact on the systems operation. It can be done during/before/after migration.

For example, when extension is *enabled*: 
- If the image has already been uploaded to Cloudinary, the system will fetch the image from Cloudinary.
- If the has not yet been uploaded, the system will fetch it locally

When extension is *disabled*
- The system will always fetch the image locally, regardless of whether it has been uploaded to Cloudinary or no.

# Known Issues
When the migration is started, all existing media will gradually be uploaded to cloudinary. If the extension cannot upload an image (e.g. its missing, corrupted or is rejected by the remote service) it will *not* mark the image as having been migrated (syncrhonized) and will log an error message to system.log, the said image will thus not be removed from the queue of images to migrate and the migration will never complete. It is up to the *Integrator* to be aware of images that could not be uploaded and to decide if they should be deleted from the local database. The migration will only be marked as completed when all of the images in the media gallery have been successfully uploaded.
