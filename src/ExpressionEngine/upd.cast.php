<?php

declare(strict_types=1);

use BuzzingPixel\Cast\Cast\Constants;
use BuzzingPixel\Cast\Cast\Di;
use BuzzingPixel\Cast\ExpressionEngine\Migration\M000001AddModule;
use BuzzingPixel\Cast\ExpressionEngine\Migration\M000002AddUploadAudioFileAction;
use BuzzingPixel\Cast\ExpressionEngine\Migration\M000003AddCastAudioUploadKeysTable;
use BuzzingPixel\Cast\ExpressionEngine\Migration\MFinal;
use Psr\Container\ContainerInterface;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps

class Cast_upd
{
    /** @var string */
    public $version = Constants::VERSION;

    /** @var ContainerInterface */
    private $di;

    public function __construct(?ContainerInterface $di = null)
    {
        // @codeCoverageIgnoreStart
        if (! $di) {
            $di = Di::diContainer();
        }
        // @codeCoverageIgnoreEnd

        $this->di = $di;
    }

    public function install() : bool
    {
        $this->di->get(M000001AddModule::class)->safeUp();
        $this->di->get(M000002AddUploadAudioFileAction::class)->safeUp();
        $this->di->get(M000003AddCastAudioUploadKeysTable::class)->safeUp();
        $this->di->get(MFinal::class)->safeUp();

        return true;
    }

    public function uninstall() : bool
    {
        $this->di->get(MFinal::class)->safeDown();
        $this->di->get(M000003AddCastAudioUploadKeysTable::class)->safeDown();
        $this->di->get(M000002AddUploadAudioFileAction::class)->safeDown();
        $this->di->get(M000001AddModule::class)->safeDown();

        return true;
    }

    public function update() : bool
    {
        return $this->install();
    }
}
