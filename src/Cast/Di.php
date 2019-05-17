<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace BuzzingPixel\Cast\Cast;

use DI\ContainerBuilder;
use EE_Config;
use EE_Lang;
use EE_Loader;
use EllisLab\ExpressionEngine\Service\Validation\Factory as EEValidationFactory;
use Psr\Container\ContainerInterface;

class Di
{
    /** @var ContainerInterface $diContainer */
    private static $diContainer;

    public static function diContainer() : ContainerInterface
    {
        if (! self::$diContainer) {
            self::build();
        }

        return self::$diContainer;
    }

    public static function build() : void
    {
        $builder = new ContainerBuilder();

        $builder->useAutowiring(true);

        $builder->useAnnotations(true);

        $builder->ignorePhpDocErrors(true);

        $builder->addDefinitions(self::definitions());

        self::$diContainer = $builder->build();
    }

    /**
     * @return mixed[]
     */
    private static function definitions() : array
    {
        return [
            EE_Config::class => static function () {
                return ee()->config;
            },
            EE_Lang::class => static function () {
                return ee()->lang;
            },
            EE_Loader::class => static function () {
                return ee()->load;
            },
            EEValidationFactory::class => static function () {
                return ee('Validation');
            },
        ];
    }
}
