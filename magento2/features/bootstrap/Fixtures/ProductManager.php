<?php

namespace Fixtures;

use Bex\Behat\Magento2InitExtension\Fixtures\BaseFixture;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

class ProductManager extends BaseFixture
{
    const TEST_PRODUCT_ID = 40;

    public function createProduct()
    {
        // TODO create new test product
        $testproduct = $this->createMagentoObject(ProductInterface::class);
        return $testproduct->load(self::TEST_PRODUCT_ID);
    }
}
