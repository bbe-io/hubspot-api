<?php

namespace BBE\HubspotAPI\Resources;

use BBE\HubspotAPI\Models\Form;
use BBE\HubspotAPI\Resources\Contracts\CanRetrieveData;
use BBE\HubspotAPI\Resources\Helpers\RetrieveData;
use Illuminate\Support\Collection;

class Forms extends Resource implements CanRetrieveData
{
    use RetrieveData;

    /**
     * Base URL for the endpoint.
     *
     * @var string
     */
    public $base_url = '/forms/v2';

    /**
     * Find a single resource model.
     *
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        $this->findWithId($id);
    }

    /**
     * Get all contacts.
     * Hubspot returns a maximum of 100 contacts.
     *
     * @return Collection
     */
    public function all()
    {
        return $this->get('/forms');
    }

    /**
     * Get a certain count of forms.
     *
     * @param $count
     * @return Collection
     */
    public function take($count)
    {
        return $this->get('/forms')->take($count);
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

        $forms = $this->all();

        return $forms->whereIn('id', $ids);
    }

    /**
     * Find a contact from their ID.
     *
     * @param int $id
     * @return Collection
     */
    public function whereSingleId($id)
    {
        $endpoint = '/forms/'.$id;

        return $this->get($endpoint, []);
    }

    /**
     * Find a single contact from their ID.
     *
     * @param int $id
     * @return Form
     */
    public function findWithId($id)
    {
        return $this->whereSingleId($id)->first();
    }

    /**
     * Pluck a collection of contacts from a json response.
     *
     * @param $forms
     * @return Collection
     */
    public function pluckResources($forms)
    {
        if ($this->isSingleResource($forms)) {
            $forms = [$forms];
        }

        return Collection::make($forms);
    }

    /**
     * Check if the object is a single contact.
     *
     * @param $form
     * @return bool
     */
    public function isSingleResource($form)
    {
        return isset($form->guid);
    }

    /**
     * Create a Contact model from contact data.
     *
     * @param $form
     * @return Form
     */
    public function mapToModel($form)
    {
        return new Form($this, $form);
    }
}
