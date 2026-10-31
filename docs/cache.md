# Caching

Acorn provides a simple caching layer with pluggable drivers.

## Usage

```php
use function Myerscode\Acorn\Foundation\container;

$cache = container('cache');

// Store a value
$cache->set('key', 'value', 3600); // TTL in seconds

// Retrieve a value
$value = $cache->get('key', 'default');

// Check existence (respects TTL expiration)
if ($cache->has('key')) {
    // ...
}

// Remove a value
$cache->delete('key');
```

## TTL (Time To Live)

TTL can be specified as:
- An integer (seconds): `$cache->set('key', 'value', 3600)`
- A `DateInterval`: `$cache->set('key', 'value', new DateInterval('PT1H'))`
- A `DateTimeInterface` (must be in the future): `$cache->set('key', 'value', new DateTime('+1 hour'))`
- `null` for no expiration

## Drivers

### RuntimeCache

In-memory cache that persists for the lifetime of the current process. Fast but not persistent across runs.

```php
// Configured in app config
'cache' => [
    'driver' => RuntimeCache::class,
    'namespace' => 'acorn',
    'config' => [],
],
```

### FileCacheDriver

File-based cache that persists across process runs. Each cache entry is stored as a JSON file.

```php
'cache' => [
    'driver' => FileCacheDriver::class,
    'namespace' => 'acorn',
    'config' => ['/path/to/cache/directory'],
],
```

The cache directory is created automatically if it does not exist.

## Helper Methods

```php
// Store only if key doesn't exist
$cache->add('key', 'value', 3600);

// Get and delete in one call
$value = $cache->pull('key');

// Remember — get from cache or execute callback and store
$value = $cache->remember('key', 3600, fn () => expensiveOperation());

// Clear all cached values
$cache->flush();
```

## Key Rules

Cache keys follow PSR-16: a key must be a non-empty string and must not contain
any of the reserved characters `{}()/\@:`. An invalid key throws
`Myerscode\Acorn\Framework\Cache\Exception\InvalidArgumentException`.

```php
$cache->set('user.rupert', $value);   // ok
$cache->set('user:rupert', $value);   // throws — ':' is reserved
$cache->set('', $value);              // throws — empty key
```
