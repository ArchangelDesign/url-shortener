<?php

namespace App\Services;

use App\Entities\WebsiteAddress;
use App\Exceptions\InvalidUrlProvided;
use App\Exceptions\UrlIsTooLong;
use App\Exceptions\UrlNotFound;
use App\Models\Website;

class ShortenerService
{
    const MAX_URL_LENGTH = 250;

    public function __construct()
    {
    }

    /**
     * @param string $url
     * @return WebsiteAddress
     * @throws InvalidUrlProvided
     * @throws UrlIsTooLong
     */
    public function createAddress(string $url): WebsiteAddress
    {
        if (empty($url))
            throw new InvalidUrlProvided();

        if (strlen($url) > self::MAX_URL_LENGTH)
            throw new UrlIsTooLong();

        if (!$this->isValidUrl($url))
            throw new InvalidUrlProvided();

        return new WebsiteAddress($url, $this->generateHash());
    }

    public function isValidUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    public function normalize(string $url): string
    {
        $parsed = parse_url($url, PHP_URL_HOST);
        if ($parsed === false)
            throw new InvalidUrlProvided();

        return $parsed;
    }

    public function generateHash(): string
    {
        return substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstvwxyz', 36)), 0, 8);
    }

    public function storeWebsite(WebsiteAddress $websiteAddress)
    {
        Website::create(['url' => $websiteAddress->getUrl(), 'hash' => $websiteAddress->getHash()]);
    }

    /**
     * @param WebsiteAddress $address
     * @return string
     * @throws UrlNotFound
     */
    public function fetchWebsiteHash(WebsiteAddress $address): string
    {
        $entry = Website::where('url', '=', $address->getUrl())->get();
        if ($entry->count() == 0)
            throw new UrlNotFound();
        return $entry->first()['hash'];
    }

    /**
     * @param WebsiteAddress $address
     * @throws UrlNotFound
     */
    public function deleteWebsite(WebsiteAddress $address): void
    {
        $hash = $this->fetchWebsiteHash($address);
        Website::find($hash)->delete();
    }

    public function fetchAll(): array
    {
        return Website::all()->toArray();
    }

    public function websiteExists(string $url): bool
    {
        try {
            $this->fetchWebsiteHash(new WebsiteAddress($url));
            return true;
        } catch (UrlNotFound $e) {
            return false;
        }
    }
}
