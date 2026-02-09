<?php

namespace Charcoal\Mailchimp\Resources\Lists;

use Charcoal\Mailchimp\Resources\Lists;
use RuntimeException;

/**
 * Mailchimp Members API facade
 *
 * Create, Get, Edit or Delete a member
 *
 * {@link https://developer.mailchimp.com/documentation/mailchimp/reference/lists/members/}
 */
class Members extends Lists
{
    public const API_ENDPOINT = '/members';

    protected function apiEndpoint(?string $userHash = null): string
    {
        if (!$this->listId()) {
            throw new RuntimeException(
                'No list ID defined.'
            );
        }

        $endpoint  = parent::apiEndpoint();
        $endpoint .= self::API_ENDPOINT;

        if ($userHash) {
            $endpoint = strtr($endpoint . '/{user_hash}', [
                '{user_hash}' => $userHash,
            ]);
        }

        return $endpoint;
    }

    /**
     * Get subscriber hash as requested in the Mailchimp API documentation
     */
    protected function subscriberHash(string $email): string
    {
        return md5(strtolower($email));
    }

    /**
     * Update a user
     *
     * @param array<string, mixed> $queryParameters
     */
    public function update(string $email, array $queryParameters): object
    {
        $endpoint = $this->apiEndpoint($this->subscriberHash($email));
        $results  = $this->mailchimp()->patch($endpoint, $queryParameters);

        return $results;
    }

    /**
     * @param array<string, mixed> $queryParameters
     */
    public function add(array $queryParameters): object
    {
        if (empty($queryParameters['email_address'])) {
            throw new \InvalidArgumentException(
                'Missing parameter \'email_address\' for user creation'
            );
        }

        if (empty($queryParameters['status'])) {
            throw new \InvalidArgumentException(
                'Missing parameter \'status\' for user creation'
            );
        }

        $endpoint = $this->apiEndpoint();
        $results  = $this->mailchimp()->post($endpoint, $queryParameters);

        return $results;
    }

    /**
     * Get a member or all members from the list
     *
     * Add filters in queryParameters according to the doc.
     *
     * @param array<string, mixed>|string|null $arg
     * @param array<string, mixed>             $queryParameters
     */
    public function get(array|string|null $arg = null, array $queryParameters = []): object
    {
        $endpoint = $this->apiEndpoint();

        if (is_string($arg)) {
            $endpoint = $this->apiEndpoint(
                $this->subscriberHash($arg)
            );
        }

        if (is_array($arg)) {
            $queryParameters = $arg;
        }

        $results = $this->mailchimp()->get($endpoint, $queryParameters);

        return $results;
    }

    /**
     * Add or update a user
     *
     * @param array<string, mixed> $queryParameters
     */
    public function addOrUpdate(array $queryParameters): object
    {
        if (empty($queryParameters['email_address'])) {
            throw new RuntimeException(
                'No user ID (subscriber_hash) nor email defined.'
            );
        }

        $endpoint = $this->apiEndpoint(
            $this->subscriberHash($queryParameters['email_address'])
        );
        $results = $this->mailchimp()->put($endpoint, $queryParameters);

        return $results;
    }

    /**
     * Remove a user from a list
     */
    public function remove(string $email): object
    {
        $subscriberHash = $this->subscriberHash($email);
        $endpoint = $this->apiEndpoint($subscriberHash);
        return $this->mailchimp()->delete($endpoint);
    }

    /**
     * Delete permanently a user
     */
    public function deletePermanent(string $email): object
    {
        $endpoint = $this->apiEndpoint(
            $this->subscriberHash($email)
        ) . '/actions/delete-permanent';

        return $this->mailchimp()->delete($endpoint);
    }
}
