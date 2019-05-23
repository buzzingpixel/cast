<?php

declare(strict_types=1);

namespace BuzzingPixel\Cast\Cast\Uploading;

use BuzzingPixel\Cast\Cast\Constants;
use BuzzingPixel\Cast\Cast\Facade\PhpInternals;
use Symfony\Component\Filesystem\Filesystem;
use Zend\Diactoros\ResponseFactory;
use Zend\HttpHandlerRunner\Emitter\EmitterStack;
use function in_array;
use function Safe\json_encode;
use function Safe\preg_replace;

class UploadFileToCacheLocation
{
    /** @var UploadKeysServiceContract */
    private $uploadKeysService;
    /** @var EmitterStack */
    private $emitterStack;
    /** @var ResponseFactory */
    private $responseFactory;
    /** @var CacheLocationServiceContract */
    private $cacheLocationService;
    /** @var Filesystem */
    private $filesystem;
    /** @var PhpInternals */
    private $phpInternals;

    public function __construct(
        UploadKeysServiceContract $uploadKeysService,
        EmitterStack $emitterStack,
        ResponseFactory $responseFactory,
        CacheLocationServiceContract $cacheLocationService,
        Filesystem $filesystem,
        PhpInternals $phpInternals
    ) {
        $this->uploadKeysService    = $uploadKeysService;
        $this->emitterStack         = $emitterStack;
        $this->responseFactory      = $responseFactory;
        $this->cacheLocationService = $cacheLocationService;
        $this->filesystem           = $filesystem;
        $this->phpInternals         = $phpInternals;
    }

    /**
     * Emits json response
     *
     * @param mixed[] $file
     */
    public function upload(string $uploadKey, array $file) : void
    {
        if (! $this->uploadKeysService->consumeKey($uploadKey)) {
            $this->emitErrorResponse('Invalid upload key', false);

            return;
        }

        $fileName = $file['name'] ?? null;
        $tmpFile  = $file['tmp_name'] ?? null;

        /** @noinspection PhpUnhandledExceptionInspection */
        $fileName = (string) preg_replace('/\s+/', '-', $fileName);

        if (! $fileName ||
            ! $tmpFile ||
            ! $this->filesystem->exists($tmpFile)
        ) {
            $this->emitErrorResponse(
                'The uploaded file could not be found. The most common reason for this is you tried to upload a file that is larger than your server allows.',
                true
            );

            return;
        }

        /** @noinspection PhpStrictTypeCheckingInspection */
        $mimeType = $this->phpInternals->mimeContentType($tmpFile);

        if (! in_array($mimeType, Constants::VALID_PODCAST_AUDIO_MIME_TYPES)) {
            $this->emitErrorResponse(
                'Uploaded file is not an audio file.',
                true
            );

            return;
        }

        $fileDestination = $this->cacheLocationService->getCacheLocation() . $fileName;

        $this->filesystem->rename($tmpFile, $fileDestination, true);

        $response = $this->responseFactory->createResponse(200);
        $response = $response->withHeader('Content-Type', 'application/json');
        /** @noinspection PhpUnhandledExceptionInspection */
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => '',
            'newUploadKey' => $this->uploadKeysService->createKey(),
            'file' => [
                'location' => $fileDestination,
                'name' => $fileName,
                'mimeType' => Constants::MIME_NORMALIZE[$mimeType],
                'fileSize' => (string) $this->phpInternals->fileSize($fileDestination),
            ],
        ]));
        $this->emitterStack->emit($response);
    }

    private function emitErrorResponse(string $reason, bool $sendNewKey) : void
    {
        $newKey = '';

        if ($sendNewKey) {
            $newKey = $this->uploadKeysService->createKey();
        }

        $response = $this->responseFactory->createResponse(400);
        $response = $response->withHeader('Content-Type', 'application/json');
        /** @noinspection PhpUnhandledExceptionInspection */
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => $reason,
            'newUploadKey' => $newKey,
            'file' => [],
        ]));
        $this->emitterStack->emit($response);
        $this->phpInternals->phpExit();
    }
}
