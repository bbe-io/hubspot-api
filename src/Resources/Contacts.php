<?php

namespace BBE\HubspotAPI\Resources;

use BBE\HubspotAPI\Http\Client;
use GuzzleHttp\Psr7\Request;
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
     * Base URL for the endpoint.
     *
     * @var string
     */
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

    /**
     * Perform a get request and return the decoded JSON.
     *
     * @param String $endpoint
     * @param array $options
     * @return mixed
     */
    public function get(String $endpoint, array $options = [])
    {
        $response = $this->client->request('GET', $this->url($endpoint), $options);

        $json = json_decode($response->getBody());

        $contacts = $this->pluckContacts($json);

        return $contacts;
    }

    /**
     * Get all contacts.
     * Hubspot returns a maximum of 100 contacts.
     *
     * TODO: Recursively request pages of contacts.
     *
     * @return Collection
     */
    public function all()
    {
        $endpoint = '/lists/all/contacts/all';

        return $this->get($endpoint, []);
    }

    /**
     * Get a certain count of contacts.
     * Hubspot returns a maximum of 100 contacts.
     *
     * @param $count
     * @return Collection
     */
    public function take($count)
    {
        if ($count > 100) {
            $count = 100;
        }

        $endpoint = '/lists/all/contacts/all';
        $options = ['query' => ['count' => $count]];

        return $this->get($endpoint, $options);
    }

    /**
     * Find one or more contacts from their IDs.
     *
     * @param mixed $ids
     * @return Collection
     */
    public function whereId($ids)
    {
        if (!is_array($ids)) {
            return $this->whereSingleId($ids);
        }

        $endpoint = '/contact/vids/batch/';
        $options = ['query' => [
            'vid' => $ids,
        ]];

        return $this->get($endpoint, $options);
    }

    /**
     * Find a contact from their ID.
     *
     * @param int $id
     * @return Collection
     */
    public function whereSingleId($id)
    {
        $options = [];
        $endpoint = '/contact/vid/' . $id . '/profile';

        return $this->get($endpoint, $options);
    }

    /**
     * Find a single contact from their ID.
     *
     * @param int $id
     * @return Collection
     */
    public function findId($id)
    {
        return $this->whereSingleId($id)->first();
    }

    /**
     * Find one or more contacts from their emails.
     *
     * @param String $emails
     * @return Collection
     */
    public function whereEmail($emails)
    {
        if (!is_array($emails)) {
            return $this->whereSingleEmail($emails);
        }

        $endpoint = '/contact/emails/batch/';
        $options = ['query' => [
            'email' => $emails,
        ]];

        return $this->get($endpoint, $options);
    }

    /**
     * Find a contact from their email.
     *
     * @param String $email
     * @return Collection
     */
    public function whereSingleEmail($email)
    {
        $options = [];
        $endpoint = '/contact/email/' . $email . '/profile';

        return $this->get($endpoint, $options);
    }

    /**
     * Find a single contact from their ID.
     *
     * @param String $email
     * @return Collection
     */
    public function findEmail($email)
    {
        return $this->whereSingleEmail($email)->first();
    }

    /**
     * @param $token
     * @return Contacts
     */
    public function whereToken($token)
    {
    }

    /**
     * Construct endpoint url with the base url.
     *
     * @param String $url
     * @return string
     */
    private function url(String $url)
    {
        return $this->base_url . $url;
    }

    /**
     * Pluck a collection of contacts from a json response.
     *
     * @param $json
     * @return Collection
     */
    private function pluckContacts($json)
    {
        $contacts = isset($json->contacts) ? $json->contacts : $json;

        if ($this->isSingleContact($contacts)) {
            $contacts = [$contacts];
        }

        return Collection::make($contacts);
    }

    /**
     * Check if the object is a single contact.
     *
     * @param $contact
     * @return bool
     */
    private function isSingleContact($contact)
    {
        return isset($contact->vid);
    }
}
