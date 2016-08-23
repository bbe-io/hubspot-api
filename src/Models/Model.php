<?php

namespace BBE\HubspotAPI\Models;

use BBE\HubspotAPI\Resources\Contracts\CanRetrieveData;
use Illuminate\Support\Collection;

abstract class Model
{
    /**
     * The model ID.
     *
     * @var int
     */
    public $id;

    /**
     * The model resource.
     *
     * @var Resource
     */
    public $resource;

    /**
     * A collection of properties the model has.
     *
     * @var Collection
     */
    public $properties;

    /**
     * A collection of unsaved changes to properties.
     *
     * @var Collection
     */
    public $changes;

    /**
     * Model constructor.
     *
     * @param CanRetrieveData $resource
     * @param $object
     */
    public function __construct(CanRetrieveData $resource, $object)
    {
        $this->resource = $resource;

        $this->id = $this->getJsonID($object);
        $this->properties = $this->mapProperties($object);
        $this->changes = Collection::make();
    }

    /**
     * Get the model ID.
     *
     * @param $object
     * @return mixed
     */
    abstract public function getJsonID($object);

    /**
     * Check if the requested property is an ID.
     *
     * @param $property
     * @return bool
     */
    abstract public function wantsId($property);

    /**
     * Map property values to their key.
     *
     * @param $object
     * @return Collection
     */
    abstract public function mapProperties($object);

    /**
     * Returns true if the value is a change to the unsaved property.
     *
     * @param $property
     * @param $value
     * @return bool
     */
    public function propertyChanged($property, $value)
    {
        if ($this->properties->has($property)) {
            return $this->properties->get($property) !== $value;
        }

        return true;
    }

    /**
     * Discard changes to the Contact.
     *
     * @return $this
     */
    public function discard()
    {
        $this->changes = Collection::make();

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (String) $this->properties->toJson();
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
            return $this->id;
        }

        // Return the changed version if we have it
        if ($this->changes->has($property)) {
            return $this->changes->get($property);
        }

        return $this->properties->get($property);
    }

    /**
     * Set a property on the contact.
     *
     * @param $property
     * @param $value
     * @return bool
     */
    public function __set($property, $value)
    {
        if ($this->propertyChanged($property, $value)) {
            $this->changes->put($property, $value);
        } else {
            // The supplied value is identical to the existing value - forget the change.
            $this->changes->forget($property);
        }
    }
}
