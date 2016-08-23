<?php

namespace BBE\HubspotAPI\Resources;

use BBE\HubspotAPI\Models\Contact;
use BBE\HubspotAPI\Resources\Contracts\CanPostData;
use BBE\HubspotAPI\Resources\Contracts\CanRetrieveData;
use BBE\HubspotAPI\Resources\Helpers\PostData;
use BBE\HubspotAPI\Resources\Helpers\RetrieveData;
use Illuminate\Support\Collection;

class Contacts extends Resource implements CanRetrieveData, CanPostData
{
    use RetrieveData, PostData;

    /**
     * Base URL for the endpoint.
     *
     * @var string
     */
    public $base_url = '/contacts/v1';

    /**
     * Find a single resource model.
     *
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return $this->findWithId($id);
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
        return $this->get('/lists/all/contacts/all');
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

        return $this->get($endpoint, $options)->take($count);
    }

    /**
     * Find one or more contacts from their IDs.
     *
     * @param mixed $ids
     * @return Collection
     */
    public function whereId($ids)
    {
        if (! is_array($ids)) {
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
        return $this->get('/contact/vid/'.$id.'/profile');
    }

    /**
     * Find a single contact from their ID.
     *
     * @param int $id
     * @return Contact
     */
    public function findWithId($id)
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
        if (! is_array($emails)) {
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
    public function whereSingleEmail(String $email)
    {
        return $this->get('/contact/email/'.$email.'/profile');
    }

    /**
     * Find a single contact from their ID.
     *
     * @param String $email
     * @return Contact
     */
    public function findWithEmail(String $email)
    {
        return $this->whereSingleEmail($email)->first();
    }

    /**
     * @param $tokens
     * @return Contacts
     */
    public function whereToken($tokens)
    {
        if (! is_array($tokens)) {
            return $this->whereSingleToken($tokens);
        }

        $endpoint = '/contact/utks/batch/';
        $options = ['query' => [
            'utk' => $tokens,
        ]];

        return $this->get($endpoint, $options);
    }

    /**
     * Find a contact from their email.
     *
     * @param String $token
     * @return Collection
     */
    public function whereSingleToken(String $token)
    {
        return $this->get('/contact/utk/'.$token.'/profile');
    }

    /**
     * Find a single contact from their ID.
     *
     * @param String $token
     * @return Contact
     */
    public function findWithToken($token)
    {
        return $this->whereSingleToken($token)->first();
    }

    /**
     * Pluck a collection of contacts from a json response.
     *
     * @param $json
     * @return Collection
     */
    public function pluckResources($json)
    {
        $contacts = isset($json->contacts) ? $json->contacts : $json;

        if ($this->isSingleResource($contacts)) {
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
    public function isSingleResource($contact)
    {
        return isset($contact->vid);
    }

    /**
     * Create a Contact model from contact data.
     *
     * @param $contact
     * @return Contact
     */
    public function mapToModel($contact)
    {
        return new Contact($this, $contact);
    }
}
