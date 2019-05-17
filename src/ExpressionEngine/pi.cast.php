<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch

/**
 * This class is here because EE 3 doesn't recognize the add-on as installed
 * if it only has a fieldtype, and that fieldtype isn't ft.cast.php
 * (and we've set ours to ft.cast_audio.php because we'll also have another
 * fieldtype soon)
 */
class Cast
{
}
