<?php

namespace BBE\HubspotAPI\Resources;

use BBE\HubspotAPI\Http\Client;
use Illuminate\Support\Collection;

class Contacts
{
    /**
     * The HTTP client.
     *
     * @var Client
     */
    private $client;

    private $base_url = '/contacts/v1';

    /**
     * Contacts constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function all($options = [])
    {
        $endpoint = '/lists/all/contacts/all';

        $response = $this->client->request('GET', $this->url($endpoint), $options);
        $json = json_decode($response->getBody());

        return Collection::make($json->contacts);
    }

    public function get()
    {
        return Collection::make();
    }

    public function take($count)
    {
        return $this->all(['query' => ['count' => $count]]);
    }

    public function whereId($id)
    {
        if (is_array($id)) {
            return $this->get();
        }

        return $this->get();
    }

    public function whereEmail($email)
    {
        if (is_array($email)) {
            return $this->get();
        }

        return $this->get();
    }

    public function whereToken($token)
    {
        if (is_array($token)) {
            return $this->get();
        }

        return $this->get();
    }


    private function url(String $url)
    {
        return $this->base_url . $url;
    }

    public function __call($name, $arguments)
    {
        echo "\nUnknown method: " . $name . "\n";
    }
}
