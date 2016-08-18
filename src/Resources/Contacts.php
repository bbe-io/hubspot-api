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

    /**
     * Contacts constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function __call($name, $arguments)
    {
        echo "\nUnknown method: ".$name."\n";
    }

    public function get()
    {
        return Collection::make();
    }

    public function take($count)
    {
        return $this->get()->take($count);
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
}
