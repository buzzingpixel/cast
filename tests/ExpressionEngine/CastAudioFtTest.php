<?php

declare(strict_types=1);

namespace Tests\ExpressionEngine;

use BuzzingPixel\Cast\Cast\Constants;
use Cast_audio_ft;
use EE_Lang;
use EE_Loader;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Throwable;

class CastAudioFtTest extends TestCase
{
    /** @var Cast_audio_ft */
    private $ft;

    /**
     * @throws Throwable
     */
    public function setUp() : void
    {
        /** @var MockObject&EE_Loader $eeLoader */
        $eeLoader = $this->createMock(EE_Loader::class);

        $eeLoader->method('get_package_paths')->willReturn([]);

        $eeLoader->expects(self::once())
            ->method('add_package_path')
            ->with(self::equalTo('pathThirdTestcast/'));

        /** @var MockObject&EE_Lang $eeLang */
        $eeLang = $this->createMock(EE_Lang::class);

        $eeLang->expects(self::once())
            ->method('loadfile')
            ->with(self::equalTo('cast'));

        $this->ft = new Cast_audio_ft($eeLoader, $eeLang);
    }

    public function testInfoProperty() : void
    {
        self::assertSame(
            [
                'name' => Constants::NAME,
                'version' => Constants::VERSION,
            ],
            $this->ft->info
        );
    }

    public function testDisplaySettingsWithNoInputData() : void
    {
        self::assertEquals(
            [
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
                                    'value' => '',
                                ],
                            ],
                        ],
                        'cast_upload_url' => [
                            'title' => 'upload_url',
                            'fields' => [
                                'cast_upload_url' => [
                                    'type' => 'text',
                                    'required' => true,
                                    'value' => '',
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
                                    'value' => 'n',
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
                                    'value' => 'ftp',
                                ],
                            ],
                        ],
                        'cast_upload_ftp_server' => [
                            'title' => 'upload_ftp_server',
                            'fields' => [
                                'cast_upload_ftp_server' => [
                                    'type' => 'text',
                                    'value' => '',
                                ],
                            ],
                        ],
                        'cast_upload_ftp_user_name' => [
                            'title' => 'upload_ftp_user_name',
                            'fields' => [
                                'cast_upload_ftp_user_name' => [
                                    'type' => 'text',
                                    'value' => '',
                                ],
                            ],
                        ],
                        'cast_upload_ftp_password' => [
                            'title' => 'upload_ftp_password',
                            'fields' => [
                                'cast_upload_ftp_password' => [
                                    'type' => 'text',
                                    'value' => '',
                                ],
                            ],
                        ],
                        'cast_upload_ftp_port' => [
                            'title' => 'upload_ftp_port',
                            'fields' => [
                                'cast_upload_ftp_port' => [
                                    'type' => 'text',
                                    'value' => '',
                                ],
                            ],
                        ],
                        'cast_upload_ftp_remote_path' => [
                            'title' => 'upload_ftp_remote_path',
                            'fields' => [
                                'cast_upload_ftp_remote_path' => [
                                    'type' => 'text',
                                    'value' => '',
                                ],
                            ],
                        ],
                        'cast_upload_ftp_remote_url' => [
                            'title' => 'upload_ftp_remote_url',
                            'fields' => [
                                'cast_upload_ftp_remote_url' => [
                                    'type' => 'text',
                                    'value' => '',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            $this->ft->display_settings(null)
        );
    }

    public function testDisplaySettingsWithInputData() : void
    {
        $data = [
            'cast_upload_path' => 'uploadPathTest',
            'cast_upload_url' => 'uploadUrlTest',
            'cast_use_ftp' => 'y',
            'cast_ftp_protocol' => 'sftp',
            'cast_upload_ftp_server' => 'uploadFtpServerTest',
            'cast_upload_ftp_user_name' => 'uploadFtpUserNameTest',
            'cast_upload_ftp_password' => 'uploadFtpPasswordTest',
            'cast_upload_ftp_port' => 'uploadFtpPortTest',
            'cast_upload_ftp_remote_path' => 'uploadFtpRemotePathTest',
            'cast_upload_ftp_remote_url' => 'uploadFtpRemoteUrlTest',
        ];

        self::assertEquals(
            [
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
                                    'value' => $data['cast_upload_path'],
                                ],
                            ],
                        ],
                        'cast_upload_url' => [
                            'title' => 'upload_url',
                            'fields' => [
                                'cast_upload_url' => [
                                    'type' => 'text',
                                    'required' => true,
                                    'value' => $data['cast_upload_url'],
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
                                    'value' => 'y',
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
                                    'value' => 'sftp',
                                ],
                            ],
                        ],
                        'cast_upload_ftp_server' => [
                            'title' => 'upload_ftp_server',
                            'fields' => [
                                'cast_upload_ftp_server' => [
                                    'type' => 'text',
                                    'value' => $data['cast_upload_ftp_server'],
                                ],
                            ],
                        ],
                        'cast_upload_ftp_user_name' => [
                            'title' => 'upload_ftp_user_name',
                            'fields' => [
                                'cast_upload_ftp_user_name' => [
                                    'type' => 'text',
                                    'value' => $data['cast_upload_ftp_user_name'],
                                ],
                            ],
                        ],
                        'cast_upload_ftp_password' => [
                            'title' => 'upload_ftp_password',
                            'fields' => [
                                'cast_upload_ftp_password' => [
                                    'type' => 'text',
                                    'value' => $data['cast_upload_ftp_password'],
                                ],
                            ],
                        ],
                        'cast_upload_ftp_port' => [
                            'title' => 'upload_ftp_port',
                            'fields' => [
                                'cast_upload_ftp_port' => [
                                    'type' => 'text',
                                    'value' => $data['cast_upload_ftp_port'],
                                ],
                            ],
                        ],
                        'cast_upload_ftp_remote_path' => [
                            'title' => 'upload_ftp_remote_path',
                            'fields' => [
                                'cast_upload_ftp_remote_path' => [
                                    'type' => 'text',
                                    'value' => $data['cast_upload_ftp_remote_path'],
                                ],
                            ],
                        ],
                        'cast_upload_ftp_remote_url' => [
                            'title' => 'upload_ftp_remote_url',
                            'fields' => [
                                'cast_upload_ftp_remote_url' => [
                                    'type' => 'text',
                                    'value' => $data['cast_upload_ftp_remote_url'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            $this->ft->display_settings($data)
        );
    }

    public function testDisplayField() : void
    {
        self::assertSame(
            'TODO: Implement display_field() method.',
            $this->ft->display_field('')
        );
    }
}
