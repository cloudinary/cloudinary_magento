# Magento 1 [Cloudinary](https://www.cloudinary.com/) Module

Magento 1 module for integration with Cloudinary.

---

## Install From Magento Admin (Using Magento Connect Manager)
1. Log into your Magento admin panel & navigate to System > Magento Connect > Magento Connect Manager.
2. Under "Direct package file upload", upload the package file that's included in this repository (var/connect/Cloudinary_Cloudinary-\*.tgz).
3. Install the package...

## Install Manually
1. Download & copy/drag the directories `app`, `js`, `lib` & `skin` into your Magento 1 root dir.
2. Clear cache from Magento admin panel or by running `rm -rf var/cache/* var/full_page_cache/*` under your Magento 1 root dir.

## Install Using Modman
1. If you don't have modman installed, install it first according to the [official instructions](https://github.com/colinmollenhour/modman#installation) & then follow the [instructions for Magento users](https://github.com/colinmollenhour/modman#magento-users).
2. cd into your Magento 1 root dir & run `modman init` (in case you haven't done it already).
3. Under your Magento 1 root dir, run `modman clone Cloudinary_Cloudinary https://github.com/cloudinary/cloudinary_magento`
4. Clear cache from Magento admin panel or by running `rm -rf var/cache/* var/full_page_cache/*` under your Magento 1 root dir.

## Update Using Modman
1. Under your Magento 1 root dir, run `modman update Cloudinary_Cloudinary`
2. Clear cache from Magento admin panel or by running `rm -rf var/cache/* var/full_page_cache/*` under your Magento 1 root dir.

---

https://www.cloudinary.com/

Copyright © 2018 Cloudinary. All rights reserved.  

![Cloudinary Logo](https://cloudinary-res.cloudinary.com/image/upload/c_scale,w_300/v1/logo/for_white_bg/cloudinary_logo_for_white_bg.svg)
