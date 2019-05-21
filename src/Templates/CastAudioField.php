<?php

declare(strict_types=1);

use BuzzingPixel\Cast\Cast\Di;
use BuzzingPixel\Cast\Cast\Language\Translator;
use BuzzingPixel\Cast\Cast\Templating\TemplatingService;

// phpcs:disable Generic.Files.InlineHTML.Found

/** @var TemplatingService $t */

$translator = Di::diContainer()->get(Translator::class);
?>

<div
    ref="CastAudioField"
    data-audio-file-name=""
>
    <div class="CastAudioField__MainWrap">
        <div class="CastAudioField__UploadIcon">
            <span class="CastAudioField__UploadIconWrapper">
                <?=$t->render('Icons/UploadIcon')?>
            </span>
        </div>
        <div class="CastAudioField__Uploading">
            <div class="CastAudioField__UploadingWrapper">
                <?=$t->render('Components/WaitingAnimation')?>
            </div>
        </div>
        <div class="CastAudioField__DropImagesToUpload">
            <?=$translator->getTranslation('dragAudioFileHereToUpload')?>
        </div>
        <div class="CastAudioField__FileDisplay">
            <div class="CastAudioField__FileDisplayInner">
                <span class="CastAudioField__RemoveFile">
                    <?=$t->render('Icons/CloseIcon')?>
                </span>
                <span class="CastAudioField__AudioFileIcon">
                    <?=$t->render('Icons/SoundFileIcon')?>
                </span>
                <span class="CastAudioField__AudioFileName">TODO: File Name</span>
            </div>
        </div>
    </div>
</div>
