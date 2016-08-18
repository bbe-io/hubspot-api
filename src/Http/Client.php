<?php

namespace BBE\HubspotAPI\Http;

use GuzzleHttp\Client as GuzzleClient;

class Client extends GuzzleClient
{
    /**
     * Hubspot API key.
     *
     * @var String
     */
    private $api_key;

    /**
     * Client constructor.
     *
     * @param String $api_key
     * @param array $config
     */
    public function __construct(String $api_key, array $config = [])
    {
        $this->api_key = $api_key;

        parent::__construct($config);
    }

    /**
     * Perform a Guzzle request.
     * Merge the Hubspot API key into the request query.
     *
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface|void
     */
    public function request($method, $uri = '', array $options = [])
    {
        $options = array_merge_recursive([
            'query' => ['hapikey' => $this->api_key],
        ], $options);

        return parent::request($method, $uri, $options);
    }
}
