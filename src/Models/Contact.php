<?php

namespace BBE\HubspotAPI\Models;

use Illuminate\Support\Collection;

class Contact extends Model
{
    /**
     * Get the model ID.
     *
     * @param $object
     * @return mixed
     */
    public function getJsonID($object)
    {
        return $object->vid ?: null;
    }

    /**
     * Check if the requested property is an ID.
     *
     * @param $property
     * @return bool
     */
    public function wantsId($property)
    {
        return $property === 'id' || $property === 'vid';
    }

    /**
     * Map property values to their key.
     *
     * @param $object
     * @return Collection
     */
    public function mapProperties($object)
    {
        $properties = Collection::make($object->properties);

        return $properties->map(function ($property) {
            return $property->value;
        });
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (String) $this->id;
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

    /**
     * Save the contact to HubSpot.
     *
     * @return $this
     * @throws \Exception
     */
    public function save()
    {
        if ($this->id) {
            $this->saveWithId();
        } elseif ($this->email) {
            $this->saveWithEmail();
        } else {
            throw new \Exception('Contact does not have an email or ID for updating');
        }

        $this->properties = $this->properties->merge($this->changes);
        $this->changes = Collection::make();

        return $this;
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

        if ($this->id) {
            $this->properties = $this->resource->findWithId($this->id)->properties;
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
        $endpoint = '/contact/vid/'.$this->id.'/profile';
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
