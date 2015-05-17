# Cloudinary Magento extension

## Installation instructions
The Cloudinary module can be installed 
- using this github project, 
or 
- direct from the [Magento Connect Store](http://bit.ly/cloudmagento)

## Functionality Overview

The extension takes over the basic Magento image functionality and provides it via Cloudinary. This allows for a rapid image delivery to users as well as a vastly reduced page size. Improvements of up to 30% in speed on Magento's image heavy pages have been benchmarked. 


### Image Upload

From an admin perspective, Image upload is in no way different from the standard Magento image upload. The only change is that when the extension is enabled, images are uploaded to Cloudinary.

### Image Display

When the extension is enabled and the image is available in Cloudinary, the images served from the Cloudinary network rather than from the local infrastructure. From the user's perspective, there's no difference in behaviour other than the potential performance gains.


### Full Quality Control of Images 
Using a drop down, its possible to optimize the quality of the images across the website. Dropping quality to 60% on some sites can increase the speed of the site by up to 30% without the user being able to notice a change in quality. 

### Image domain sharding support 
Removes the browser bottleneck limit of 4 images  by serving the images from multiple domains. 

### Automatic Caching of images
The plugin will automatically cache and serve images from the closest server to a visitor using one of the top CDNs ( Akamai)

### Fully Robust backup 
All images are stored locally as well as on Cloudinary automatically to make sure users always get the right image whether the plugin is off or on. In addition all images are saved and can be located easily using search. 

## What is Cloudinary

Cloudinary is a cloud service that offers a solution to a web application's entire image management pipeline. 

Easily upload images to the cloud. Automatically perform smart image resizing, cropping and conversion without installing any complex software. Integrate Facebook or Twitter profile image extraction in a snap, in any dimension and style to match your website’s graphics requirements. Images are seamlessly delivered through a fast CDN, and much much more. 

Cloudinary offers comprehensive APIs and administration capabilities and is easy to integrate with any web application, existing or new.

Cloudinary provides URL and HTTP based APIs that can be easily integrated with any Web development framework. 


## Important: Changing cloud names

Currently the extension doesn't cope well with changing clouds when the extension is in use because of the image synchronisation between Magento and Cloudinary.
Changing clouds will cause Magento to be unaware that the images that were already synchronised are now not available in the newly specified cloud. If the records for the synchronisation are removed, then it is possible to run the migration process for the new cloud, but if the configuration ever reverts to the previous cloud, there will be no record of the previously synchronised images, and, in addition to having to reset the synchronisation records to be able to run the migration again, the images will be re-uploaded and exist duplicated on Cloudinary.



### Credentials and cloud configuration

The Plugin uses the 'Cloudinary Environment Variable' configuration which are all available under the `System->Configuration` menu option, in the `Services` group under the name `Cloudinary`.

### Image Migration ( Via Cron)

The Cloudinary extension provides functionality to automatically trigger the upload of pre-existing images to Cloudinary. This process is throttled to prevent network flooding, and is controlled manually to allow for store admins to choose when it should happen.

*Please note* that this process currently requires your site to use Magento's Cron setup.  ( [Magento's documentation on how to set up Cron](http://www.magentocommerce.com/wiki/1_-_installation_and_configuration/how_to_setup_a_cron_job) )

To start the migration process go to the `Cloudinary->Manage` menu in Magento's admin panel, and press the `Start Migration` button, note that if there are no images to migrate, the button will be greyed out. Pressing the `Start Migration` button (when it's not greyed out), will trigger the migration process and show the migration progress. When the migration finished, the `Start Migration` button will become greyed out.

It's possible to pause the migration process by pressing the `Stop Migration` button. This will allow you to continue the migration process later.
Images become available via Cloudinary as soon as they've been uploaded, so stopping the migration process still allows the site to benefit from Cloudinary for the images that were already uploaded.

### Enabling/Disabling the extension

The extension can be enabled and disabled at will. To disable the extension, go to the `Cloudinary->Manage` menu, and press the `Enable Cloudinary` \ `Disable Cloudinary` button. Keep in mind that when the extension is disabled, no images will be served from Cloudinary nor will new images be uploaded to Cloudinary.
If the extension is disabled at any point, it's advisable to start the migration process after enabling it. The `Start Migration` will be greyed out, if there are no images to migrate.


## Enabling/Disabling
The enable/disable button in the Cloudinary admin section determines whether the application will request its media from the remote filesystem (Cloudinary) or from the local server. Since the media will end up being store both locally and remotely, the extension can be enabled/disabled without a major impact on the systems operation. It can be done during/before/after migration.

For example, when extension is *enabled*: 
- If the image has already been uploaded to Cloudinary, the system will fetch the image from Cloudinary.
- If the has not yet been uploaded, the system will fetch it locally.

When extension is *disabled*
- The system will always fetch the image locally, regardless of whether it has been uploaded to Cloudinary or no.

## Known Issues

- When the migration is started, all existing media will gradually be uploaded to Cloudinary. If the extension cannot upload an image (e.g. its missing, corrupted or is rejected by the remote service) it will *not* mark the image as having been migrated (synchronized) and will log an error message to system.log, the said image will thus not be removed from the queue of images to migrate and the migration will never complete. It is up to the *Integrator* to be aware of images that could not be uploaded and to decide if they should be deleted from the local database. The migration will only be marked as completed when all of the images in the media gallery have been successfully uploaded.

## Running Gherkin Features
- Start phantomjs on the VM:

        phantomjs --webdriver 4444 --load-images=no &
- Make sure you are in the cloudinary directory:

        cd /vagrant/vendor/inviqa/cloudinary
- Run Behat

        bin/behat -fprogress
        

## Additional resources ##########################################################

Additional resources are available at:

* [Website](http://cloudinary.com)
* [ Cloudinary Module on the Magento Connect Store](http://www.magentocommerce.com/magento-connect/cloudinary-image-management-in-the-cloud.html)
* [Documentation](http://cloudinary.com/documentation)
* [Knowledge Base](http://support.cloudinary.com/forums)
* [Image transformations documentation](http://cloudinary.com/documentation/image_transformations)

## Support

You can [open an issue through GitHub](https://github.com/cloudinary/cloudinary_magento/issues).

Contact us [http://cloudinary.com/contact](http://cloudinary.com/contact)

Stay tuned for updates, tips and tutorials: [Blog](http://cloudinary.com/blog), [Twitter](https://twitter.com/cloudinary), [Facebook](http://www.facebook.com/Cloudinary).


## License #######################################################################

Released under the MIT license. 
