<?php

declare(strict_types=1);

use BuzzingPixel\Cast\Cast\Constants;
use BuzzingPixel\Cast\Cast\Di;
use BuzzingPixel\Cast\Cast\Facade\PhpInternals;
use BuzzingPixel\Cast\Cast\Templating\TemplatingService;
use BuzzingPixel\Cast\Cast\Uploading\UploadKeysServiceContract;
use BuzzingPixel\Cast\ExpressionEngine\Service\ActionsService;
use BuzzingPixel\Cast\ExpressionEngine\Service\NormalizePaths;
use EllisLab\ExpressionEngine\Service\Validation\Factory as ValidationFactory;
use EllisLab\ExpressionEngine\Service\Validation\Result as ValidationResult;
use EllisLab\ExpressionEngine\Service\Validation\Rule\Callback as ValidationRuleCallback;
use Symfony\Component\Filesystem\Filesystem;
use function Safe\filemtime;
use function Safe\json_decode;
use function Safe\json_encode;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

class Cast_audio_ft extends EE_Fieldtype
{
    /** @var EE_Lang */
    private $lang;
    /** @var Cp|null */
    private $eeCp;
    /** @var NormalizePaths */
    private $normalizePaths;
    /** @var ValidationFactory */
    private $validationFactory;
    /** @var PhpInternals */
    private $phpInternals;
    /** @var UploadKeysServiceContract */
    private $uploadKeysService;
    /** @var ActionsService */
    private $actionsService;
    /** @var TemplatingService */
    private $templatingService;
    /** @var Filesystem */
    private $filesystem;

    /** @var mixed[] */
    public $info = [
        'name' => Constants::NAME,
        'version' => Constants::VERSION,
    ];

    public function __construct(
        ?EE_Loader $loader = null,
        ?EE_Lang $lang = null,
        ?Cp $eeCp = null,
        ?NormalizePaths $normalizePaths = null,
        ?ValidationFactory $validationFactory = null,
        ?PhpInternals $phpInternals = null,
        ?UploadKeysServiceContract $uploadKeysService = null,
        ?ActionsService $actionsService = null,
        ?Filesystem $filesystem = null,
        ?TemplatingService $templatingService = null
    ) {
        // @codeCoverageIgnoreStart

        if (! $loader) {
            $loader =  Di::diContainer()->get(EE_Loader::class);
        }

        if (! $lang) {
            $lang = Di::diContainer()->get(EE_Lang::class);
        }

        if ($eeCp) {
            $this->eeCp = $eeCp;
        }

        if (! $eeCp && isset(ee()->cp)) {
            $this->eeCp = ee()->cp;
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

        if (! $uploadKeysService) {
            $uploadKeysService = Di::diContainer()->get(UploadKeysServiceContract::class);
        }

        if (! $actionsService) {
            $actionsService = Di::diContainer()->get(ActionsService::class);
        }

        if (! $filesystem) {
            $filesystem = Di::diContainer()->get(Filesystem::class);
        }

        if (! $templatingService) {
            $templatingService = Di::diContainer()->get(TemplatingService::class);
        }

        // @codeCoverageIgnoreEnd

        $this->lang              = $lang;
        $this->normalizePaths    = $normalizePaths;
        $this->validationFactory = $validationFactory;
        $this->phpInternals      = $phpInternals;
        $this->uploadKeysService = $uploadKeysService;
        $this->actionsService    = $actionsService;
        $this->filesystem        = $filesystem;
        $this->templatingService = $templatingService;

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

        foreach (array_keys($data) as $key) {
            if (mb_strpos($key, 'cast_') === 0) {
                continue;
            }

            unset($data[$key]);
        }

        return $data;
    }

    // phpcs:enable SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint

    /**
     * @param mixed $data
     */
    public function display_field($data) : string
    {
        try {
            $data = is_string($data) ? $data : '';
            $data = json_decode($data, true);
        } catch (Throwable $e) {
            $data = [];
        }

        $this->setCpCssAndJs();

        return $this->templatingService->render('CastAudioField', [
            'csrfTokenName' => 'csrf_token',
            'csrfToken' => defined('CSRF_TOKEN') ? CSRF_TOKEN : '',
            'uploadKey' => $this->uploadKeysService->createKey(),
            'uploadUrl' => $this->actionsService->getUploadActionUrl(),
            'fieldName' => $this->field_name,
            'fileName' => $data['cast_file_name'] ?? '',
            'mimeType' => $data['cast_mime_type'] ?? '',
            'fileSize' => $data['cast_file_size'] ?? '',
        ]);
    }

    /**
     * @param mixed $data
     *
     * @return bool|string
     */
    public function validate($data)
    {
        $data = is_array($data) ? $data : [];

        $uploadFile = $data['cast_upload_path'] ?? '';

        if (! $uploadFile) {
            return true;
        }

        return is_file($uploadFile) ? true : $this->lang->line('badFileUpload');
    }

    /**
     * @param mixed $data
     */
    public function save($data) : string
    {
        try {
            $data = is_array($data) ? $data : [];

            return json_encode($data);
        } catch (Throwable $e) {
            return '';
        }
    }

    /**
     * @param mixed $data
     */
    public function post_save($data) : void
    {
        try {
            $data = is_string($data) ? $data : '';
            $data = json_decode($data, true);
        } catch (Throwable $e) {
            $data = [];
        }

        $uploadFile = $data['cast_upload_path'] ?? '';
        $fileName   = $data['cast_file_name'] ?? '';

        if (! $uploadFile || ! $fileName) {
            return;
        }

        $uploadPath = $this->settings['cast_audio_upload_path'] ?? '';

        $uploadPath = $this->normalizePaths->normalize(
            $uploadPath . DIRECTORY_SEPARATOR . $this->content_id()
        );

        $this->filesystem->mkdir($uploadPath, 0777);

        $this->filesystem->rename(
            $uploadFile,
            $uploadPath . DIRECTORY_SEPARATOR . $fileName
        );
    }

    private function setCpCssAndJs() : void
    {
        $this->setCpCss();
        $this->setCpJs();
    }

    private function setCpCss() : void
    {
        // @codeCoverageIgnoreStart

        if (! $this->eeCp) {
            return;
        }

        // @codeCoverageIgnoreEnd

        $cssFileTime = uniqid();

        $cssPath = PATH_THIRD_THEMES . 'cast/css/style.min.css';

        if (is_file($cssPath)) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $cssFileTime = filemtime($cssPath);
        }

        $css = URL_THIRD_THEMES;

        $css .= 'cast/css/style.min.css?v=' . $cssFileTime;

        $cssTag = '<link rel="stylesheet" href="' . $css . '">';

        $this->eeCp->add_to_head($cssTag);
    }

    private function setCpJs() : void
    {
        // @codeCoverageIgnoreStart

        if (! $this->eeCp) {
            return;
        }

        // @codeCoverageIgnoreEnd

        $this->eeCp->add_to_foot(
            '<script src="https://cdn.jsdelivr.net/npm/vue@2.6.10/dist/vue.min.js"></script>'
        );

        $this->eeCp->add_to_foot(
            '<script src="https://unpkg.com/axios/dist/axios.min.js"></script>'
        );

        $jsFileTime = uniqid();

        $jsPath = PATH_THIRD_THEMES . 'cast/js/main.js';

        if (is_file($jsPath)) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $jsFileTime = filemtime($jsPath);
        }

        $js = URL_THIRD_THEMES;

        $js .= 'cast/js/main.js?v=' . $jsFileTime;

        $jsTag = '<script type="module" src="' . $js . '"></script>';

        $this->eeCp->add_to_foot($jsTag);
    }
}
