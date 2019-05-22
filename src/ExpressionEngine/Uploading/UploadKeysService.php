<?php

declare(strict_types=1);

namespace BuzzingPixel\Cast\ExpressionEngine\Uploading;

use BuzzingPixel\Cast\Cast\Clock\CommonTime;
use BuzzingPixel\Cast\Cast\Uploading\UploadKeysServiceContract;
use BuzzingPixel\Cast\ExpressionEngine\Factory\QueryBuilderFactory;
use Ramsey\Uuid\UuidFactory;
use function Safe\strtotime;

class UploadKeysService implements UploadKeysServiceContract
{
    /** @var QueryBuilderFactory */
    private $queryBuilderFactory;
    /** @var UuidFactory */
    private $uuidFactory;

    public function __construct(
        QueryBuilderFactory $queryBuilderFactory,
        UuidFactory $uuidFactory
    ) {
        $this->queryBuilderFactory = $queryBuilderFactory;
        $this->uuidFactory         = $uuidFactory;

        $queryBuilder = $this->queryBuilderFactory->make();

        $queryBuilder->where('expires < ' . CommonTime::getCommonTime()->getTimestamp());

        $queryBuilder->delete('cast_audio_upload_keys');
    }

    public function createKey() : string
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $key = $this->uuidFactory->uuid4()->toString();

        $time = CommonTime::getCommonTime()->getTimestamp();

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->queryBuilderFactory->make()->insert(
            'cast_audio_upload_keys',
            [
                'key' => $key,
                'created' => $time,
                'expires' => strtotime('+ 2 hours', $time),
            ]
        );

        return $key;
    }

    public function deleteKey(string $key) : void
    {
        $this->queryBuilderFactory->make()->delete(
            'cast_audio_upload_keys',
            ['key' => $key]
        );
    }

    public function validateKey(string $key) : bool
    {
        $query = (int) $this->queryBuilderFactory->make()
            ->where('key', $key)
            ->count_all_results('cast_audio_upload_keys');

        return $query > 0;
    }

    public function consumeKey(string $key) : bool
    {
        $isValid = $this->validateKey($key);

        if ($isValid) {
            $this->deleteKey($key);
        }

        return $isValid;
    }
}
