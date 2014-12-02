<?php

Mage::getModel('cloudinary_cloudinary/extension')
    ->setEnabled(0)
    ->setMigrationTriggered(0)
    ->save();