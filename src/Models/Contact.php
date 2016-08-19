<?php

namespace BBE\HubspotAPI\Models;

use Illuminate\Support\Collection;

class Contact
{
    public $vid;

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

    public function __toString()
    {
        return '';
    }

    public function __isset($property)
    {
        if ($this->wantsId($property)) {
            return true;
        }

        return $this->properties->has($property);
    }

    public function __get($property)
    {
        if ($this->wantsId($property)) {
            return $this->vid;
        }

        return $this->properties->get($property);
    }

    public function __set($property, $value)
    {

    }

    private function mapProperties($properties)
    {
        $properties = Collection::make($properties);

        return $properties->map(function ($property) {
            return $property->value;
        });
    }

    private function wantsId($property)
    {
        return $property === 'id' || $property === 'vid';
    }
}
