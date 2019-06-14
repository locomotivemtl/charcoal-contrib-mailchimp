<?php

namespace Charcoal\Mailchimp\Resources\Lists;

use Charcoal\Mailchimp\Resources\Lists;

/**
 * Class Members
 * Create, Get, Edit or Delete a member
 *
 * @see https://developer.mailchimp.com/documentation/mailchimp/reference/lists/members/
 * @package Charcoal\Mailchimp\Resources\Lists
 */
class Members extends Lists
{
    const API_ENDPOINT = '/members';

    /**
     * @return string
     */
    protected function apiEndpoint($userHash = null)
    {
        if (!$this->listId()) {
            throw new \RuntimeException(
                'No list ID defined.'
            );
        }

        $endpoint = parent::apiEndpoint();
        $endpoint .= self::API_ENDPOINT;

        if ($userHash) {
            $endpoint = strtr($endpoint.'/{user_hash}', [
                '{user_hash}' => $userHash
            ]);
        }

        return $endpoint;
    }

    /**
     * Get subscriber hash as requested in the mailchimp api documentation
     *
     * @param $email
     */
    protected function subscriberHash($email)
    {
        return md5(strtolower($email));
    }

    /**
     * Update a user
     *
     * @param array $queryParameters
     * @return mixed
     */
    public function update($email, array $queryParameters)
    {
        $endpoint = $this->apiEndpoint($this->subscriberHash($email));
        $results = $this->mailchimp()->patch($endpoint, $queryParameters);
        return $results;
    }

    /**
     *
     *
     * @param array $queryParameters
     * @return mixed
     */
    public function add(array $queryParameters)
    {
        if (!isset($queryParameters['email_address'])) {
            throw new \InvalidArgumentException(
                'Missing parameter \'email_address\' for user creation'
            );
        }

        if (!isset($queryParameters['status'])) {
            throw new \InvalidArgumentException(
                'Missing parameter \'status\' for user creation'
            );
        }

        $endpoint = $this->apiEndpoint();
        $results = $this->mailchimp()->post($endpoint, $queryParameters);

        return $results;
    }

    /**
     * Get a member or all members from the list
     * Add filters in queryParameters according to the doc.
     *
     * @param mixed $arg
     * @param array $queryParameters
     * @return mixed
     */
    public function get($arg = null, $queryParameters = [])
    {
        $endpoint = $this->apiEndpoint();

        // Is an email
        if (is_string($arg)) {
            $endpoint = $this->apiEndpoint($this->subscriberHash($arg));
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
     * @param array $queryParameters
     * @return mixed
     */
    public function addOrUpdate(array $queryParameters)
    {
        if (!isset($queryParameters['email_address'])) {
            throw new \RuntimeException(
                'No user ID (subscriber_hash) nor email defined.'
            );
        }

        $endpoint = $this->apiEndpoint($this->subscriberHash($queryParameters['email_address']));
        $results = $this->mailchimp()->put($endpoint, $queryParameters);
        return $results;
    }

    /**
     * Remove a user from a list
     *
     * @param $email
     * @return mixed
     */
    public function remove($email)
    {
        $subscriberHash = $this->subscriberHash($email);
        $endpoint = $this->apiEndpoint($subscriberHash);
        return $this->mailchimp()->delete($endpoint);
    }

    /**
     * Delete permanently a user
     *
     * @param $email
     * @return mixed
     */
    public function deletePermanent($email)
    {
        $subscriberHash = $this->subscriberHash($email);
        $endpoint = $this->apiEndpoint($subscriberHash).'/actions/delete-permanent';
        return $this->mailchimp()->delete($endpoint);
    }
}
