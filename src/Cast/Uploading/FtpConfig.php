<?php

declare(strict_types=1);

namespace BuzzingPixel\Cast\Cast\Uploading;

class FtpConfig
{
    /** @var string */
    private $host;

    /** @var string */
    private $username;

    /** @var string */
    private $password;

    /** @var int */
    private $port;

    /** @var string */
    private $root;

    /** @var bool */
    private $passive;

    /** @var bool */
    private $ssl;

    /** @var int */
    private $timeout;

    /**
     * @param mixed[] $config
     */
    public function __construct(array $config = [])
    {
        $this->host = (string) ($config['host'] ?? '');

        $this->username = (string) ($config['username'] ?? '');

        $this->password = (string) ($config['password'] ?? '');

        $this->port = (int) ($config['port'] ?? 21);

        $this->root = (string) ($config['root'] ?? '');

        $this->passive = ($config['root'] ?? true);
        $this->passive = $this->passive === true;

        $this->ssl = ($config['root'] ?? false);
        $this->ssl = $this->ssl === true;

        $this->timeout = (int) ($config['timeout'] ?? 30);
    }

    /**
     * @return mixed[]
     */
    public function toArray() : array
    {
        $array = [];

        if ($this->host) {
            $array['host'] = $this->host;
        }

        if ($this->username) {
            $array['username'] = $this->username;
        }

        if ($this->password) {
            $array['password'] = $this->password;
        }

        if ($this->port) {
            $array['port'] = $this->port;
        }

        if ($this->root) {
            $array['root'] = $this->root;
        }

        $array['passive'] = $this->passive;

        $array['ssl'] = $this->ssl;

        $array['timeout'] = $this->timeout;

        return $array;
    }
}
