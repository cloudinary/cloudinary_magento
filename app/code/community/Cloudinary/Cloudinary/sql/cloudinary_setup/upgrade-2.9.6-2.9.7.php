<?php

/* @var $installer  Mage_Eav_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();

$installer->addAttribute(
    'catalog_product', 'cloudinary_data', array(
    'group'                   => 'General',
    'label'                   => 'Cloudinary Data',
    'input'                   => 'text',
    'type'                    => 'text',
    'required'                => 0,
    'visible_on_front'        => 0,
    'filterable'              => 0,
    'searchable'              => 0,
    'comparable'              => 0,
    'user_defined'            => 0,
    'is_configurable'         => 0,
    'used_in_product_listing' => '1',
    'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'note'                    => '',
    )
);

$installer->endSetup();
