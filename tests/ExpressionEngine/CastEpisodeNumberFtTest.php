<?php

declare(strict_types=1);

namespace Tests\ExpressionEngine;

use Cast_episode_number_ft;
use EE_Input;
use EE_Loader;
use EE_URI;
use EllisLab\ExpressionEngine\Model\Channel\ChannelEntry;
use EllisLab\ExpressionEngine\Service\Model\Facade as ModelFacade;
use EllisLab\ExpressionEngine\Service\Model\Query\Builder as ModelQueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Throwable;

class CastEpisodeNumberFtTest extends TestCase
{
    /** @var Cast_episode_number_ft */
    private $ft;
    /** @var MockObject&EE_URI */
    private $eeUri;
    /** @var MockObject&EE_Input */
    private $eeInput;
    /** @var MockObject&ModelFacade */
    private $modelFacade;

    /**
     * @throws Throwable
     */
    protected function setUp() : void
    {
        /** @var MockObject&EE_Loader $eeLoader */
        $eeLoader = $this->createMock(EE_Loader::class);

        $eeLoader->method('get_package_paths')->willReturn([]);

        $eeLoader->expects(self::once())
            ->method('add_package_path')
            ->with(self::equalTo('pathThirdTestcast/'));

        $this->eeUri = $this->createMock(EE_URI::class);

        $this->eeInput = $this->createMock(EE_Input::class);

        $this->modelFacade = $this->createMock(ModelFacade::class);

        $this->ft = new Cast_episode_number_ft(
            $eeLoader,
            $this->eeUri,
            $this->eeInput,
            $this->modelFacade
        );

        $this->ft->_init([
            'field_id' => 321,
            'content_id' => 675,
        ]);
    }

    public function testDisplayFieldWithData() : void
    {
        self::assertSame(
            '<input type="hidden" value="retain_episode_number_foo" name="">foo',
            $this->ft->display_field('foo')
        );
    }

    /**
     * @throws Throwable
     */
    public function testDisplayFieldPostHasChannelId() : void
    {
        $modelQueryBuilder = $this->createMock(ModelQueryBuilder::class);

        $modelQueryBuilder->expects(self::at(0))
            ->method('filter')
            ->with(
                self::equalTo('channel_id'),
                self::equalTo(123)
            )
            ->willReturn($modelQueryBuilder);

        $modelQueryBuilder->expects(self::at(1))
            ->method('filter')
            ->with(
                self::equalTo('field_id_321'),
                self::equalTo('!='),
                self::equalTo('')
            )
            ->willReturn($modelQueryBuilder);

        $modelQueryBuilder->expects(self::at(2))
            ->method('count')
            ->willReturn(987);

        $this->eeInput->method('get_post')->willReturn(123);

        $this->modelFacade->expects(self::once())
            ->method('get')
            ->with(self::equalTo('ChannelEntry'))
            ->willReturn($modelQueryBuilder);

        self::assertSame(
            '<input type="hidden" value="create_episode_number" name="">988',
            $this->ft->display_field(null)
        );
    }

    /**
     * @throws Throwable
     */
    public function testDisplayFieldSegmentHasChannelId() : void
    {
        $this->eeUri->expects(self::at(0))
            ->method('segment')
            ->with(self::equalTo(3))
            ->willReturn('create');

        $this->eeUri->expects(self::at(1))
            ->method('segment')
            ->with(self::equalTo(4))
            ->willReturn(645);

        $modelQueryBuilder = $this->createMock(ModelQueryBuilder::class);

        $modelQueryBuilder->expects(self::at(0))
            ->method('filter')
            ->with(
                self::equalTo('channel_id'),
                self::equalTo(645)
            )
            ->willReturn($modelQueryBuilder);

        $modelQueryBuilder->expects(self::at(1))
            ->method('filter')
            ->with(
                self::equalTo('field_id_321'),
                self::equalTo('!='),
                self::equalTo('')
            )
            ->willReturn($modelQueryBuilder);

        $modelQueryBuilder->expects(self::at(2))
            ->method('count')
            ->willReturn(321);

        $this->modelFacade->expects(self::once())
            ->method('get')
            ->with(self::equalTo('ChannelEntry'))
            ->willReturn($modelQueryBuilder);

        self::assertSame(
            '<input type="hidden" value="create_episode_number" name="">322',
            $this->ft->display_field(null)
        );
    }

    /**
     * @throws Throwable
     */
    public function testDisplayFieldQueryForChannelId() : void
    {
        $channelEntry = $this->createMock(ChannelEntry::class);

        $channelEntry->expects(self::once())
            ->method('getProperty')
            ->with(self::equalTo('channel_id'))
            ->willReturn('923');

        $modelQueryBuilderEntry = $this->createMock(ModelQueryBuilder::class);

        $modelQueryBuilderEntry->expects(self::once())
            ->method('first')
            ->willReturn($channelEntry);

        $this->modelFacade->expects(self::at(0))
            ->method('get')
            ->with(
                self::equalTo('ChannelEntry'),
                self::equalTo(675)
            )
            ->willReturn($modelQueryBuilderEntry);

        /**
         * Count Query
         */
        $modelQueryBuilderCount = $this->createMock(ModelQueryBuilder::class);

        $modelQueryBuilderCount->expects(self::at(0))
            ->method('filter')
            ->with(
                self::equalTo('channel_id'),
                self::equalTo(923)
            )
            ->willReturn($modelQueryBuilderCount);

        $modelQueryBuilderCount->expects(self::at(1))
            ->method('filter')
            ->with(
                self::equalTo('field_id_321'),
                self::equalTo('!='),
                self::equalTo('')
            )
            ->willReturn($modelQueryBuilderCount);

        $modelQueryBuilderCount->expects(self::at(2))
            ->method('count')
            ->willReturn(543);

        $this->modelFacade->expects(self::at(1))
            ->method('get')
            ->with(self::equalTo('ChannelEntry'))
            ->willReturn($modelQueryBuilderCount);

        self::assertSame(
            '<input type="hidden" value="create_episode_number" name="">544',
            $this->ft->display_field(null)
        );
    }

    public function testSaveRetainData() : void
    {
        self::assertSame('', $this->ft->save('foo'));

        self::assertSame('423', $this->ft->save('retain_episode_number_423'));
    }

    /**
     * @throws Throwable
     */
    public function testSaveCreateEpisodeNumber() : void
    {
        $modelQueryBuilder = $this->createMock(ModelQueryBuilder::class);

        $modelQueryBuilder->expects(self::at(0))
            ->method('filter')
            ->with(
                self::equalTo('channel_id'),
                self::equalTo(645)
            )
            ->willReturn($modelQueryBuilder);

        $modelQueryBuilder->expects(self::at(1))
            ->method('filter')
            ->with(
                self::equalTo('field_id_321'),
                self::equalTo('!='),
                self::equalTo('')
            )
            ->willReturn($modelQueryBuilder);

        $modelQueryBuilder->expects(self::at(2))
            ->method('count')
            ->willReturn(324);

        $this->modelFacade->expects(self::once())
            ->method('get')
            ->with(self::equalTo('ChannelEntry'))
            ->willReturn($modelQueryBuilder);

        $channelEntry = $this->createMock(ChannelEntry::class);

        $channelEntry->expects(self::once())
            ->method('getProperty')
            ->with(self::equalTo('channel_id'))
            ->willReturn('645');

        self::assertSame('325', $this->ft->save('create_episode_number', $channelEntry));
    }
}
