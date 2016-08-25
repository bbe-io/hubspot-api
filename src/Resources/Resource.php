<?php

namespace BBE\HubspotAPI\Resources;

use BBE\HubspotAPI\Http\Client;

abstract class Resource
{
    /**
     * The HTTP client.
     *
     * @var Client
     */
    public $client;

    /**
     * Base URL for the endpoint.
     *
     * @var string
     */
    public $base_url = '';

    /**
     * Contacts constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Construct endpoint url with the base url.
     *
     * @param String $url
     * @return string
     */
    public function url(String $url)
    {
        return $this->base_url.$url;
    }
}
