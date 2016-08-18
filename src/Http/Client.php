<?php

namespace BBE\HubspotAPI\Http;

use GuzzleHttp\Client as GuzzleClient;
use function GuzzleHttp\Psr7\build_query;
use GuzzleHttp\Psr7\Request;

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

        // We have to use the query builder because Hubspot doesn't like proper encoding
        $options['query'] = build_query($options['query']);

        return parent::request($method, $uri, $options);
    }
}
