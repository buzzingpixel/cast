<?php

declare(strict_types=1);

namespace Tests\ExpressionEngine\Uploading;

use BuzzingPixel\Cast\Cast\Clock\CommonTime;
use BuzzingPixel\Cast\ExpressionEngine\Factory\QueryBuilderFactory;
use BuzzingPixel\Cast\ExpressionEngine\Uploading\UploadKeysService;
use EllisLab\ExpressionEngine\Service\Database\Query;
use LogicException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidInterface;
use Throwable;
use function strtotime;

class UploadKeysServiceTest extends TestCase
{
    /** @var int */
    private $factoryMakeCalls = 0;
    /** @var MockObject&Query */
    private $queryBuilder1;
    /** @var MockObject&Query */
    private $queryBuilder2;
    /** @var MockObject&Query */
    private $queryBuilder3;
    /** @var MockObject&UuidInterface */
    private $uuid4;
    /** @var UploadKeysService */
    private $uploadKeysService;

    /**
     * @throws Throwable
     */
    protected function setUp() : void
    {
        $self = $this;

        /** @var MockObject&Query $queryBuilder0 */
        $queryBuilder0 = $this->createMock(Query::class);

        $queryBuilder0->expects(self::at(0))
            ->method('table_exists')
            ->with(self::equalTo('cast_audio_upload_keys'))
            ->willReturn(true);

        $this->queryBuilder1 = $this->createMock(Query::class);

        $this->queryBuilder1->expects(self::at(0))
            ->method('where')
            ->with(
                self::equalTo(
                    'expires < ' . CommonTime::getCommonTime()->getTimestamp()
                )
            );

        $this->queryBuilder1->expects(self::at(1))
            ->method('delete')
            ->with(self::equalTo('cast_audio_upload_keys'));

        $this->queryBuilder2 = $this->createMock(Query::class);

        $this->queryBuilder3 = $this->createMock(Query::class);

        /** @var MockObject&QueryBuilderFactory $queryBuilderFactory */
        $queryBuilderFactory = $this->createMock(QueryBuilderFactory::class);

        $queryBuilderFactory->method('make')
            ->willReturnCallback(function () use ($self, $queryBuilder0) {
                $calls = $self->factoryMakeCalls;

                $self->factoryMakeCalls++;

                if ($calls === 0) {
                    return $queryBuilder0;
                }

                if ($calls === 1) {
                    return $this->queryBuilder1;
                }

                if ($calls === 2) {
                    return $this->queryBuilder2;
                }

                if ($calls === 3) {
                    return $this->queryBuilder3;
                }

                throw new LogicException('Query Builder Factory Make method called too many times');
            });

        $this->uuid4 = (new UuidFactory())->uuid4();

        /** @var MockObject&UuidFactory $uuidFactory */
        $uuidFactory = $this->createMock(UuidFactory::class);

        $uuidFactory->method('uuid4')
            ->willReturn($this->uuid4);

        $this->uploadKeysService = new UploadKeysService(
            $queryBuilderFactory,
            $uuidFactory
        );
    }

    /**
     * @throws Throwable
     */
    public function testTableDoesNotExist() : void
    {
        /** @var MockObject&Query $queryBuilder0 */
        $queryBuilder = $this->createMock(Query::class);

        $queryBuilder->expects(self::at(0))
            ->method('table_exists')
            ->with(self::equalTo('cast_audio_upload_keys'))
            ->willReturn(false);

        /** @var MockObject&QueryBuilderFactory $queryBuilderFactory */
        $queryBuilderFactory = $this->createMock(QueryBuilderFactory::class);

        $queryBuilderFactory->expects(self::once())->method('make')->willReturn($queryBuilder);

        new UploadKeysService(
            $queryBuilderFactory,
            new UuidFactory()
        );
    }

    public function testCreateKey() : void
    {
        $key = $this->uuid4->toString();

        $time = CommonTime::getCommonTime()->getTimestamp();

        $this->queryBuilder2->expects(self::once())
            ->method('insert')
            ->with(
                self::equalTo('cast_audio_upload_keys'),
                self::equalTo([
                    'key' => $key,
                    'created' => $time,
                    'expires' => strtotime('+ 2 hours', $time),
                ])
            );

        self::assertSame($key, $this->uploadKeysService->createKey());
    }

    public function testDeleteKey() : void
    {
        $key = 'fooBar';

        $this->queryBuilder2->expects(self::once())
            ->method('delete')
            ->with(
                self::equalTo('cast_audio_upload_keys'),
                self::equalTo(['key' => $key])
            );

        $this->uploadKeysService->deleteKey($key);
    }

    public function testValidateKeyInvalidKey() : void
    {
        $key = 'testKey';

        $this->queryBuilder2->expects(self::at(0))
            ->method('where')
            ->with(
                self::equalTo('key'),
                self::equalTo($key)
            )
            ->willReturn($this->queryBuilder2);

        $this->queryBuilder2->expects(self::at(1))
            ->method('count_all_results')
            ->with(self::equalTo('cast_audio_upload_keys'))
            ->willReturn(0);

        self::assertFalse($this->uploadKeysService->validateKey($key));
    }

    public function testValidateKeyValidKey() : void
    {
        $key = 'testKey';

        $this->queryBuilder2->expects(self::at(0))
            ->method('where')
            ->with(
                self::equalTo('key'),
                self::equalTo($key)
            )
            ->willReturn($this->queryBuilder2);

        $this->queryBuilder2->expects(self::at(1))
            ->method('count_all_results')
            ->with(self::equalTo('cast_audio_upload_keys'))
            ->willReturn(1);

        self::assertTrue($this->uploadKeysService->validateKey($key));
    }

    public function testConsumeKeyInvalidKey() : void
    {
        $key = 'testKey';

        $this->queryBuilder2->expects(self::at(0))
            ->method('where')
            ->with(
                self::equalTo('key'),
                self::equalTo($key)
            )
            ->willReturn($this->queryBuilder2);

        $this->queryBuilder2->expects(self::at(1))
            ->method('count_all_results')
            ->with(self::equalTo('cast_audio_upload_keys'))
            ->willReturn(0);

        $this->queryBuilder3->expects(self::never())
            ->method('delete');

        self::assertFalse($this->uploadKeysService->consumeKey($key));
    }

    public function testConsumeKeyValidKey() : void
    {
        $key = 'testKey';

        $this->queryBuilder2->expects(self::at(0))
            ->method('where')
            ->with(
                self::equalTo('key'),
                self::equalTo($key)
            )
            ->willReturn($this->queryBuilder2);

        $this->queryBuilder2->expects(self::at(1))
            ->method('count_all_results')
            ->with(self::equalTo('cast_audio_upload_keys'))
            ->willReturn(1);

        $this->queryBuilder3->expects(self::at(0))
            ->method('delete')
            ->with(
                self::equalTo('cast_audio_upload_keys'),
                self::equalTo(['key' => $key])
            );

        self::assertTrue($this->uploadKeysService->consumeKey($key));
    }
}
