<?php

declare(strict_types=1);

namespace BuzzingPixel\Cast\Cast\Request;

class Storage
{
    /** @var mixed[] */
    private $storage = [];

    /**
     * @param mixed $val
     */
    public function set(string $key, $val) : self
    {
        $this->storage[$key] = $val;

        return $this;
    }

    /**
     * @return mixed|null
     */
    public function get(string $key)
    {
        return $this->storage[$key] ?? null;
    }
}
