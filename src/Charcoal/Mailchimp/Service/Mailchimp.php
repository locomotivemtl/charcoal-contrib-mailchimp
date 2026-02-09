<?php

namespace Charcoal\Mailchimp\Service;

use InvalidArgumentException;

/**
 * Mailchimp API v3 client
 */
class Mailchimp
{
    private ?string $apiKey = null;

    private int $timeout = 10;

    private bool $verifySsl = false;

    public function setVerifySsl(bool $verify): self
    {
        $this->verifySsl = $verify;

        return $this;
    }

    public function verifySsl(): bool
    {
        return $this->verifySsl;
    }

    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function timeout(): int
    {
        return $this->timeout;
    }

    /**
     * @throws InvalidArgumentException When the API key is invalid.
     */
    public function setApiKey(string $key): self
    {
        $split = explode('-', $key);

        if (empty($split[1])) {
            throw new InvalidArgumentException(sprintf(
                'Invalid Mailchimp API key: %s',
                $key
            ));
        }

        $this->apiKey = $key;

        return $this;
    }

    public function apiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * API endpoint from the API key.
     */
    private function apiEndpoint(): string
    {
        $key   = $this->apiKey();
        $split = explode('-', $key);

        return strtr('https://<dc>.api.mailchimp.com/3.0/', [
            '<dc>' => $split[1],
        ]);
    }

    /**
     * Send the actual request
     *
     * @param  string               $verb     PUT, PATCH, GET, POST, DELETE.
     * @param  string               $endpoint Endpoint URL such as `lists/{id}/members`.
     * @param  array<string, mixed> $opts     Arguments to be sent to the endpoint.
     * @return object JSON-decoded response body without headers.
     */
    private function sendRequest(string $verb, string $endpoint, array $opts): object
    {
        $timeout = $this->timeout();
        $url     = $this->apiEndpoint() . $endpoint;
        $ssl     = $this->verifySsl();
        $key     = $this->apiKey();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/vnd.api+json',
            'Content-Type: application/vnd.api+json',
            strtr('Authorization: apikey %key', ['%key' => $key])
        ]);

        // Remove header from response
        curl_setopt($ch, CURLOPT_HEADER, false);

        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $ssl);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, true);

        // Encoded for cURL.
        $encoded = json_encode($opts);

        switch ($verb) {
            case 'post':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);
                break;

            case 'patch':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);
                break;

            case 'put':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);
                break;

            case 'get':
                $query = $url;
                if (!empty($opts)) {
                    $query = $url . '?' . http_build_query($opts, '', '&');
                }

                curl_setopt($ch, CURLOPT_URL, $query);
                break;

            case 'delete':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }

        $response = curl_exec($ch);

        return json_decode($response);
    }

    /**
     * Shorthand to sendRequest('get', $endpoint, $args).
     *
     * @param  string               $endpoint API method.
     * @param  array<string, mixed> $args     Arguments to send to the endpoint.
     * @return object JSON-decoded response body without headers.
     */
    public function get(string $endpoint, array $args = []): object
    {
        return $this->sendRequest('get', $endpoint, $args);
    }

    /**
     * Shorthand to sendRequest('post', $endpoint, $args).
     *
     * @param  string               $endpoint API method.
     * @param  array<string, mixed> $args     Arguments to send to the endpoint.
     * @return object JSON-decoded response body without headers.
     */
    public function post(string $endpoint, array $args = []): object
    {
        return $this->sendRequest('post', $endpoint, $args);
    }

    /**
     * Shorthand to sendRequest('put', $endpoint, $args).
     *
     * @param  string               $endpoint API method.
     * @param  array<string, mixed> $args     Arguments to send to the endpoint.
     * @return object JSON-decoded response body without headers.
     */
    public function put(string $endpoint, array $args = []): object
    {
        return $this->sendRequest('put', $endpoint, $args);
    }

    /**
     * Shorthand to sendRequest('patch', $endpoint, $args).
     *
     * @param  string               $endpoint API method.
     * @param  array<string, mixed> $args     Arguments to send to the endpoint.
     * @return object JSON-decoded response body without headers.
     */
    public function patch(string $endpoint, array $args = []): object
    {
        return $this->sendRequest('patch', $endpoint, $args);
    }

    /**
     * Shorthand to sendRequest('delete', $endpoint, $args).
     *
     * @param  string               $endpoint API method.
     * @param  array<string, mixed> $args     Arguments to send to the endpoint.
     * @return object JSON-decoded response body without headers.
     */
    public function delete(string $endpoint, array $args = []): object
    {
        return $this->sendRequest('delete', $endpoint, $args);
    }
}
