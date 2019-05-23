<?php

declare(strict_types=1);

namespace BuzzingPixel\Cast\Cast\Uploading;

use League\Flysystem\Adapter\Ftp;
use League\Flysystem\Filesystem;

class FlysystemFactory
{
    public function makeFtp(FtpConfig $config) : Filesystem
    {
        return new Filesystem(new Ftp($config->toArray()));
    }
}
