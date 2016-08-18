# Medibank Index
Global realtime search index for Medibank

## Backend

### Setup
```php
use AlgoliaSearch\Client as Algolia;
use Medibank\Index\Engines\AlgoliaEngine as Engine;

$algolia = new Algolia('id', 'secret');
$searchEngine = new Engine($algolia, 'dev_HealthBreif'); 
```

### Import
```php
use Illuminate\Support\Collection;

$post_data = [
    ['id' => 1],
    ['id' => 2],
    ['id' => 3],
];

$searchEngine->import(Collection::make($post_data));
```

### Update
```php
use Illuminate\Support\Collection;

$post_data = [
    ['id' => 1, title => 'Title']
];

$searchEngine->update(Collection::make($post_data));
```

### Delete
```php
use Illuminate\Support\Collection;

$post_ids = [1, 2];

$searchEngine->delete(Collection::make($post_data));
```