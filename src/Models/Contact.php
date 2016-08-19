<?php

namespace BBE\HubspotAPI\Models;

use Illuminate\Support\Collection;

class Contact
{
    /**
     * The contact ID.
     *
     * @var int
     */
    public $vid;

    /**
     * A collection of properties the contact has.
     *
     * @var Collection
     */
    public $properties;

    /**
     * Contact constructor.
     *
     * @param $object
     */
    public function __construct($object)
    {
        $this->vid = $object->vid;
        $this->properties = $this->mapProperties($object->properties);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return '';
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
            return $this->vid;
        }

        return $this->properties->get($property);
    }

    /**
     * Set a property on the contact.
     *
     * @param $property
     * @param $value
     */
    public function __set($property, $value)
    {
        $this->properties->put($property, $value);
    }

    /**
     * Map property values to their key.
     *
     * @param $properties
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
        return $property === 'id' || $property === 'vid';
    }
}
