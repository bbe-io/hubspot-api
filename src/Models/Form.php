<?php

namespace BBE\HubspotAPI\Models;

use Illuminate\Support\Collection;

class Form extends Model
{
    /**
     * Get the model ID.
     *
     * @param $object
     * @return mixed
     */
    public function getJsonID($object)
    {
        return $object->guid ?: null;
    }

    /**
     * Check if the requested property is an ID.
     *
     * @param $property
     * @return bool
     */
    public function wantsId($property)
    {
        return $property === 'id' || $property === 'guid';
    }

    /**
     * Map property values to their key.
     *
     * @param $object
     * @return Collection
     */
    public function mapProperties($object)
    {
        return Collection::make();
    }
}
