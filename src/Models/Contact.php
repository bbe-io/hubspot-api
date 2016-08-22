<?php

namespace BBE\HubspotAPI\Models;

use BBE\HubspotAPI\Resources\Contacts;
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
     * A collection of unsaved changes to properties.
     *
     * @var Collection
     */
    public $changes;

    /**
     * The Contacts resource.
     *
     * @var Contacts
     */
    public $resource;

    /**
     * Contact constructor.
     *
     * @param Contacts $resource
     * @param $object
     */
    public function __construct(Contacts $resource, $object)
    {
        $this->resource = $resource;

        $this->vid = $object->vid ?: null;
        $this->properties = $this->mapProperties($object->properties);
        $this->changes = Collection::make();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (String) $this->vid;
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

    /**
     * Save the contact to HubSpot.
     *
     * @return mixed|null|\Psr\Http\Message\ResponseInterface|void
     * @throws \Exception
     */
    public function save()
    {
        $response = null;

        // Merge changes
        $this->properties = $this->properties->merge($this->changes);

        if ($this->vid) {
            $response = $this->saveWithId();
        } elseif ($this->email) {
            $response = $this->saveWithEmail();
        }

        // Clear changes collection
        if ($response) {
            $this->changes = Collection::make();

            return $response;
        }

        throw new \Exception('Contact does not have an email or ID for updating');
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
     * Get a fresh copy of the Contact from HubSpot.
     *
     * @return $this
     */
    public function fresh()
    {
        $this->discard();

        if ($this->vid) {
            $this->properties = $this->resource->findWithId($this->vid)->properties;
        } elseif ($this->email) {
            $this->properties = $this->resource->findWithEmail($this->email)->properties;
        }

        return $this;
    }

    /**
     * Update the contact using its ID.
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface|void
     */
    private function saveWithId()
    {
        $endpoint = '/contact/vid/'.$this->vid.'/profile';
        $options = ['json' => ['properties' => $this->changesToArray()]];

        return $this->resource->post($endpoint, $options);
    }

    /**
     * Create or update the contact with its email.
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface|void
     */
    private function saveWithEmail()
    {
        $endpoint = '/contact/createOrUpdate/email/'.$this->email;
        $options = ['json' => ['properties' => $this->changesToArray()]];

        return $this->resource->post($endpoint, $options);
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
     * Map the changes into a property/value format array for HubSpot.
     *
     * @return array
     */
    private function changesToArray()
    {
        return $this->changes->map(function ($item, $key) {
            return [
                'property' => $key,
                'value' => $item,
            ];
        })->values()->toArray();
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

    /**
     * Returns true if the value is a change to the unsaved property.
     *
     * @param $property
     * @param $value
     * @return bool
     */
    private function propertyChanged($property, $value)
    {
        if ($this->properties->has($property)) {
            return $this->properties->get($property) !== $value;
        }

        return true;
    }
}
