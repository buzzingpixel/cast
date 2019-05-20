<?php

declare(strict_types=1);

use BuzzingPixel\Cast\Cast\Constants;
use BuzzingPixel\Cast\Cast\Di;
use BuzzingPixel\Cast\Cast\Facade\PhpInternals;
use BuzzingPixel\Cast\Cast\Templating\TemplatingService;
use BuzzingPixel\Cast\ExpressionEngine\Service\NormalizePaths;
use EllisLab\ExpressionEngine\Service\Validation\Factory as ValidationFactory;
use EllisLab\ExpressionEngine\Service\Validation\Result as ValidationResult;
use EllisLab\ExpressionEngine\Service\Validation\Rule\Callback as ValidationRuleCallback;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

class Cast_audio_ft extends EE_Fieldtype
{
    /** @var NormalizePaths */
    private $normalizePaths;
    /** @var ValidationFactory */
    private $validationFactory;
    /** @var PhpInternals */
    private $phpInternals;
    /** @var TemplatingService */
    private $templatingService;

    /** @var mixed[] */
    public $info = [
        'name' => Constants::NAME,
        'version' => Constants::VERSION,
    ];

    public function __construct(
        ?EE_Loader $loader = null,
        ?EE_Lang $lang = null,
        ?NormalizePaths $normalizePaths = null,
        ?ValidationFactory $validationFactory = null,
        ?PhpInternals $phpInternals = null,
        ?TemplatingService $templatingService = null
    ) {
        // @codeCoverageIgnoreStart

        if (! $loader) {
            $loader =  Di::diContainer()->get(EE_Loader::class);
        }

        if (! $lang) {
            $lang = Di::diContainer()->get(EE_Lang::class);
        }

        if (! $normalizePaths) {
            $normalizePaths = Di::diContainer()->get(NormalizePaths::class);
        }

        if (! $validationFactory) {
            $validationFactory = Di::diContainer()->get(ValidationFactory::class);
        }

        if (! $phpInternals) {
            $phpInternals = Di::diContainer()->get(PhpInternals::class);
        }

        if (! $templatingService) {
            $this->templatingService = Di::diContainer()->get(TemplatingService::class);
        }

        // @codeCoverageIgnoreEnd

        $this->normalizePaths    = $normalizePaths;
        $this->validationFactory = $validationFactory;
        $this->phpInternals      = $phpInternals;

        $castPath = PATH_THIRD . 'cast/';

        /** @var array $packagePaths */
        $packagePaths = $loader->get_package_paths();

        $pathLoaded = in_array($castPath, $packagePaths);

        if (! $pathLoaded) {
            $loader->add_package_path($castPath);
        }

        $lang->loadfile('cast');

        // Run parent constructor
        parent::__construct();
    }

    // phpcs:disable SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint

    /**
     * @param mixed[]|null $data
     *
     * @return mixed[]
     */
    public function display_settings($data) : array
    {
        $data = is_array($data) ? $data : [];

        $useFtp = $data['cast_audio_use_ftp'] ?? 'n';

        $ftpProtocol = $data['cast_audio_ftp_protocol'] ?? 'ftp';

        return [
            'standard_field_options' => [
                'label' => 'field_options',
                'group' => 'cast_audio',
                'settings' => [
                    'cast_audio_upload_path' => [
                        'title' => 'upload_path',
                        'fields' => [
                            'cast_audio_upload_path' => [
                                'type' => 'text',
                                'required' => true,
                                'value' => $data['cast_audio_upload_path'] ?? '',
                            ],
                        ],
                    ],
                    'cast_audio_upload_url' => [
                        'title' => 'upload_url',
                        'fields' => [
                            'cast_audio_upload_url' => [
                                'type' => 'text',
                                'required' => true,
                                'value' => $data['cast_audio_upload_url'] ?? '',
                            ],
                        ],
                    ],
                ],
            ],
            'ftp_field_options' => [
                'label' => 'ftp_field_options',
                'group' => 'cast_audio',
                'settings' => [
                    'cast_audio_use_ftp' => [
                        'title' => 'use_ftp',
                        'fields' => [
                            'cast_audio_use_ftp' => [
                                'type' => 'yes_no',
                                'value' => $useFtp === 'y' ? 'y' : 'n',
                            ],
                        ],
                    ],
                    'cast_audio_ftp_protocol' => [
                        'title' => 'protocol',
                        'fields' => [
                            'cast_audio_ftp_protocol' => [
                                'type' => 'inline_radio',
                                'choices' => [
                                    'ftp' => 'FTP',
                                    'sftp' => 'SFTP',
                                ],
                                'value' => $ftpProtocol,
                            ],
                        ],
                    ],
                    'cast_audio_upload_ftp_server' => [
                        'title' => 'upload_ftp_server',
                        'fields' => [
                            'cast_audio_upload_ftp_server' => [
                                'type' => 'text',
                                'value' => $data['cast_audio_upload_ftp_server'] ?? '',
                            ],
                        ],
                    ],
                    'cast_audio_upload_ftp_user_name' => [
                        'title' => 'upload_ftp_user_name',
                        'fields' => [
                            'cast_audio_upload_ftp_user_name' => [
                                'type' => 'text',
                                'value' => $data['cast_audio_upload_ftp_user_name'] ?? '',
                            ],
                        ],
                    ],
                    'cast_audio_upload_ftp_password' => [
                        'title' => 'upload_ftp_password',
                        'fields' => [
                            'cast_audio_upload_ftp_password' => [
                                'type' => 'text',
                                'value' => $data['cast_audio_upload_ftp_password'] ?? '',
                            ],
                        ],
                    ],
                    'cast_audio_upload_ftp_port' => [
                        'title' => 'upload_ftp_port',
                        'fields' => [
                            'cast_audio_upload_ftp_port' => [
                                'type' => 'text',
                                'value' => $data['cast_audio_upload_ftp_port'] ?? '',
                            ],
                        ],
                    ],
                    'cast_audio_upload_ftp_remote_path' => [
                        'title' => 'upload_ftp_remote_path',
                        'fields' => [
                            'cast_audio_upload_ftp_remote_path' => [
                                'type' => 'text',
                                'value' => $data['cast_audio_upload_ftp_remote_path'] ?? '',
                            ],
                        ],
                    ],
                    'cast_audio_upload_ftp_remote_url' => [
                        'title' => 'upload_ftp_remote_url',
                        'fields' => [
                            'cast_audio_upload_ftp_remote_url' => [
                                'type' => 'text',
                                'value' => $data['cast_audio_upload_ftp_remote_url'] ?? '',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param mixed[]|null $data
     */
    public function validate_settings($data) : ValidationResult
    {
        // Ugh, apparently EE's validator can't be tested
        // @codeCoverageIgnoreStart
        $data = is_array($data) ? $data : [];

        $useFtp = $data['cast_audio_use_ftp'] ?? 'n';
        $useFtp = $useFtp === 'y';

        $validator = $this->validationFactory->make([
            'cast_audio_upload_path' => 'required|validPath',
            'cast_audio_upload_url' => 'required',
            'cast_audio_use_ftp' => 'enum[y,n]',
            'cast_audio_ftp_protocol' => 'enum[ftp,sftp]',
            'cast_audio_upload_ftp_server' => 'requiredIfFtp',
            'cast_audio_upload_ftp_user_name' => 'requiredIfFtp',
            'cast_audio_upload_ftp_password' => 'requiredIfFtp',
            'cast_audio_upload_ftp_port' => 'naturalNumberIfFtp',
            'cast_audio_upload_ftp_remote_url' => 'requiredIfFtp',
        ]);

        $validator->defineRule('validPath', function ($key, $val, $params, ValidationRuleCallback $rule) {
            $valid = $this->phpInternals->isDir($this->normalizePaths->normalize($val));

            if (! $valid) {
                $rule->stop();
            }

            return $valid ?: 'valid_path_required';
        });

        $validator->defineRule('requiredIfFtp', static function ($key, $val, $params, ValidationRuleCallback $rule) use ($useFtp) {
            $invalid = $useFtp && ! $val;
            $valid   = ! $invalid;

            if (! $valid) {
                $rule->stop();
            }

            return $valid ?: 'required';
        });

        $validator->defineRule('naturalNumberIfFtp', static function ($key, $val, $params, ValidationRuleCallback $rule) use ($useFtp) {
            $invalid = ($useFtp || $val) && ! ctype_digit($val);
            $valid   = ! $invalid;

            if (! $valid) {
                $rule->stop();
            }

            return $valid ?: 'must_be_positive_integer';
        });

        return $validator->validate($data);

        // @codeCoverageIgnoreEnd
    }

    /**
     * @param mixed[]|null $data
     *
     * @return mixed[]
     */
    public function save_settings($data) : array
    {
        $data = is_array($data) ? $data : [];

        return $data;
    }

    // phpcs:enable SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint

    /**
     * @param mixed $data
     */
    public function display_field($data) : string
    {
        return $this->templatingService->render('CastAudioField');
    }
}
