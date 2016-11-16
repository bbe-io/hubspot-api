<?php
namespace BBE\HubspotAPI\Resources\Contracts;

interface CanPostData
{
    /**
     * Perform a post request.
     *
     * @param String $endpoint
     * @param array $options
     */
    public function post($endpoint, array $options = []);
}
