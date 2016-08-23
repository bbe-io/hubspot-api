<?php

namespace BBE\HubspotAPI\Resources\Helpers;

trait PostData
{
    /**
     * Perform a post request.
     *
     * @param String $endpoint
     * @param array $options
     */
    public function post(String $endpoint, array $options = [])
    {
        $response = $this->client->request('POST', $this->url($endpoint), $options);

        return $response;
    }
}
