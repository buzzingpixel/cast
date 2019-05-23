<?php

declare(strict_types=1);

namespace Tests\ExpressionEngine;

use BuzzingPixel\Cast\Cast\Uploading\UploadFileToCacheLocation;
use Cast;
use EE_Input;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Throwable;

class CastModTest extends TestCase
{
    /** @var MockObject&EE_Input */
    private $eeInput;
    /** @var MockObject&UploadFileToCacheLocation */
    private $uploadFileToCacehLocation;
    /** @var Cast */
    private $mod;

    /**
     * @throws Throwable
     */
    protected function setUp() : void
    {
        $this->eeInput = $this->createMock(EE_Input::class);

        $this->uploadFileToCacehLocation = $this->createMock(UploadFileToCacheLocation::class);

        $this->mod = new Cast(
            0,
            $this->eeInput,
            $this->uploadFileToCacehLocation
        );
    }

    public function testUploadAudioFileWithFilesAsArray() : void
    {
        $file = [
            'foo' => 'bar',
            'baz' => 'foo',
        ];

        $_FILES['file'] = $file;

        $this->eeInput->expects(self::once())
            ->method('post')
            ->with(self::equalTo('upload_key'))
            ->willReturn('fooBarUploadKey');

        $this->uploadFileToCacehLocation->expects(self::once())
            ->method('upload')
            ->with(
                self::equalTo('fooBarUploadKey'),
                self::equalTo($file)
            );

        $this->mod->uploadAudioFile();

        $_FILES = [];
    }

    public function testUploadAudioFileWithFilesMissing() : void
    {
        $_FILES = [];

        $this->eeInput->expects(self::once())
            ->method('post')
            ->with(self::equalTo('upload_key'))
            ->willReturn('fooBarUploadKey');

        $this->uploadFileToCacehLocation->expects(self::once())
            ->method('upload')
            ->with(
                self::equalTo('fooBarUploadKey'),
                self::equalTo([])
            );

        $this->mod->uploadAudioFile();
    }
}
