<?php

declare(strict_types=1);

use BuzzingPixel\Cast\Cast\Constants;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

class Cast_audio_ft extends EE_Fieldtype
{
    /** @var mixed[] */
    public $info = [
        'name' => Constants::NAME,
        'version' => Constants::VERSION,
    ];

    public function __construct(
        ?EE_Loader $eeLoader = null,
        ?EE_Lang $eeLang = null
    ) {
        // @codeCoverageIgnoreStart
        if (! $eeLoader) {
            $eeLoader = ee()->load;
        }

        if (! $eeLang) {
            $eeLang = ee()->lang;
        }
        // @codeCoverageIgnoreEnd

        $castPath = PATH_THIRD . 'cast/';

        /** @var array $packagePaths */
        $packagePaths = $eeLoader->get_package_paths();

        $pathLoaded = in_array($castPath, $packagePaths);

        if (! $pathLoaded) {
            $eeLoader->add_package_path($castPath);
        }

        $eeLang->loadfile('cast');

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

        $useFtp = $data['cast_use_ftp'] ?? 'n';

        $ftpProtocol = $data['cast_ftp_protocol'] ?? 'ftp';

        return [
            'standard_field_options' => [
                'label' => 'field_options',
                'group' => 'cast',
                'settings' => [
                    'cast_upload_path' => [
                        'title' => 'upload_path',
                        'fields' => [
                            'cast_upload_path' => [
                                'type' => 'text',
                                'required' => true,
                                'value' => $data['cast_upload_path'] ?? '',
                            ],
                        ],
                    ],
                    'cast_upload_url' => [
                        'title' => 'upload_url',
                        'fields' => [
                            'cast_upload_url' => [
                                'type' => 'text',
                                'required' => true,
                                'value' => $data['cast_upload_url'] ?? '',
                            ],
                        ],
                    ],
                ],
            ],
            'ftp_field_options' => [
                'label' => 'ftp_field_options',
                'group' => 'cast',
                'settings' => [
                    'cast_use_ftp' => [
                        'title' => 'use_ftp',
                        'fields' => [
                            'cast_use_ftp' => [
                                'type' => 'yes_no',
                                'value' => $useFtp === 'y' ? 'y' : 'n',
                            ],
                        ],
                    ],
                    'cast_ftp_protocol' => [
                        'title' => 'protocol',
                        'fields' => [
                            'cast_ftp_protocol' => [
                                'type' => 'inline_radio',
                                'choices' => [
                                    'ftp' => 'FTP',
                                    'sftp' => 'SFTP',
                                ],
                                'value' => $ftpProtocol,
                            ],
                        ],
                    ],
                    'cast_upload_ftp_server' => [
                        'title' => 'upload_ftp_server',
                        'fields' => [
                            'cast_upload_ftp_server' => [
                                'type' => 'text',
                                'value' => $data['cast_upload_ftp_server'] ?? '',
                            ],
                        ],
                    ],
                    'cast_upload_ftp_user_name' => [
                        'title' => 'upload_ftp_user_name',
                        'fields' => [
                            'cast_upload_ftp_user_name' => [
                                'type' => 'text',
                                'value' => $data['cast_upload_ftp_user_name'] ?? '',
                            ],
                        ],
                    ],
                    'cast_upload_ftp_password' => [
                        'title' => 'upload_ftp_password',
                        'fields' => [
                            'cast_upload_ftp_password' => [
                                'type' => 'text',
                                'value' => $data['cast_upload_ftp_password'] ?? '',
                            ],
                        ],
                    ],
                    'cast_upload_ftp_port' => [
                        'title' => 'upload_ftp_port',
                        'fields' => [
                            'cast_upload_ftp_port' => [
                                'type' => 'text',
                                'value' => $data['cast_upload_ftp_port'] ?? '',
                            ],
                        ],
                    ],
                    'cast_upload_ftp_remote_path' => [
                        'title' => 'upload_ftp_remote_path',
                        'fields' => [
                            'cast_upload_ftp_remote_path' => [
                                'type' => 'text',
                                'value' => $data['cast_upload_ftp_remote_path'] ?? '',
                            ],
                        ],
                    ],
                    'cast_upload_ftp_remote_url' => [
                        'title' => 'upload_ftp_remote_url',
                        'fields' => [
                            'cast_upload_ftp_remote_url' => [
                                'type' => 'text',
                                'value' => $data['cast_upload_ftp_remote_url'] ?? '',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    // phpcs:enable SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint

    /**
     * @param mixed $data
     */
    public function display_field($data) : string
    {
        // TODO: Implement display_field() method.
        return 'TODO: Implement display_field() method.';
    }
}
