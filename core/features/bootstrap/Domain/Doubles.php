<?php
/**
 * Created by PhpStorm.
 * User: danielk
 * Date: 29/01/16
 * Time: 13:28
 */

namespace Domain;


use CloudinaryExtension\ConfigurationInterface;
use CloudinaryExtension\Image\SynchronizationChecker;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophet;

class Doubles
{
    /**
     * @var ObjectProphecy
     */
    private static $configurationProphecy;

    private static $synchronizationCheckerProphecy;

    private static $setupDone = false;

    public static function setup()
    {
        if (!self::$setupDone) {
            self::$configurationProphecy = (new Prophet())->prophesize(ConfigurationInterface::class);
            self::$synchronizationCheckerProphecy = (new Prophet())->prophesize(SynchronizationChecker::class);
            self::$configurationProphecy->getFormatsToPreserve()->willReturn([]);
            self::$setupDone = true;
        }
    }

    public static function getConfiguration()
    {
        self::setup();
        return self::$configurationProphecy->reveal();
    }

    public static function getSynchronizationChecker()
    {
        self::setup();
        return self::$synchronizationCheckerProphecy->reveal();
    }

    public static function getConfigurationProphecy()
    {
        self::setup();
        return self::$configurationProphecy;
    }

    public static function getSynchronizationCheckerProphecy()
    {
        self::setup();
        return self::$synchronizationCheckerProphecy;
    }


}