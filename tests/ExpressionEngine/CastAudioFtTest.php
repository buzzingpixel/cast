<?php

declare(strict_types=1);

namespace Tests\ExpressionEngine;

use BuzzingPixel\Cast\Cast\Constants;
use BuzzingPixel\Cast\Cast\Di;
use BuzzingPixel\Cast\Cast\Facade\PhpInternals;
use BuzzingPixel\Cast\Cast\Templating\TemplatingService;
use BuzzingPixel\Cast\Cast\Uploading\UploadKeysServiceContract;
use BuzzingPixel\Cast\ExpressionEngine\Service\ActionsService;
use BuzzingPixel\Cast\ExpressionEngine\Service\NormalizePaths;
use Cast_audio_ft;
use Cp;
use EE_Lang;
use EE_Loader;
use EllisLab\ExpressionEngine\Service\Validation\Factory as EEValidationFactory;
use JsonSerializable;
use LogicException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidFactory;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;
use function filemtime;
use function is_array;
use function ob_get_clean;
use function ob_start;
use function Safe\json_encode;
use function sprintf;

class CastAudioFtTest extends TestCase
{
    /** @var Cast_audio_ft */
    private $ft;

    /** @var MockObject&Cp */
    private $eeCp;

    /** @var string */
    private $uploadKey;
    /** @var MockObject&EE_Lang $eeLang */
    private $eeLang;
    /** @var MockObject&Filesystem */
    private $filesystem;

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

        $this->eeLang = $this->createMock(EE_Lang::class);

        $this->eeCp = $this->createMock(Cp::class);

        $this->eeLang->expects(self::once())
            ->method('loadfile')
            ->with(self::equalTo('cast'));

        /** @var MockObject&NormalizePaths $normalizePaths */
        $normalizePaths = $this->createMock(NormalizePaths::class);

        $normalizePaths->method('normalize')
            ->willReturnCallback(static function (string $path) {
                return sprintf('%s--testNormalize', $path);
            });

        /** @var MockObject&PhpInternals $phpInternals */
        $phpInternals = $this->createMock(PhpInternals::class);

        /** @var MockObject&UploadKeysServiceContract $uploadKeyService */
        $uploadKeyService = $this->createMock(UploadKeysServiceContract::class);

        $this->uploadKey = (new UuidFactory())->uuid4()->toString();

        $uploadKeyService->method('createKey')->willReturn($this->uploadKey);

        /** @var MockObject&ActionsService $actionsService */
        $actionsService = $this->createMock(ActionsService::class);

        $actionsService->method('getUploadActionUrl')
            ->willReturn('testUploadActionUrl');

        $this->filesystem = $this->createMock(Filesystem::class);

        $this->ft = new Cast_audio_ft(
            $eeLoader,
            $this->eeLang,
            $this->eeCp,
            $normalizePaths,
            new EEValidationFactory(),
            $phpInternals,
            $uploadKeyService,
            $actionsService,
            $this->filesystem
        );
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
                    'group' => 'cast_audio',
                    'settings' => [
                        'cast_audio_upload_path' => [
                            'title' => 'upload_path',
                            'fields' => [
                                'cast_audio_upload_path' => [
                                    'type' => 'text',
                                    'required' => true,
                                    'value' => '',
                                ],
                            ],
                        ],
                        'cast_audio_upload_url' => [
                            'title' => 'upload_url',
                            'fields' => [
                                'cast_audio_upload_url' => [
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
                    'group' => 'cast_audio',
                    'settings' => [
                        'cast_audio_use_ftp' => [
                            'title' => 'use_ftp',
                            'fields' => [
                                'cast_audio_use_ftp' => [
                                    'type' => 'yes_no',
                                    'value' => 'n',
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
                                    'value' => 'ftp',
                                ],
                            ],
                        ],
                        'cast_audio_upload_ftp_server' => [
                            'title' => 'upload_ftp_server',
                            'fields' => [
                                'cast_audio_upload_ftp_server' => [
                                    'type' => 'text',
                                    'value' => '',
                                ],
                            ],
                        ],
                        'cast_audio_upload_ftp_user_name' => [
                            'title' => 'upload_ftp_user_name',
                            'fields' => [
                                'cast_audio_upload_ftp_user_name' => [
                                    'type' => 'text',
                                    'value' => '',
                                ],
                            ],
                        ],
                        'cast_audio_upload_ftp_password' => [
                            'title' => 'upload_ftp_password',
                            'fields' => [
                                'cast_audio_upload_ftp_password' => [
                                    'type' => 'text',
                                    'value' => '',
                                ],
                            ],
                        ],
                        'cast_audio_upload_ftp_port' => [
                            'title' => 'upload_ftp_port',
                            'fields' => [
                                'cast_audio_upload_ftp_port' => [
                                    'type' => 'text',
                                    'value' => '',
                                ],
                            ],
                        ],
                        'cast_audio_upload_ftp_remote_path' => [
                            'title' => 'upload_ftp_remote_path',
                            'fields' => [
                                'cast_audio_upload_ftp_remote_path' => [
                                    'type' => 'text',
                                    'value' => '',
                                ],
                            ],
                        ],
                        'cast_audio_upload_ftp_remote_url' => [
                            'title' => 'upload_ftp_remote_url',
                            'fields' => [
                                'cast_audio_upload_ftp_remote_url' => [
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
            'cast_audio_upload_path' => 'uploadPathTest',
            'cast_audio_upload_url' => 'uploadUrlTest',
            'cast_audio_use_ftp' => 'y',
            'cast_audio_ftp_protocol' => 'sftp',
            'cast_audio_upload_ftp_server' => 'uploadFtpServerTest',
            'cast_audio_upload_ftp_user_name' => 'uploadFtpUserNameTest',
            'cast_audio_upload_ftp_password' => 'uploadFtpPasswordTest',
            'cast_audio_upload_ftp_port' => 'uploadFtpPortTest',
            'cast_audio_upload_ftp_remote_path' => 'uploadFtpRemotePathTest',
            'cast_audio_upload_ftp_remote_url' => 'uploadFtpRemoteUrlTest',
        ];

        self::assertEquals(
            [
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
                                    'value' => $data['cast_audio_upload_path'],
                                ],
                            ],
                        ],
                        'cast_audio_upload_url' => [
                            'title' => 'upload_url',
                            'fields' => [
                                'cast_audio_upload_url' => [
                                    'type' => 'text',
                                    'required' => true,
                                    'value' => $data['cast_audio_upload_url'],
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
                                    'value' => 'y',
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
                                    'value' => 'sftp',
                                ],
                            ],
                        ],
                        'cast_audio_upload_ftp_server' => [
                            'title' => 'upload_ftp_server',
                            'fields' => [
                                'cast_audio_upload_ftp_server' => [
                                    'type' => 'text',
                                    'value' => $data['cast_audio_upload_ftp_server'],
                                ],
                            ],
                        ],
                        'cast_audio_upload_ftp_user_name' => [
                            'title' => 'upload_ftp_user_name',
                            'fields' => [
                                'cast_audio_upload_ftp_user_name' => [
                                    'type' => 'text',
                                    'value' => $data['cast_audio_upload_ftp_user_name'],
                                ],
                            ],
                        ],
                        'cast_audio_upload_ftp_password' => [
                            'title' => 'upload_ftp_password',
                            'fields' => [
                                'cast_audio_upload_ftp_password' => [
                                    'type' => 'text',
                                    'value' => $data['cast_audio_upload_ftp_password'],
                                ],
                            ],
                        ],
                        'cast_audio_upload_ftp_port' => [
                            'title' => 'upload_ftp_port',
                            'fields' => [
                                'cast_audio_upload_ftp_port' => [
                                    'type' => 'text',
                                    'value' => $data['cast_audio_upload_ftp_port'],
                                ],
                            ],
                        ],
                        'cast_audio_upload_ftp_remote_path' => [
                            'title' => 'upload_ftp_remote_path',
                            'fields' => [
                                'cast_audio_upload_ftp_remote_path' => [
                                    'type' => 'text',
                                    'value' => $data['cast_audio_upload_ftp_remote_path'],
                                ],
                            ],
                        ],
                        'cast_audio_upload_ftp_remote_url' => [
                            'title' => 'upload_ftp_remote_url',
                            'fields' => [
                                'cast_audio_upload_ftp_remote_url' => [
                                    'type' => 'text',
                                    'value' => $data['cast_audio_upload_ftp_remote_url'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            $this->ft->display_settings($data)
        );
    }

    public function testSaveSettings() : void
    {
        $data = $this->ft->save_settings(null);

        self::assertTrue(is_array($data));

        self::assertSame(
            ['cast_path' => 'testPath'],
            $this->ft->save_settings([
                'test1' => 'test1',
                'cast_path' => 'testPath',
            ])
        );
    }

    public function testDisplayField() : void
    {
        $templatingService = Di::diContainer()->get(TemplatingService::class);

        $output = $templatingService->render('CastAudioField', [
            'csrfTokenName' => 'csrf_token',
            'csrfToken' => CSRF_TOKEN,
            'uploadKey' => $this->uploadKey,
            'uploadUrl' => 'testUploadActionUrl',
            'fieldName' => '',
            'audioFileName' => '',
        ]);

        // Test having output in the buffer
        ob_start();

        $echoContent = 'testing output';

        echo $echoContent;

        $cssFileTime = filemtime(PATH_THIRD_THEMES . 'cast/css/style.min.css');

        $this->eeCp->expects(self::at(0))
            ->method('add_to_head')
            ->with(
                self::equalTo(
                    '<link rel="stylesheet" href="https://test.com/themes/cast/css/style.min.css?v=' . $cssFileTime . '">'
                )
            );

        $this->eeCp->expects(self::at(1))
            ->method('add_to_foot')
            ->with(
                self::equalTo(
                    '<script src="https://cdn.jsdelivr.net/npm/vue@2.6.10/dist/vue.min.js"></script>'
                )
            );

        $this->eeCp->expects(self::at(2))
            ->method('add_to_foot')
            ->with(
                self::equalTo(
                    '<script src="https://unpkg.com/axios/dist/axios.min.js"></script>'
                )
            );

        $jsFileTime = filemtime(PATH_THIRD_THEMES . 'cast/js/main.js');

        $this->eeCp->expects(self::at(3))
            ->method('add_to_foot')
            ->with(
                self::equalTo(
                    '<script type="module" src="https://test.com/themes/cast/js/main.js?v=' . $jsFileTime . '"></script>'
                )
            );

        self::assertSame(
            $output,
            $this->ft->display_field('')
        );

        $oldContent = ob_get_clean();

        self::assertEquals($oldContent, $echoContent);
    }

    public function testValidateNoUploadPath() : void
    {
        $data = ['foo' => 'bar'];

        self::assertTrue($this->ft->validate($data));
    }

    public function testValidateInvalidUploadPath() : void
    {
        $data = [
            'foo' => 'bar',
            'cast_upload_path' => '/foo/bar',
        ];

        $this->eeLang->expects(self::once())
            ->method('line')
            ->with(self::equalTo('badFileUpload'))
            ->willReturn('fooBadFileUpload');

        self::assertSame(
            'fooBadFileUpload',
            $this->ft->validate($data)
        );
    }

    public function testValidateValidUploadPath() : void
    {
        $data = [
            'foo' => 'bar',
            'cast_upload_path' => TESTS_BASE_PATH . '/filesystemTesting/testFile.txt',
        ];

        self::assertTrue($this->ft->validate($data));
    }

    public function testSaveBadData() : void
    {
        $errorClass = new class implements JsonSerializable {
            public function jsonSerialize() : void
            {
                throw new LogicException('Testing');
            }
        };

        self::assertSame('', $this->ft->save(['foo', $errorClass]));
    }

    public function testSaveNotArray() : void
    {
        self::assertSame('[]', $this->ft->save('fooBar'));
    }

    public function testSave() : void
    {
        self::assertSame('{"foo":"bar"}', $this->ft->save(['foo' => 'bar']));
    }

    public function testPostSaveBadData() : void
    {
        $this->filesystem->expects(self::never())->method(self::anything());

        $this->ft->post_save('');
    }

    /**
     * @throws Throwable
     */
    public function testPostSaveNoUploadFile() : void
    {
        $this->filesystem->expects(self::never())->method(self::anything());

        $this->ft->post_save(json_encode([
            'cast_upload_path' => '',
            'cast_file_name' => 'fooBar',
        ]));
    }

    /**
     * @throws Throwable
     */
    public function testPostSaveNoFileName() : void
    {
        $this->filesystem->expects(self::never())->method(self::anything());

        $this->ft->post_save(json_encode([
            'cast_upload_path' => 'testFilePath',
            'cast_file_name' => '',
        ]));
    }

    /**
     * @throws Throwable
     */
    public function testPostSave() : void
    {
        $this->filesystem->expects(self::at(0))
            ->method('mkdir')
            ->with(
                self::equalTo('/--testNormalize'),
                self::equalTo(0777)
            );
        $this->filesystem->expects(self::at(1))
            ->method('rename')
            ->with(
                self::equalTo('testFilePath'),
                self::equalTo('/--testNormalize/testFileName')
            );

        $this->ft->post_save(json_encode([
            'cast_upload_path' => 'testFilePath',
            'cast_file_name' => 'testFileName',
        ]));
    }
}
