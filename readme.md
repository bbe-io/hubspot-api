# BBE Hubspot API
Wrapper for the Hubspot API

## Setup
```php
use BBE\HubspotAPI\Factory as Hubspot;

$hubspot = Hubspot::connect('***REMOVED***');
```

## Usage

### Contacts
`BBE\HubspotAPI\Resources\Contacts`

#### Retrieving single contacts

Returns an instance of [`BBE\HubspotAPI\Models\Contact`](#contacts).

```php
$contact = $hubspot->contacts()->findWithId('***REMOVED***');
$contact = $hubspot->contacts()->findWithEmail('***REMOVED***');
$contact = $hubspot->contacts()->findWithToken('***REMOVED***');
```

#### Retrieving multiple contacts

Always returns a `Collection`, even if only one contact is requested.
All of Laravel's [collection methods](https://laravel.com/docs/5.2/collections#available-methods) are available. 

##### All recent contacts

```php
$contacts = $hubspot->contacts()->all();
```
Note that HubSpot will only return a maximum of 100 contacts

##### Subset of recent contacts

```php
$contacts = $hubspot->contacts()->take(3);
```

##### Contacts by ID

```php
$contacts = $hubspot->contacts()->whereId('***REMOVED***');
$contacts = $hubspot->contacts()->whereId(['***REMOVED***', '***REMOVED***', '***REMOVED***']);
```

##### Contacts by email

```php
$contacts = $hubspot->contacts()->whereEmail('***REMOVED***');
$contacts = $hubspot->contacts()->whereEmail([
    '***REMOVED***',
    '***REMOVED***',
    '***REMOVED***',
]);
```

##### Contacts by token

```php
$contacts = $hubspot->contacts()->whereToken('***REMOVED***');
$contacts = $hubspot->contacts()->whereToken([
    '***REMOVED***',
    '***REMOVED***',
]);
```

### Contact
`BBE\HubspotAPI\Models\Contact`

All contacts properties are automatically accessible from the object itself.
For example, if there is an "email" property setup in HubSpot you can get/set it with `$contact->email`.

#### Inserts

[TODO]

#### Updates

The `save` method can be called to update the contact in HubSpot.

```php
$contact = $hubspot->contacts()->whereId('***REMOVED***')->first();

$contact->phone = '03 9500 000';
$contact->email = 'new@email.com';

$contact->save();
```

Changes to the model are tracked, and only the changed properties will be sent to HubSpot to update.

#### Discarding changes

`discard` will drop all changes you have made to the contact.

```php
$contact = $hubspot->contacts()->findWithId('***REMOVED***');
$contact->discard();
```

`fresh` will drop all changes and fetch a fresh copy of the contact from HubSpot.

```php
$contact = $hubspot->contacts()->findWithId('***REMOVED***');
$contact->fresh();
```