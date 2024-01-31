# Cash - PHP File-Based Cache

This is a simple PHP file-based caching system with support for cache monitoring and encryption. It allows you to securely cache data and monitor cache usage in your PHP applications.

## Features

- **File-based caching**: Data is stored in files on the filesystem.
- **Cache monitoring**: Track cache hits, misses, and cache size.
- **Encryption support**: Encrypt cached data for security.
- **Customizable expiry**: Set expiry time for cached data.

## Installation

1. Clone the repository or download the `Cache.php` file.
2. Include the `Cache.php` file in your PHP project.

## Usage

### 1. Initialization

```php
// Include the Cache class
require_once "Cache.php";

// Create a new Cache instance with cache directory and encryption key
$cache = new Cache("./cache", "my_secret_key");
```

### 2. Caching Data

```php
// Cache data with optional expiry time (default: no expiry)
$cache->set("key", "value", 3600); // Cache "value" with key "key" for 1 hour
```

### 3. Retrieving Data

```php
// Retrieve cached data
if ($cachedData = $cache->get("key");) {
    echo "Cached data: " . $cachedData; // return "value" - if set
} else {
    echo "Data not found in cache.";
    // You now may execute a call to the Database and then set the cache
}
```

### 4. Clearing Cache

```php
// Clear cache for a specific key
$cache->clearCache("key");

// Clear entire cache
$cache->clearCache();
```

## Contributing

Contributions are welcome! Please feel free to submit issues or pull requests.

## License

This project is licensed under the GPL-3.0 license - see the [LICENSE](LICENSE) file for details.