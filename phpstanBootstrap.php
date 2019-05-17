<?php

declare(strict_types=1);

define('PATH_THIRD', 'pathThirdTest');

/**
 * @return mixed
 */
function ee()
{
    throw new RuntimeException("We can't test ee() so we should not call it directly");
}
