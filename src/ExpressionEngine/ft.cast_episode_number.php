<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps


use BuzzingPixel\Cast\Cast\Constants;
use BuzzingPixel\Cast\Cast\Di;
use EllisLab\ExpressionEngine\Model\Channel\ChannelEntry;
use EllisLab\ExpressionEngine\Service\Model\Facade as ModelFacade;

class Cast_episode_number_ft extends EE_Fieldtype
{
    /** @var mixed[] */
    public $info = [
        'name' => Constants::NAME,
        'version' => Constants::VERSION,
    ];

    /** @var EE_URI */
    private $eeUri;
    /** @var EE_Input */
    private $eeInput;
    /** @var ModelFacade */
    private $modelFacade;

    public function __construct(
        ?EE_Loader $loader = null,
        ?EE_URI $eeUri = null,
        ?EE_Input $eeInput = null,
        ?ModelFacade $modelFacade = null
    ) {
        parent::__construct();

        // @codeCoverageIgnoreStart

        if (! $loader) {
            $loader = Di::diContainer()->get(EE_Loader::class);
        }

        if (! $eeUri) {
            $eeUri = Di::diContainer()->get(EE_URI::class);
        }

        if (! $eeInput) {
            $eeInput = Di::diContainer()->get(EE_Input::class);
        }

        if (! $modelFacade) {
            $modelFacade = Di::diContainer()->get(ModelFacade::class);
        }

        // @codeCoverageIgnoreEnd

        $this->eeUri       = $eeUri;
        $this->eeInput     = $eeInput;
        $this->modelFacade = $modelFacade;

        $castPath = PATH_THIRD . 'cast/';

        /** @var array $packagePaths */
        $packagePaths = $loader->get_package_paths();

        $pathLoaded = in_array($castPath, $packagePaths);

        if ($pathLoaded) {
            // @codeCoverageIgnoreStart

            return;

            // @codeCoverageIgnoreEnd
        }

        $loader->add_package_path($castPath);
    }

    /** @var ChannelEntry|null */
    private $entry;

    /**
     * @param mixed $data
     */
    public function display_field($data) : string
    {
        if (! $data) {
            $epNum = $this->calculateEpisodeNumber();

            return '<input type="hidden" value="create_episode_number" name="' . $this->field_name . '">' . $epNum;
        }

        return '<input type="hidden" value="retain_episode_number_' . $data . '" name="' . $this->field_name . '">' . $data;
    }

    /**
     * @param mixed $data
     */
    public function save($data) : string
    {
        /** @var ChannelEntry $entry */
        $entry = func_get_args()[1] ?? null;

        $this->entry = $entry;

        if ($data === 'create_episode_number') {
            return (string) $this->calculateEpisodeNumber();
        }

        $parts = explode('retain_episode_number_', $data);

        return (string) ($parts[1] ?? '');
    }

    private function calculateEpisodeNumber() : int
    {
        $channelId = (int) $this->eeInput->get_post('channel_id');

        if ($this->entry) {
            $channelId = (int) $this->entry->getProperty('channel_id');
        }

        if (! $channelId && $this->eeUri->segment(3) === 'create') {
            $channelId = (int) $this->eeUri->segment(4);
        }

        if (! $channelId) {
            $channelId = $this->getChannelIdFromEntryId();
        }

        $count = $this->modelFacade->get('ChannelEntry')
            ->filter('channel_id', $channelId)
            ->filter('field_id_' . $this->field_id, '!=', '')
            ->count();

        return $count + 1;
    }

    private function getChannelIdFromEntryId() : int
    {
        /** @var ChannelEntry $channelEntry */
        $channelEntry = $this->modelFacade->get('ChannelEntry', $this->content_id())->first();

        return (int) $channelEntry->getProperty('channel_id');
    }
}
