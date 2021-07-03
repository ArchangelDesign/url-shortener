<?php

namespace Tests\Feature;

use App\Http\Middleware\Authenticate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(null);
    }

    public function testListExistingWebsites()
    {
        $response = $this->get('/api/website/list');
        $this->assertEquals(200, $response->getStatusCode(),
            'List websites endpoint did not return 200. Response code: ' . $response->getStatusCode());
        $responseBody = $response->json();
        $this->assertIsArray($responseBody);
    }

    public function testCreateNewWebsiteEntry(): void
    {
        $url = 'http://localhost.local/my-link';
        try {
            $response = $this->post('/api/website', ['url' => $url]);
            $response->assertStatus(201);
        } finally {
            $this->delete('/api/website', ['url' => $url]);
        }
    }

    public function testCreateExistingWebsiteEntry(): void
    {
        $url = 'https://unique-address.com';
        $this->post('/api/website', ['url' => $url]);
        $response = $this->post('/api/website', ['url' => $url]);
        try {
            $this->assertEquals(200, $response->getStatusCode(), 'Create endpoint did not return 200 for existing entry');
        } finally {
            $this->delete('/api/website', ['url' => $url]);
        }
    }

    public function testDeleteWebsiteEntry(): void
    {
        $url = 'http://localhost.local/my-long-link';
        $response = $this->post('/api/website', ['url' => $url]);
        $this->assertEquals(201, $response->getStatusCode(),
            'Cannot create entry to test its removal.');
        $response = $this->delete('/api/website', ['url' => $url]);
        $this->assertEquals(200, $response->getStatusCode(),
            'Created entry has not been removed.');
    }

    public function testDeletingMissingUrl(): void
    {
        $url = 'https://google.com';
        try {
            $this->delete('/api/website', ['url' => $url]);
        } catch (\Exception $e) {
        }
        $response = $this->delete('/api/website', ['url' => $url]);
        $this->assertEquals(404, $response->getStatusCode(),
            'Delete endpoint did not return 404 for non-existing entry');
    }

    public function testCreateWebsiteEntryAndVerify(): void
    {
        $url = 'http://localhost.local/my-long-link/to/my/website';
        try {
            $this->delete('/api/website', ['url' => $url]);
        } catch (\Exception $e) {
        }
        $response = $this->post('/api/website', ['url' => $url]);
        $body = $response->json();
        $this->assertArrayHasKey('hash', $body,
            'Create entry endpoint did not return hash.');
        $this->assertEquals(8, strlen($body['hash']),
            'Create entry endpoint returned invalid hash length.');
        $response = $this->get('/api/website/list');
        $body = $response->json();
        $this->assertIsArray($body,
            'List endpoint did not return an array.');
        $this->assertNotEmpty($body,
            'Website entry has not been created or not returned by list endpoint.');
    }

    public function testCreateWebsiteReturnsExistingHash(): void
    {
        $url = 'https://google.com';
        try {
            $this->delete('/api/website', ['url' => $url]);
        } catch (\Exception $e) {
        }
        $response = $this->post('/api/website', ['url' => $url]);
        $this->assertEquals(201, $response->getStatusCode(),
            'Create endpoint did not return 201 for new entry.');
        $hash = $response->json('hash');
        $response = $this->post('/api/website', ['url' => $url]);
        $this->assertEquals(200, $response->getStatusCode(),
            'Create endpoint did not return 200 for existing entry.');
        $this->assertEquals($hash, $response->json('hash'),
            'Create endpoint did not return existing hash.');
    }
}
