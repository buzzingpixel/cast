<?php

declare(strict_types=1);

define('PATH_THIRD', 'pathThirdTest');
define('PATH_THIRD_THEMES', 'pathThirdThemesTest');
define('URL_THIRD_THEMES', 'urlThirdThemesTest');
define('SYSPATH', __DIR__ . '/work/testing/ee3/system/');
define('BASEPATH', SYSPATH . 'ee/legacy/');

/**
 * @return mixed
 */
function ee()
{
    throw new RuntimeException("We can't test ee() so we should not call it directly");
}
