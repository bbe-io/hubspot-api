<?php

namespace BBE\HubspotAPI\Resources\Helpers;

use Illuminate\Support\Collection;

trait RetrieveData
{
    /**
     * Perform a get request and return a Collection of models.
     *
     * @param String $endpoint
     * @param array $options
     * @return Collection
     */
    public function get(String $endpoint, array $options = [])
    {
        $response = $this->client->request('GET', $this->url($endpoint), $options);

        $json = json_decode($response->getBody());

        $contacts = $this->pluckResources($json)->map(function ($contact) {
            return $this->mapToModel($contact);
        });

        return $contacts;
    }
}
