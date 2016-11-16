<?php
namespace BBE\HubspotAPI\Resources\Contracts;

use Illuminate\Support\Collection;

interface CanRetrieveData
{
    /**
     * Perform a get request and return a Collection of models.
     *
     * @param String $endpoint
     * @param array $options
     * @return Collection
     */
    public function get($endpoint, array $options = []);

    /**
     * Find a single resource model.
     *
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * Pluck a collection of the resource from a json response.
     *
     * @param $json
     * @return Collection
     */
    public function pluckResources($json);

    /**
     * Check if the object is a single resource.
     *
     * @param $contact
     * @return bool
     */
    public function isSingleResource($contact);

    /**
     * Create a resource model from json data.
     *
     * @param $contact
     */
    public function mapToModel($contact);
}
