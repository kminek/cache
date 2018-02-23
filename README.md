# Cache

This is really simple filesystem cache implementation which should work on
broad range of PHP versions. Just plug and play - perfect solution for
tiny projects and quick scripts where you do not need full-blown cache
solutions.

## Installation

```
$ composer require kminek/cache
```

## API

```php
/**
 * @param string $key
 * @param mixed  $default
 *
 * @return mixed
 */
function cache_get($key, $default = null);

/**
 * @param string   $key
 * @param mixed    $value
 * @param null|int $ttl
 *
 * @return bool
 */
function cache_set($key, $value, $ttl = null);

/**
 * @param string $key
 *
 * @return bool
 */
function cache_delete($key);

/**
 * @return bool
 */
function cache_clear();
```

## Usage

```php
require 'vendor/autoload.php';

$cachedData = cache_get('cachedData');

if (!$cachedData) {
    echo 'Saving cache...' . PHP_EOL;
    cache_set('cachedData', array('lorem', 'ipsum'), 5 * 60); // 5 minutes
} else {
    echo 'Data retrieved from cache!' . PHP_EOL;
    var_dump($cachedData);
}
```

## Advanced usage

```php
// set your custom configuration options (below are default ones)
Kminek_Cache::configure(array(
    'engine' => Kminek_Cache::ENGINE_SERIALIZE, // or Kminek_Cache::ENGINE_JSON or Kminek_Cache::ENGINE_VAR_EXPORT
    'dir' => sys_get_temp_dir(), // where to store cache files
    'prefix' => 'kminek_cache',
    'separator' => '_',
    'extension' => '.cache',
    'ttl' => 60 * 60, // default ttl in seconds (1 hour)
));

// then use cache as you would normally do
$cachedData = cache_get('cachedData');
...
```
