<?php

/* @var $installer  Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->run("DELETE FROM core_config_data WHERE path LIKE 'cloudinary/%' AND scope != 'default';");

$installer->endSetup();
