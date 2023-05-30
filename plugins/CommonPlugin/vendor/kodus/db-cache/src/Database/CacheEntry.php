<?php

namespace Kodus\Cache\Database;

class CacheEntry
{
    /**
     * @var string
     */
    public $key;

    /**
     * @var string serialized data
     */
    public $data;

    /**
     * @var int expiration timestamp
     */
    public $expires;

    public function __construct(string $key, string $data, int $expires)
    {
        $this->key = $key;
        $this->data = $data;
        $this->expires = $expires;
    }
}
