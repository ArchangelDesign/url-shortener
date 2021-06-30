<?php

namespace App\Entities;

class WebsiteAddress
{
    private $url;

    private $hash;

    public function __construct(string $url, ?string $hash = null)
    {
        $this->url = $url;
        $this->hash = $hash;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string|null
     */
    public function getHash(): ?string
    {
        return $this->hash;
    }
}
