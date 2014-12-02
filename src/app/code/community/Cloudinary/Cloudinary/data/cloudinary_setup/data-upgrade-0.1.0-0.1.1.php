<?php

Mage::getModel('cloudinary_cloudinary/migration')
    ->setStarted(0)
    ->save();

$config = new Mage_Core_Model_Config();
$config->saveConfig('cloudinary/cloud/cloudinary_enabled', '0', 'default', 0);
