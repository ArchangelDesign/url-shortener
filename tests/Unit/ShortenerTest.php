<?php

namespace Tests\Unit;

use App\Entities\WebsiteAddress;
use App\Exceptions\InvalidUrlProvided;
use App\Exceptions\UrlIsTooLong;
use App\Services\ShortenerService;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\App;
use Tests\CreatesApplication;

class ShortenerTest extends TestCase
{
    use CreatesApplication;

    /**
     * @covers \App\Services\ShortenerService::createAddress
     * @throws \App\Exceptions\InvalidUrlProvided
     * @throws \App\Exceptions\UrlIsTooLong
     */
    public function testUrlValidation(): void
    {
        /** @var ShortenerService $shortener */
        $shortener = App::make(ShortenerService::class);
        $url = $shortener->createAddress('https://my-website.com');
        $this->assertInstanceOf(WebsiteAddress::class, $url);
        $this->assertEquals(8, strlen($url->getHash()));
    }

    /**
     * @covers \App\Services\ShortenerService::websiteExists
     * @covers \App\Services\ShortenerService::deleteWebsite
     * @covers \App\Services\ShortenerService::storeWebsite
     * @throws \App\Exceptions\InvalidUrlProvided
     * @throws \App\Exceptions\UrlIsTooLong
     * @throws \App\Exceptions\UrlNotFound
     */
    public function testStoringAndDeletingEntries(): void
    {
        /** @var ShortenerService $shortener */
        $shortener = App::make(ShortenerService::class);
        $url = 'https://google.com/my-website-' . $shortener->generateHash();
        if ($shortener->websiteExists($url)) {
            $shortener->deleteWebsite(new WebsiteAddress($url));
        }
        $address = $shortener->createAddress($url);
        $shortener->storeWebsite($address);
        $this->assertTrue($shortener->websiteExists($url), 'Entry doesn\'t exist after creating.');
        $hash = $shortener->fetchWebsiteHash($address);
        $this->assertEquals($address->getHash(), $hash, 'Generated hash mismatch.');
        $shortener->deleteWebsite($address);
        $this->assertFalse($shortener->websiteExists($url), 'Entry exists after deleting.');
    }

    /**
     * @covers \App\Exceptions\InvalidUrlProvided
     * @throws InvalidUrlProvided
     * @throws \App\Exceptions\UrlIsTooLong
     */
    public function testCreateAddressThrowsInvalid(): void
    {
        /** @var ShortenerService $shortener */
        $shortener = App::make(ShortenerService::class);
        $this->expectException(InvalidUrlProvided::class);
        $shortener->createAddress('');
    }

    /**
     * @covers \App\Exceptions\UrlIsTooLong
     * @throws InvalidUrlProvided
     * @throws UrlIsTooLong
     */
    public function testCreateAddressThrowsForUrlTooLong(): void
    {
        /** @var ShortenerService $shortener */
        $shortener = App::make(ShortenerService::class);
        $this->expectException(UrlIsTooLong::class);
        $url = 'https://' . str_repeat('a', ShortenerService::MAX_URL_LENGTH) . '.com';
        $shortener->createAddress($url);
    }
}
