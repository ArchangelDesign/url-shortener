<?php

namespace App\Services;

use App\Entities\WebsiteAddress;
use App\Exceptions\InvalidHash;
use App\Exceptions\InvalidUrlProvided;
use App\Exceptions\UrlIsTooLong;
use App\Exceptions\UrlNotFound;
use App\Models\Website;

/**
 * Class ShortenerService
 * @package App\Services
 */
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

    /**
     * @param string $url
     * @return bool
     */
    public function isValidUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Generates a random string 8 character long.
     * It is used to identify original URL
     *
     * @return string
     */
    public function generateHash(): string
    {
        return substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstvwxyz', 36)), 0, 8);
    }

    /**
     * Stores generated website
     *
     * @param WebsiteAddress $websiteAddress
     * @throws InvalidHash
     */
    public function storeWebsite(WebsiteAddress $websiteAddress)
    {
        if (empty($websiteAddress->getHash()))
            throw new InvalidHash();

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

    /**
     * Returns an array of all shortened websites
     *
     * @return array
     */
    public function fetchAll(): array
    {
        return Website::all()->toArray();
    }

    /**
     * @param string $url
     * @return bool
     */
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
