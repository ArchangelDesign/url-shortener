<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidUrlProvided;
use App\Exceptions\UrlIsTooLong;
use App\Exceptions\UrlNotFound;
use App\Models\Website;
use App\Services\ShortenerService;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    public function listWebsites(ShortenerService $shortenerService)
    {
        return response($shortenerService->fetchAll(), 200);
    }

    public function createWebsite(Request $request, ShortenerService $shortenerService)
    {
        $entity = $request->post();
        if (!isset($entity['url'])) {
            return response(['message' => 'Missing `url` in request'], 400);
        }

        try {
            $address = $shortenerService->createAddress($entity['url']);
        } catch (InvalidUrlProvided | UrlIsTooLong $e) {
            return response(['message' => 'Invalid URL provided'], 400);
        }

        try {
            $hash = $shortenerService->fetchWebsiteHash($address);
            return response(['hash' => $hash], 200);
        } catch (UrlNotFound $e) {
            $shortenerService->storeWebsite($address);
            return response(['hash' => $address->getHash()], 201);
        }
    }

    public function deleteWebsite(Request $request, ShortenerService $shortenerService)
    {
        $entity = $request->post();
        if (!isset($entity['url'])) {
            return response(['message' => 'Request must contain full url'], 400);
        }
        try {
            $address = $shortenerService->createAddress($entity['url']);
            $shortenerService->deleteWebsite($address);
            return response(['message' => 'URL deleted.'], 200);
        } catch (InvalidUrlProvided | UrlIsTooLong $e) {
            return response(['message' => 'Invalid URL provided'], 400);
        } catch (UrlNotFound $e) {
            return response(['message' => 'URL not found'], 404);
        }
    }
}
