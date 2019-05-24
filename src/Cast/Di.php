<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace BuzzingPixel\Cast\Cast;

use BuzzingPixel\Cast\Cast\Language\EnglishTranslations;
use BuzzingPixel\Cast\Cast\Language\Translator;
use BuzzingPixel\Cast\Cast\Uploading\CacheLocationServiceContract;
use BuzzingPixel\Cast\Cast\Uploading\UploadKeysServiceContract;
use BuzzingPixel\Cast\ExpressionEngine\Service\CacheLocationService;
use BuzzingPixel\Cast\ExpressionEngine\Uploading\UploadKeysService;
use CI_DB_forge;
use Cp;
use DI\ContainerBuilder;
use EE_Config;
use EE_Input;
use EE_Lang;
use EE_Loader;
use EE_Template;
use EE_URI;
use EllisLab\ExpressionEngine\Service\Model\Facade as ModelFacade;
use EllisLab\ExpressionEngine\Service\Validation\Factory as EEValidationFactory;
use Psr\Container\ContainerInterface;
use Zend\HttpHandlerRunner\Emitter\EmitterStack;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

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
        // @codeCoverageIgnoreStart
        return [
            CacheLocationServiceContract::class => static function (ContainerInterface $di) {
                // TODO: determine what system this is (Craft/EE), then determine lang, then get appropriate translation
                return $di->get(CacheLocationService::class);
            },
            CI_DB_forge::class => static function () {
                ee()->load->dbforge();

                return ee()->dbforge;
            },
            Cp::class => static function () {
                if (! isset(ee()->cp)) {
                    return null;
                }

                return ee()->cp;
            },
            EE_Config::class => static function () {
                return ee()->config;
            },
            EE_Input::class => static function () {
                return ee()->input;
            },
            EE_Lang::class => static function () {
                return ee()->lang;
            },
            EE_Loader::class => static function () {
                return ee()->load;
            },
            EE_Template::class => static function () {
                ee()->load->library('template', null, 'TMPL');

                return ee()->TMPL;
            },
            EE_URI::class => static function () {
                return ee()->uri;
            },
            EmitterStack::class => static function (ContainerInterface $di) {
                $stack = new EmitterStack();

                $stack->push($di->get(SapiEmitter::class));

                return $stack;
            },
            EEValidationFactory::class => static function () {
                return ee('Validation');
            },
            ModelFacade::class => static function () {
                return ee('Model');
            },
            Translator::class => static function () {
                // TODO: determine what system this is (Craft/EE), then determine lang, then get appropriate translation
                return new Translator(new EnglishTranslations());
            },
            UploadKeysServiceContract::class => static function (ContainerInterface $di) {
                // TODO: determine what the system is (Craft/EE), then determine which service is appropriate
                return $di->get(UploadKeysService::class);
            },
        ];

        // @codeCoverageIgnoreEnd
    }
}
