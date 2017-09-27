<?php

namespace EndToEnd;

use Mage;
use MageTest\Manager\Attributes\Provider\YamlProvider;
use MageTest\Manager\FixtureManager;

trait Fixture
{
    protected abstract function setFixtureManager(FixtureManager $fixtureManager);
    protected abstract function getFixtureManager();
    protected abstract function getMagentoFacade();

    /**
     * @BeforeScenario
     */
    public function beforeScenario()
    {
        $this->setFixtureManager(new FixtureManager(new YamlProvider()));
    }

    /**
     * @AfterScenario
     */
    public function afterScenario()
    {
        $this->getFixtureManager()->clear();
    }

    /**
     * @Given the product :arg1 exists
     */
    public function theProductExists($sku, $retry = true)
    {
        try {
            $this->getFixtureManager()->loadFixture('catalog/product', __DIR__ . '/../Fixtures/' . $sku . '.yaml');
        }

        catch (\Zend_Db_Statement_Exception $e) {
            if (!$retry) {
                throw $e;
            }
            $product = Mage::getModel('catalog/product');
            $product->load($product->getIdBySku($sku));
            $product->delete();
            $this->theProductExists($sku, false);
        }

        if (getenv('BEHAT_DEBUG')) {
            echo 'Fixture product = ', $this->getMagentoFacade()->productWithSku($sku)->getId();
        }
    }

    protected function getFixtureFilePath($filename)
    {
        $path = realpath(__DIR__ . '/../Fixtures/' . $filename);

        if (!file_exists($path)) {
            throw new Exception('Fixture file not found: ' . $path);
        }

        return $path;
    }
}
