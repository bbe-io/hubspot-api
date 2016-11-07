# BBE Hubspot API
Wrapper for the Hubspot API

# Setup
```php
use BBE\HubspotAPI\Factory as Hubspot;

$hubspot = Hubspot::connect('[hubspot_api_key]');
```

# Usage

## Contacts
`BBE\HubspotAPI\Resources\Contacts`

### Retrieving a single contact

Returns an instance of `BBE\HubspotAPI\Models\Contact`.

```php
$contact = $hubspot->contacts()->find('[contact-id]');
$contact = $hubspot->contacts()->findWithId('[contact-id]');
$contact = $hubspot->contacts()->findWithEmail('existing@email.com');
$contact = $hubspot->contacts()->findWithToken('[contact-token]');
```

### Retrieving multiple contacts

Always returns a `Collection`, even if only one contact is requested.
All of Laravel's [collection methods](https://laravel.com/docs/5.2/collections#available-methods) are available. 

#### All recent contacts

```php
$contacts = $hubspot->contacts()->all();
```
Note that HubSpot will only return a maximum of 100 contacts.

#### Subset of recent contacts

```php
$contacts = $hubspot->contacts()->take(3);
```

#### Contacts by ID

```php
$contacts = $hubspot->contacts()->whereId('[contact-id]');
$contacts = $hubspot->contacts()->whereId(['[contact-id]', '[contact-id-2]', '[contact-id-3]']);
```

#### Contacts by email

```php
$contacts = $hubspot->contacts()->whereEmail('existing@email.com');
$contacts = $hubspot->contacts()->whereEmail([
    'existing@email.com',
    'existing@email2.com',
    'existing@email3.com',
]);
```

#### Contacts by token

```php
$contacts = $hubspot->contacts()->whereToken('[contact-token]');
$contacts = $hubspot->contacts()->whereToken([
    '[contact-token]',
    '[contact-token-2]',
]);
```

## Contact
`BBE\HubspotAPI\Models\Contact`

All contacts properties are automatically accessible from the object itself.
For example, if there is an "email" property setup in HubSpot you can get/set it with `$contact->email`.

### Updates

The `save` method can be called to update the contact in HubSpot.

```php
$contact = $hubspot->contacts()->whereId('[contact-id]')->first();

$contact->phone = '03 9500 000';
$contact->email = 'new@email.com';

$contact->save();
```

Changes to the model are tracked, and only the changed properties will be sent to HubSpot to update.

### Discarding changes

`discard` will drop all changes you have made to the contact.

```php
$contact = $hubspot->contacts()->findWithId('[contact-id]');
$contact->discard();
```

`fresh` will drop all changes and fetch a fresh copy of the contact from HubSpot.

```php
$contact = $hubspot->contacts()->findWithId('[contact-id]');
$contact->fresh();
```

## Forms
`BBE\HubspotAPI\Resources\Forms`

### Retrieving a single form

Returns an instance of `BBE\HubspotAPI\Models\Form`.

```php
$form = $hubspot->forms()->find('[form_id]');
$form = $hubspot->forms()->findWithId('[form_id]');
```

### Retrieving multiple forms

Always returns a `Collection`, even if only one contact is requested.
All of Laravel's [collection methods](https://laravel.com/docs/5.2/collections#available-methods) are available. 

#### All forms

```php
$forms = $hubspot->forms()->all();
```

#### Subset of recent forms

```php
$forms = $hubspot->forms()->take(3);
```

#### Forms by ID

```php
$forms = $hubspot->contacts()->whereId('[form_id]');
$forms = $hubspot->contacts()->whereId([
    '[form_id_1]',
    '[form_id_2]',
]);
```

## Form
`BBE\HubspotAPI\Models\Form`

All fields are automatically accessible from the object itself.
For example, if there is an "firstname" field setup in HubSpot you can get it with `$contact->firstname`.

## Form Submission
`BBE\HubspotAPI\FormSubmission`

### Creating a submission

Form submissions can be created directly from the `FormSubmission` class, or directly from a `Form` model itself.

If you don't have `Form` model available, the endpoint must be set manually.

```php
$submission = FormSubmission::createForEndpoint('[portal_id]', '[form_id]');

$submission = FormSubmission::create()
    ->portalId('[portal_id]')
    ->formId('[form_id]');
```

Otherwise, If you have a `Form` model this data can be automatically set for you.

```php
$form = $hubspot->forms()->find('[form_id]');

$submission = FormSubmission::createForForm($form);
$submission = FormSubmission::create()->form($form);
$submission = $form->submission(); // Using the helper on the form
```

### Setting context

HubSpot allows you to supply contextual information about the submission, these properties can be fluently set on the `FormSubmission` once it has been created.

```php
$submission = FormSubmission::createForForm($form)
    ->page('Page Name', 'http://page.url');
    
$submission = FormSubmission::createForForm($form)
    ->pageName('Page Name')
    ->pageUrl('http://page.url');
```

The tracking token and IP address are automatically set from `$_COOKIE['hubspotutk']` and `$_SERVER['REMOTE_ADDR']` but can be manually overwritten.

```php
$submission = FormSubmission::createForForm($form)
    ->ip('127.0.0.1')
    ->token('[tracking_token]');
```

### Setting form fields

Form data can be set by passing a named array into the `data` method of the `FormSubmission`.

```php
$submission = FormSubmission::createForForm($form)
    ->data([
        'firstname' => 'James',
        'lastname' => 'Test',
        'email' => 'existing@email.com'
    ]);
```

### Submitting

Once a `FormSubmission` has been created and the data has been set, you can submit the form by simply calling `submit`.

```php
$submission = FormSubmission::createForForm($form)
    ->page('Page Name', 'http://page.url')
    ->data([
        'firstname' => 'James',
        'lastname' => 'Test',
        'email' => 'existing@email.com'
    ])
    ->submit();
```

You can also submit the `FormSubmission` directly to a `Form` model.

```php
$form = $hubspot->forms()->find('[form_id]');

$submission = FormSubmission::create()
    ->page('Page Name', 'http://page.url')
    ->data([
        'firstname' => 'James',
        'lastname' => 'Test',
        'email' => 'existing@email.com'
    ])
    ->submitToForm($form);
```

If you prefer, data and context can also be set during the submission.
 
```php
// Set form data in submit
$submission = FormSubmission::createForEndpoint('[portal_id]', '[form_id]')
    ->page('Unit Test', '//localhost')
    ->submit([
        'firstname' => 'James',
        'lastname' => 'Test',
        'email' => 'existing@email.com'
    ]);

// Set form data and context in submit
$submission = FormSubmission::createForEndpoint('[portal_id]', '[form_id]')
    ->submit([
        'firstname' => 'James',
        'lastname' => 'Test',
        'email' => 'existing@email.com'
    ], 'Unit Test', '//localhost');
```

This can be easily chained with helper methods.

```php
$submission = $hubspot->forms()
    ->find('[form_id]')
    ->submit([
        'firstname' => 'James',
        'lastname' => 'Test',
        'email' => 'existing@email.com'
    ], 'Unit Test', '//localhost');
```

Form submission return `true` on a successful submission and throw an exception otherwise.