<?php

declare(strict_types=1);

use BuzzingPixel\Cast\Cast\Di;
use BuzzingPixel\Cast\Cast\Uploading\UploadFileToCacheLocation;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch

class Cast
{
    /** @var EE_Input */
    private $input;
    /** @var UploadFileToCacheLocation */
    private $uploadFileToCacheLocation;

    /** @noinspection PhpUnusedParameterInspection */

    /**
     * I don't know what the crap this first argument EE is passing here
     *
     * @param mixed $whatIsThis
     */
    public function __construct(
        $whatIsThis = 0,
        ?EE_Input $input = null,
        ?UploadFileToCacheLocation $uploadFileToCacheLocation = null
    ) {
        // @codeCoverageIgnoreStart

        if (! $input) {
            $input = Di::diContainer()->get(EE_Input::class);
        }

        if (! $uploadFileToCacheLocation) {
            $uploadFileToCacheLocation = Di::diContainer()->get(UploadFileToCacheLocation::class);
        }

        // @codeCoverageIgnoreEnd

        $this->input                     = $input;
        $this->uploadFileToCacheLocation = $uploadFileToCacheLocation;
    }

    public function uploadAudioFile() : void
    {
        $file = $_FILES['file'] ?? [];
        $file = is_array($file) ? $file : [];

        $this->uploadFileToCacheLocation->upload(
            (string) $this->input->post('upload_key'),
            $file
        );
    }
}
