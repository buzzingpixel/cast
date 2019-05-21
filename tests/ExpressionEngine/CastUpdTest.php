<?php

declare(strict_types=1);

namespace Tests\ExpressionEngine;

use BuzzingPixel\Cast\ExpressionEngine\Migration\M000001AddModule;
use BuzzingPixel\Cast\ExpressionEngine\Migration\M000002AddUploadAudioFileAction;
use BuzzingPixel\Cast\ExpressionEngine\Migration\M000003AddCastAudioUploadKeysTable;
use BuzzingPixel\Cast\ExpressionEngine\Migration\MFinal;
use Cast_upd;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Throwable;

class CastUpdTest extends TestCase
{
    /** @var MockObject&ContainerInterface */
    private $di;

    /** @var Cast_upd */
    private $upd;

    /**
     * @throws Throwable
     */
    protected function setUp() : void
    {
        $this->di = $this->createMock(ContainerInterface::class);

        $this->upd = new Cast_upd($this->di);
    }

    /**
     * @throws Throwable
     */
    public function testInstall() : void
    {
        $m1 = $this->createMock(M000001AddModule::class);

        $m1->expects(self::once())
            ->method('safeUp')
            ->willReturn(true);

        $m1->expects(self::never())
            ->method('safeDown');

        $this->di->expects(self::at(0))
            ->method('get')
            ->with(self::equalTo(M000001AddModule::class))
            ->willReturn($m1);

        $m2 = $this->createMock(M000002AddUploadAudioFileAction::class);

        $m2->expects(self::once())
            ->method('safeUp')
            ->willReturn(true);

        $m2->expects(self::never())
            ->method('safeDown');

        $this->di->expects(self::at(1))
            ->method('get')
            ->with(self::equalTo(M000002AddUploadAudioFileAction::class))
            ->willReturn($m2);

        $m3 = $this->createMock(M000003AddCastAudioUploadKeysTable::class);

        $m3->expects(self::once())
            ->method('safeUp')
            ->willReturn(true);

        $m3->expects(self::never())
            ->method('safeDown');

        $this->di->expects(self::at(2))
            ->method('get')
            ->with(self::equalTo(M000003AddCastAudioUploadKeysTable::class))
            ->willReturn($m3);

        $mFinal = $this->createMock(MFinal::class);

        $mFinal->expects(self::once())
            ->method('safeUp')
            ->willReturn(true);

        $mFinal->expects(self::never())
            ->method('safeDown');

        $this->di->expects(self::at(3))
            ->method('get')
            ->with(self::equalTo(MFinal::class))
            ->willReturn($mFinal);

        self::assertTrue($this->upd->install());
    }

    /**
     * @throws Throwable
     */
    public function testUninstall() : void
    {
        $m1 = $this->createMock(M000001AddModule::class);

        $m1->expects(self::never())
            ->method('safeUp');

        $m1->expects(self::once())
            ->method('safeDown')
            ->willReturn(true);

        $this->di->expects(self::at(3))
            ->method('get')
            ->with(self::equalTo(M000001AddModule::class))
            ->willReturn($m1);

        $m2 = $this->createMock(M000002AddUploadAudioFileAction::class);

        $m2->expects(self::never())
            ->method('safeUp');

        $m2->expects(self::once())
            ->method('safeDown')
            ->willReturn(true);

        $this->di->expects(self::at(2))
            ->method('get')
            ->with(self::equalTo(M000002AddUploadAudioFileAction::class))
            ->willReturn($m2);

        $m3 = $this->createMock(M000003AddCastAudioUploadKeysTable::class);

        $m3->expects(self::never())
            ->method('safeUp')
            ->willReturn(true);

        $m3->expects(self::once())
            ->method('safeDown')
            ->willReturn(true);

        $this->di->expects(self::at(1))
            ->method('get')
            ->with(self::equalTo(M000003AddCastAudioUploadKeysTable::class))
            ->willReturn($m3);

        $mFinal = $this->createMock(MFinal::class);

        $mFinal->expects(self::never())
            ->method('safeUp');

        $mFinal->expects(self::once())
            ->method('safeDown')
            ->willReturn(true);

        $this->di->expects(self::at(0))
            ->method('get')
            ->with(self::equalTo(MFinal::class))
            ->willReturn($mFinal);

        self::assertTrue($this->upd->uninstall());
    }

    /**
     * @throws Throwable
     */
    public function testUpdate() : void
    {
        $m1 = $this->createMock(M000001AddModule::class);

        $m1->expects(self::once())
            ->method('safeUp')
            ->willReturn(true);

        $m1->expects(self::never())
            ->method('safeDown');

        $this->di->expects(self::at(0))
            ->method('get')
            ->with(self::equalTo(M000001AddModule::class))
            ->willReturn($m1);

        $m2 = $this->createMock(M000002AddUploadAudioFileAction::class);

        $m2->expects(self::once())
            ->method('safeUp')
            ->willReturn(true);

        $m2->expects(self::never())
            ->method('safeDown');

        $this->di->expects(self::at(1))
            ->method('get')
            ->with(self::equalTo(M000002AddUploadAudioFileAction::class))
            ->willReturn($m2);

        $m3 = $this->createMock(M000003AddCastAudioUploadKeysTable::class);

        $m3->expects(self::once())
            ->method('safeUp')
            ->willReturn(true);

        $m3->expects(self::never())
            ->method('safeDown');

        $this->di->expects(self::at(2))
            ->method('get')
            ->with(self::equalTo(M000003AddCastAudioUploadKeysTable::class))
            ->willReturn($m3);

        $mFinal = $this->createMock(MFinal::class);

        $mFinal->expects(self::once())
            ->method('safeUp')
            ->willReturn(true);

        $mFinal->expects(self::never())
            ->method('safeDown');

        $this->di->expects(self::at(3))
            ->method('get')
            ->with(self::equalTo(MFinal::class))
            ->willReturn($mFinal);

        self::assertTrue($this->upd->update());
    }
}
