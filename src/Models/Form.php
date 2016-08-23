<?php

namespace BBE\HubspotAPI\Models;

use BBE\HubspotAPI\Resources\Forms;
use Illuminate\Support\Collection;

class Form
{
    /**
     * The contact ID.
     *
     * @var int
     */
    public $guid;

    /**
     * A collection of properties the contact has.
     *
     * @var Collection
     */
    public $properties;

    /**
     * The Contacts resource.
     *
     * @var Forms
     */
    public $resource;

    /**
     * Contact constructor.
     *
     * @param Forms $resource
     * @param $object
     */
    public function __construct(Forms $resource, $object)
    {
        $this->resource = $resource;

        $this->guid = $object->guid ?: null;
//        $this->properties = $this->mapProperties($object->properties);
//        $this->changes = Collection::make();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (String) $this->guid;
    }

    /**
     * Check if the property is accessible.
     *
     * @param $property
     * @return bool
     */
    public function __isset($property)
    {
        if ($this->wantsId($property)) {
            return true;
        }

        return $this->properties->has($property);
    }

    /**
     * Remove the property from the model.
     *
     * @param $property
     * @return void
     */
    public function __unset($property)
    {
        $this->properties->forget($property);
    }

    /**
     * Get a property from the contact.
     *
     * @param $property
     * @return mixed
     */
    public function __get($property)
    {
        if ($this->wantsId($property)) {
            return $this->guid;
        }

        // Return the changed version if we have it
        if ($this->changes->has($property)) {
            return $this->changes->get($property);
        }

        return $this->properties->get($property);
    }


    /**
     * Map property values to their key.
     *
     * @param array $properties
     * @return Collection
     */
    private function mapProperties($properties)
    {
        $properties = Collection::make($properties);

        return $properties->map(function ($property) {
            return $property->value;
        });
    }

    /**
     * Check if the requested property is an ID.
     *
     * @param $property
     * @return bool
     */
    private function wantsId($property)
    {
        return $property === 'id' || $property === 'guid';
    }
}
