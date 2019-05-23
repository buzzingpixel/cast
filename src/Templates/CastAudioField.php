<?php

declare(strict_types=1);

use BuzzingPixel\Cast\Cast\Di;
use BuzzingPixel\Cast\Cast\Language\Translator;
use BuzzingPixel\Cast\Cast\Templating\TemplatingService;

// phpcs:disable Generic.Files.InlineHTML.Found

/** @var TemplatingService $t */
/** @var string $csrfTokenName */
/** @var string $csrfToken */
/** @var string $uploadKey */
/** @var string $uploadUrl */
/** @var string $fieldName */
/** @var string $fileName */
/** @var string $mimeType */
/** @var string $fileSize */

$translator = Di::diContainer()->get(Translator::class);
?>

<div
    ref="CastAudioField"
    data-file-name="<?=$fileName?>"
    data-mime-type="<?=$mimeType?>"
    data-file-size="<?=$fileSize?>"
    data-csrf-token-name="<?=$csrfTokenName?>"
    data-csrf-token="<?=$csrfToken?>"
    data-upload-key="<?=$uploadKey?>"
    data-upload-url="<?=$uploadUrl?>"
    @drag="preventDefault"
    @dragstart="preventDefault"
    @dragover="dragOver"
    @dragenter="dragOver"
    @dragleave="dragLeave"
    @dragend="dragLeave"
    @drop="drop"
>
    <input
        type="hidden"
        name="<?=$fieldName?>[cast_upload_path]"
        v-model="uploadPath"
    >
    <input
        type="hidden"
        name="<?=$fieldName?>[cast_file_name]"
        v-model="fileName"
        value="<?=$fileName?>"
    >
    <input
        type="hidden"
        name="<?=$fieldName?>[cast_mime_type]"
        v-model="mimeType"
        value="<?=$mimeType?>"
    >
    <input
        type="hidden"
        name="<?=$fieldName?>[cast_file_size]"
        v-model="fileSize"
        value="<?=$fileSize?>"
    >
    <div class="CastAudioField__MainWrap">
        <div
            class="CastAudioField__UploadIcon"
            v-bind:class="{'CastAudioField__UploadIcon--IsActive': uploadIconIsActive}"
        >
            <span class="CastAudioField__UploadIconWrapper">
                <?=$t->render('Icons/UploadIcon')?>
            </span>
        </div>
        <div
            class="CastAudioField__Uploading"
            v-bind:class="{'CastAudioField__Uploading--IsActive': uploadInProgress}"
        >
            <div class="CastAudioField__UploadingWrapper">
                <?=$t->render('Components/WaitingAnimation')?>
            </div>
        </div>
        <div
            class="CastAudioField__DropImagesToUpload"
            v-bind:class="{'CastAudioField__DropImagesToUpload--IsActive': !hasFile}"
        >
            <?=$translator->getTranslation('dragAudioFileHereToUpload')?>
        </div>
        <div
            class="CastAudioField__FileDisplay"
            v-bind:class="{'CastAudioField__FileDisplay--IsActive': hasFile}"
        >
            <div class="CastAudioField__FileDisplayInner">
                <span
                    class="CastAudioField__RemoveFile"
                    v-on:click="removeFile"
                >
                    <?=$t->render('Icons/CloseIcon')?>
                </span>
                <span class="CastAudioField__AudioFileIcon">
                    <?=$t->render('Icons/SoundFileIcon')?>
                </span>
                <span class="CastAudioField__AudioFileName">{{ fileName }}</span>
            </div>
        </div>
    </div>
</div>
