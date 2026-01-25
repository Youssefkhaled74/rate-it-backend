<?php

namespace Tests\Feature\Admin\Support;

/**
 * Trait for interacting with Admin API in tests.
 * Provides helper methods for making authenticated requests.
 */
trait InteractsWithAdminApi
{
    /**
     * Make an authenticated GET request to the admin API.
     */
    protected function getAsAdmin(string $uri, ?string $token = null): \Illuminate\Testing\TestResponse
    {
        $token = $token ?? $this->adminToken;
        return $this->getJson($uri, $this->adminHeadersWithToken($token));
    }

    /**
     * Make an authenticated POST request to the admin API.
     */
    protected function postAsAdmin(string $uri, array $data = [], ?string $token = null): \Illuminate\Testing\TestResponse
    {
        $token = $token ?? $this->adminToken;
        return $this->postJson($uri, $data, $this->adminHeadersWithToken($token));
    }

    /**
     * Make an authenticated PUT request to the admin API.
     */
    protected function putAsAdmin(string $uri, array $data = [], ?string $token = null): \Illuminate\Testing\TestResponse
    {
        $token = $token ?? $this->adminToken;
        return $this->putJson($uri, $data, $this->adminHeadersWithToken($token));
    }

    /**
     * Make an authenticated PATCH request to the admin API.
     */
    protected function patchAsAdmin(string $uri, array $data = [], ?string $token = null): \Illuminate\Testing\TestResponse
    {
        $token = $token ?? $this->adminToken;
        return $this->patchJson($uri, $data, $this->adminHeadersWithToken($token));
    }

    /**
     * Make an authenticated DELETE request to the admin API.
     */
    protected function deleteAsAdmin(string $uri, array $data = [], ?string $token = null): \Illuminate\Testing\TestResponse
    {
        $token = $token ?? $this->adminToken;
        return $this->deleteJson($uri, $data, $this->adminHeadersWithToken($token));
    }

    /**
     * Make a request to admin API without authentication.
     */
    protected function getAsGuest(string $uri): \Illuminate\Testing\TestResponse
    {
        return $this->getJson($uri, [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Make a POST request to admin API without authentication.
     */
    protected function postAsGuest(string $uri, array $data = []): \Illuminate\Testing\TestResponse
    {
        return $this->postJson($uri, $data, [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]);
    }
}
