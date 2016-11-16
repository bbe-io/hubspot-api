<?php
namespace BBE\HubspotAPI;

use BBE\HubspotAPI\Http\Client;

class Factory
{
    /**
     * The HTTP client.
     *
     * @var Client
     */
    protected $client;

    /**
     * Factory constructor.
     *
     * @param String $api_key
     * @param array $config
     */
    public function __construct($api_key, $config = [])
    {
        // Default request options
        $config = array_merge([
            'base_uri' => 'https://api.hubapi.com',
            'connect_timeout' => 3,
            'timeout' => 5,
        ], $config);

        $this->client = new Client($api_key, $config);
    }

    /**
     * Static constructor.
     *
     * @param String $api_key
     * @param array $config
     * @return static
     */
    public static function connect($api_key, $config = [])
    {
        return new static($api_key, $config);
    }

    public function __call($name, $arguments)
    {
        $resource = 'BBE\\HubspotAPI\\Resources\\'.ucfirst($name);

        return new $resource($this->client);
    }
}
