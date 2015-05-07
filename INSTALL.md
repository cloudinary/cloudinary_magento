# INSTALLATION

## Composer

To install the Cloudinary extenstion via composer you'll need to specify the release you want to install, and the path for the extension repository. You'll also need to specify the path for the `magento-composer-installer` composer plugin.

The following example of what to add to a `composer.json` file, in order to install via composer, assumes that Magento resides inside a folder named `public/` inside your codebase:

```JSON
{
    "require": {
        "cloudinary/cloudinary_magento": "dev-master"
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:cloudinary/cloudinary_magento.git"
        },
        {
            "type": "vcs",
            "url": "https://github.com/magento-hackathon/magento-composer-installer"
        }
    ],
    "extra":{
        "magento-root-dir": "./public",
        "magento-deploystrategy": "copy"
    },
    "autoload": {
        "psr-0": {
            "": [
               "public/app/code/local",
                "public/app/code/community",
                "public/app/code/core",
                "public/app",
                "public/lib"
            ],
            "Mage" : "public/app/code/core"
        }
    }
}
```

Although the `master` branch should always be stable, for compatibility reasons you probably want to change `dev-master` to a specific release, making sure the extension will only be upgraded to a newer version when explicitly changed in the `composer.json` file.
At the time of writing (December 2014) the current version is `0.1.1` and this should be used in place of `dev-master`.
