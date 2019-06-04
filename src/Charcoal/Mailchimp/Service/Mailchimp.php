<?php

namespace Charcoal\Mailchimp\Service;

use Exception;
use RuntimeException;
use stdClass;

/**
 * Mailchimp helper
 * Connects to api v3 of mailchimp.
 */
class Mailchimp
{
    /**
     * @var integer
     */
    private $timeout = 10;

    /**
     * @var boolean
     */
    private $verifySsl = false;

    /**
     * Api key used to connect to the mail chimp api.
     *
     * @var string $apiKey
     */
    protected $apiKey;

    /**
     * @param boolean $bool Verify ssl state.
     * @return self
     */
    public function setVerifySsl($bool)
    {
        $this->verifySsl = $bool;

        return $this;
    }

    /**
     * @return boolean Verify ssl.
     */
    public function verifySsl()
    {
        return $this->verifySsl;
    }

    /**
     * @param integer $timeout Timeout.
     * @return self
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * @return integer Timeout.
     */
    public function timeout()
    {
        return $this->timeout;
    }

    /**
     * @param string $key Api key.
     * @return self
     * @throws RuntimeException When the api key is invalid.
     */
    public function setApiKey($key)
    {
        $split = explode('-', $key);

        if (!isset($split[1])) {
            throw new RuntimeException(sprintf('Invalid mailchimp api key: %s', $key));
        }

        $this->apiKey = $key;

        return $this;
    }

    /**
     * @return string Api key.
     * @throws Exception When no api key is defined.
     */
    public function apiKey()
    {
        if (!$this->apiKey) {
            throw new Exception('No api key defined.');
        }

        return $this->apiKey;
    }

    /**
     * Api endpoint from the api key.
     *
     * @return string Route to api.
     */
    private function apiEndpoint()
    {
        $key   = $this->apiKey();
        $split = explode('-', $key);
        $dc    = $split[1];

        return strtr('https://<dc>.api.mailchimp.com/3.0/', ['<dc>' => $dc]);
    }

    /**
     * Send the actual request
     * @param string $verb   Put,Patch,Get,Post,Delete.
     * @param string $method Method URL such as lists/{id}/members.
     * @param array  $opts   Arguments to be sent to the endpoint.
     * @return stdClass      Json_decode response without headers.
     */
    private function sendRequest($verb, $method, array $opts)
    {
        $timeout = $this->timeout();
        $url     = $this->apiEndpoint().$method;
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

        // Encoded for curl.
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
                    $query = http_build_query($opts, '', '&');
                    $query .= '?'.$query;
                }
                curl_setopt($ch, CURLOPT_URL, $query);
                break;

            case 'delete':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }

        $response = curl_exec($ch);

        curl_close($ch);

        return json_decode($response);
    }

    /**
     * Shorthand to sendRequest('get', $method, $args).
     *
     * @param string $method Api method.
     * @param array  $args   Arguments to send to the endpoint.
     * @return stdClass      Json decoded response without headers.
     */
    public function get($method, array $args = [])
    {
        return $this->sendRequest('get', $method, $args);
    }

    /**
     * Shorthand to sendRequest('post', $method, $args).
     *
     * @param string $method Api method.
     * @param array  $args   Arguments to send to the endpoint.
     * @return stdClass       Json decoded response without headers.
     */
    public function post($method, array $args = [])
    {
        return $this->sendRequest('post', $method, $args);
    }

    /**
     * Shorthand to sendRequest('put', $method, $args).
     *
     * @param string $method Api method.
     * @param array  $args   Arguments to send to the endpoint.
     * @return stdClass       Json decoded response without headers.
     */
    public function put($method, array $args = [])
    {
        return $this->sendRequest('put', $method, $args);
    }

    /**
     * Shorthand to sendRequest('patch', $method, $args).
     *
     * @param string $method Api method.
     * @param array  $args   Arguments to send to the endpoint.
     * @return stdClass       Json decoded response without headers.
     */
    public function patch($method, array $args = [])
    {
        return $this->sendRequest('patch', $method, $args);
    }

    /**
     * Shorthand to sendRequest('delete', $method, $args).
     *
     * @param string $method Api method.
     * @param array  $args   Arguments to send to the endpoint.
     * @return stdClass       Json decoded response without headers.
     */
    public function delete($method, array $args = [])
    {
        return $this->sendRequest('delete', $method, $args);
    }
}
