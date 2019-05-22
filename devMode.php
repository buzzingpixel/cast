<?php

declare(strict_types=1);

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\ContextProvider\CliContextProvider;
use Symfony\Component\VarDumper\Dumper\ContextProvider\SourceContextProvider;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\Dumper\ServerDumper;
use Symfony\Component\VarDumper\VarDumper;

if (class_exists(Symfony\Component\VarDumper\VarDumper::class)) {
    $cloner = new VarCloner();

    $htmlDumper = new HtmlDumper();

    /**
     * Can also be set to dark. Search for "theme" on this page to learn more
     *
     * @see https://symfony.com/doc/current/components/var_dumper/advanced.html
     */
    $htmlDumper->setTheme('light');

    $fallbackDumper = in_array(PHP_SAPI, ['cli', 'phpdbg']) ? new CliDumper() : $htmlDumper;

    $dumper = new ServerDumper('tcp://127.0.0.1:9912', $fallbackDumper, [
        'cli' => new CliContextProvider(),
        'source' => new SourceContextProvider(),
    ]);

    $varStore            = new stdClass();
    $varStore->hasDumped = false;

    VarDumper::setHandler(static function ($var) use ($cloner, $dumper, $varStore) : void {
        if (PHP_SAPI !== 'cli' && ! $varStore->hasDumped) {
            print '<head><title>Symfony Dumper</title></head><body>';
            $varStore->hasDumped = true;
        }

        $traceItem = debug_backtrace()[2];

        if (PHP_SAPI !== 'cli') {
            print '<pre style="margin-bottom: -12px;">';
        }

        print $traceItem['file'] . ':' . $traceItem['line'];

        if (PHP_SAPI === 'cli') {
            print ': ';
        }

        if (PHP_SAPI !== 'cli') {
            print '</pre>';
        }

        $dumper->dump($cloner->cloneVar($var));
    });
}
