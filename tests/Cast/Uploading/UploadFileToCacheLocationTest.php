<?php

declare(strict_types=1);

namespace Tests\Cast\Uploading;

use BuzzingPixel\Cast\Cast\Constants;
use BuzzingPixel\Cast\Cast\Facade\PhpInternals;
use BuzzingPixel\Cast\Cast\Uploading\CacheLocationServiceContract;
use BuzzingPixel\Cast\Cast\Uploading\UploadFileToCacheLocation;
use BuzzingPixel\Cast\Cast\Uploading\UploadKeysServiceContract;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;
use Zend\Diactoros\ResponseFactory;
use Zend\HttpHandlerRunner\Emitter\EmitterStack;

class UploadFileToCacheLocationTest extends TestCase
{
    /** @var MockObject&UploadKeysServiceContract */
    private $uploadKeysService;
    /** @var MockObject&EmitterStack */
    private $emitterStack;
    /** @var MockObject&CacheLocationServiceContract */
    private $cacheLocationService;
    /** @var MockObject&Filesystem */
    private $fileSystem;
    /** @var MockObject&PhpInternals */
    private $phpInternals;
    /** @var UploadFileToCacheLocation */
    private $service;

    /** @var ResponseInterface|null */
    private $testResponse = null;

    /**
     * @throws Throwable
     */
    protected function setUp() : void
    {
        $this->uploadKeysService = $this->createMock(UploadKeysServiceContract::class);

        $this->emitterStack = $this->createMock(EmitterStack::class);

        $this->cacheLocationService = $this->createMock(CacheLocationServiceContract::class);

        $this->fileSystem = $this->createMock(Filesystem::class);

        $this->phpInternals = $this->createMock(PhpInternals::class);

        $this->service = new UploadFileToCacheLocation(
            $this->uploadKeysService,
            $this->emitterStack,
            new ResponseFactory(),
            $this->cacheLocationService,
            $this->fileSystem,
            $this->phpInternals
        );

        $this->testResponse = null;
    }

    public function testUploadInvalidUploadKey() : void
    {
        $self = $this;

        $key = 'fooBarTestUploadKey';

        $this->cacheLocationService->expects(self::never())->method(self::anything());

        $this->fileSystem->expects(self::never())->method(self::anything());

        $this->phpInternals->expects(self::once())->method('phpExit');

        $this->uploadKeysService->expects(self::once())
            ->method('consumeKey')
            ->with(self::equalTo($key))
            ->willReturn(false);

        $this->emitterStack->expects(self::once())
            ->method('emit')
            ->willReturnCallback(static function (ResponseInterface $response) use ($self) {
                $self->testResponse = $response;

                return true;
            });

        // Invoke service method
        $this->service->upload($key, []);

        self::assertInstanceOf(ResponseInterface::class, $this->testResponse);

        self::assertSame(400, $this->testResponse->getStatusCode());

        $headers = $this->testResponse->getHeaders();

        self::assertCount(1, $headers);

        self::assertCount(1, $headers['Content-Type']);

        self::assertSame('application/json', $headers['Content-Type'][0]);

        self::assertSame(
            '{"success":false,"message":"Invalid upload key","newUploadKey":"","file":[]}',
            (string) $this->testResponse->getBody()
        );
    }

    public function testUploadNoFileName() : void
    {
        $self = $this;

        $key = 'fooBarTestUploadKeyAgain';

        $file = ['tmp_name' => 'fooBar'];

        $this->cacheLocationService->expects(self::never())->method(self::anything());

        $this->fileSystem->expects(self::never())->method(self::anything());

        $this->phpInternals->expects(self::once())->method('phpExit');

        $this->uploadKeysService->expects(self::once())
            ->method('consumeKey')
            ->with(self::equalTo($key))
            ->willReturn(true);

        $this->emitterStack->expects(self::once())
            ->method('emit')
            ->willReturnCallback(static function (ResponseInterface $response) use ($self) {
                $self->testResponse = $response;

                return true;
            });

        // Invoke service method
        $this->service->upload($key, $file);

        self::assertInstanceOf(ResponseInterface::class, $this->testResponse);

        self::assertSame(400, $this->testResponse->getStatusCode());

        $headers = $this->testResponse->getHeaders();

        self::assertCount(1, $headers);

        self::assertCount(1, $headers['Content-Type']);

        self::assertSame('application/json', $headers['Content-Type'][0]);

        self::assertSame(
            '{"success":false,"message":"The uploaded file could not be found. The most common reason for this is you tried to upload a file that is larger than your server allows.","newUploadKey":"","file":[]}',
            (string) $this->testResponse->getBody()
        );
    }

    public function testUploadNoTmpName() : void
    {
        $self = $this;

        $key = 'fooBarTestUploadKeyAgain';

        $file = ['name' => 'fooBar'];

        $this->cacheLocationService->expects(self::never())->method(self::anything());

        $this->fileSystem->expects(self::never())->method(self::anything());

        $this->phpInternals->expects(self::once())->method('phpExit');

        $this->uploadKeysService->expects(self::once())
            ->method('consumeKey')
            ->with(self::equalTo($key))
            ->willReturn(true);

        $this->emitterStack->expects(self::once())
            ->method('emit')
            ->willReturnCallback(static function (ResponseInterface $response) use ($self) {
                $self->testResponse = $response;

                return true;
            });

        // Invoke service method
        $this->service->upload($key, $file);

        self::assertInstanceOf(ResponseInterface::class, $this->testResponse);

        self::assertSame(400, $this->testResponse->getStatusCode());

        $headers = $this->testResponse->getHeaders();

        self::assertCount(1, $headers);

        self::assertCount(1, $headers['Content-Type']);

        self::assertSame('application/json', $headers['Content-Type'][0]);

        self::assertSame(
            '{"success":false,"message":"The uploaded file could not be found. The most common reason for this is you tried to upload a file that is larger than your server allows.","newUploadKey":"","file":[]}',
            (string) $this->testResponse->getBody()
        );
    }

    public function testUploadTmpNameDoesNotExist() : void
    {
        $self = $this;

        $key = 'fooBarTestUploadKeyAgain';

        $file = [
            'name' => 'fooBar.mp3',
            'tmp_name' => '/foo/fooBar.mp3',
        ];

        $this->cacheLocationService->expects(self::never())->method(self::anything());

        $this->fileSystem->expects(self::once())
            ->method('exists')
            ->with(self::equalTo($file['tmp_name']))
            ->willReturn(false);

        $this->phpInternals->expects(self::once())->method('phpExit');

        $this->uploadKeysService->expects(self::once())
            ->method('consumeKey')
            ->with(self::equalTo($key))
            ->willReturn(true);

        $this->emitterStack->expects(self::once())
            ->method('emit')
            ->willReturnCallback(static function (ResponseInterface $response) use ($self) {
                $self->testResponse = $response;

                return true;
            });

        // Invoke service method
        $this->service->upload($key, $file);

        self::assertSame(400, $this->testResponse->getStatusCode());

        self::assertInstanceOf(ResponseInterface::class, $this->testResponse);

        $headers = $this->testResponse->getHeaders();

        self::assertCount(1, $headers);

        self::assertCount(1, $headers['Content-Type']);

        self::assertSame('application/json', $headers['Content-Type'][0]);

        self::assertSame(
            '{"success":false,"message":"The uploaded file could not be found. The most common reason for this is you tried to upload a file that is larger than your server allows.","newUploadKey":"","file":[]}',
            (string) $this->testResponse->getBody()
        );
    }

    public function testUploadIncorrectMimeType() : void
    {
        $self = $this;

        $key = 'fooBarTestUploadKeyAgain';

        $file = [
            'name' => 'fooBar.mp3',
            'tmp_name' => '/foo/fooBar.mp3',
        ];

        $this->cacheLocationService->expects(self::never())->method(self::anything());

        $this->fileSystem->expects(self::once())
            ->method('exists')
            ->with(self::equalTo($file['tmp_name']))
            ->willReturn(true);

        $this->phpInternals->expects(self::once())
            ->method('mimeContentType')
            ->with(self::equalTo($file['tmp_name']))
            ->willReturn('fooBar');

        $this->phpInternals->expects(self::once())->method('phpExit');

        $this->uploadKeysService->expects(self::once())
            ->method('consumeKey')
            ->with(self::equalTo($key))
            ->willReturn(true);

        $this->emitterStack->expects(self::once())
            ->method('emit')
            ->willReturnCallback(static function (ResponseInterface $response) use ($self) {
                $self->testResponse = $response;

                return true;
            });

        // Invoke service method
        $this->service->upload($key, $file);

        self::assertInstanceOf(ResponseInterface::class, $this->testResponse);

        self::assertSame(400, $this->testResponse->getStatusCode());

        $headers = $this->testResponse->getHeaders();

        self::assertCount(1, $headers);

        self::assertCount(1, $headers['Content-Type']);

        self::assertSame('application/json', $headers['Content-Type'][0]);

        self::assertSame(
            '{"success":false,"message":"Uploaded file is not an audio file.","newUploadKey":"","file":[]}',
            (string) $this->testResponse->getBody()
        );
    }

    public function testUploadAllSystemsGo() : void
    {
        $self = $this;

        $key = 'fooBarTestUploadKeyAgain';

        $file = [
            'name' => 'fooBar.mp3',
            'tmp_name' => '/foo/fooBar.mp3',
        ];

        $this->cacheLocationService->expects(self::once())
            ->method('getCacheLocation')
            ->willReturn('/foo/bar/');

        $this->fileSystem->expects(self::once())
            ->method('rename')
            ->with(
                self::equalTo($file['tmp_name']),
                self::equalTo('/foo/bar/' . $file['name']),
                self::equalTo(true)
            )
            ->willReturn(true);

        $this->fileSystem->expects(self::once())
            ->method('exists')
            ->with(self::equalTo($file['tmp_name']))
            ->willReturn(true);

        $this->phpInternals->expects(self::once())
            ->method('mimeContentType')
            ->with(self::equalTo($file['tmp_name']))
            ->willReturn(Constants::VALID_PODCAST_AUDIO_MIME_TYPES[1]);

        $this->phpInternals->expects(self::never())->method('phpExit');

        $this->uploadKeysService->expects(self::once())
            ->method('consumeKey')
            ->with(self::equalTo($key))
            ->willReturn(true);

        $this->emitterStack->expects(self::once())
            ->method('emit')
            ->willReturnCallback(static function (ResponseInterface $response) use ($self) {
                $self->testResponse = $response;

                return true;
            });

        // Invoke service method
        $this->service->upload($key, $file);

        self::assertInstanceOf(ResponseInterface::class, $this->testResponse);

        self::assertSame(200, $this->testResponse->getStatusCode());

        $headers = $this->testResponse->getHeaders();

        self::assertCount(1, $headers);

        self::assertCount(1, $headers['Content-Type']);

        self::assertSame('application/json', $headers['Content-Type'][0]);

        self::assertSame(
            '{"success":true,"message":"","newUploadKey":"","file":{"location":"\/foo\/bar\/fooBar.mp3","name":"fooBar.mp3","mimeType":"audio\/aac","fileSize":"0"}}',
            (string) $this->testResponse->getBody()
        );
    }
}
