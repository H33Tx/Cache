<?php

/**
 * Class Cache
 *
 * A simple file-based caching system in PHP with cache monitoring and encryption support.
 *
 * @author saintly2k (https://github.com/saintly2k)
 * @version 2024-01-29
 */
class Cache
{
    /** @var string The directory where cache files are stored. */
    private $cacheDir;

    /** @var string The encryption key. */
    private $encryptionKey;

    /** @var int Number of cache hits. */
    private $cacheHits = 0;

    /** @var int Number of cache misses. */
    private $cacheMisses = 0;

    /**
     * Cache constructor.
     *
     * @param string $cacheDir The directory where cache files will be stored.
     * @param string $encryptionKey The encryption key.
     *
     * @throws Exception If the cache directory cannot be created.
     */
    public function __construct($cacheDir, $encryptionKey)
    {
        if (!is_dir($cacheDir) && !mkdir($cacheDir, 0777, true)) {
            throw new Exception("Failed to create cache directory: $cacheDir");
        }
        $this->cacheDir = $cacheDir;
        $this->encryptionKey = $encryptionKey;
    }

    /**
     * Retrieves data from cache.
     *
     * @param string $key The key associated with the cached data.
     *
     * @return mixed|false The decrypted cached data if found and not expired, false otherwise.
     */
    public function get($key)
    {
        $file = $this->getCacheFileName($key);

        if (!file_exists($file)) {
            $this->incrementCacheMisses(); // Increment cache misses counter
            return false;
        }

        $encryptedData = file_get_contents($file);
        if ($encryptedData === false) {
            // Error reading cache file
            // Handle the error, e.g., log an error message and return false
            return false;
        }

        $decryptedData = $this->customDecrypt($encryptedData);
        if ($decryptedData === false) {
            // Error decrypting data
            // Handle the error, e.g., log an error message and return false
            return false;
        }

        $cachedData = unserialize($decryptedData);
        if (!$cachedData || !isset($cachedData['expiry'], $cachedData['data'])) {
            // Invalid cache data
            // Handle the error, e.g., log an error message and return false
            return false;
        }

        // Check if cache has expired
        if ($cachedData['expiry'] === 0 || time() < $cachedData['expiry']) {
            $this->incrementCacheHits(); // Increment cache hits counter
            return $cachedData['data'];
        } else {
            // Remove cache file if expired
            unlink($file);
            $this->incrementCacheMisses(); // Increment cache misses counter
            return false;
        }
    }

    /**
     * Stores data in cache.
     *
     * @param string $key The key to associate with the cached data.
     * @param mixed $data The data to be cached.
     * @param int $expiry The expiry time for the cached data in seconds. Defaults to 0 (no expiry).
     *
     * @return void
     */
    public function set($key, $data, $expiry = 0)
    {
        if (empty($key)) {
            throw new InvalidArgumentException("Key must be a non-empty string.");
        }

        $file = $this->getCacheFileName($key);
        $cachedData = [
            'expiry' => $expiry > 0 ? time() + $expiry : 0,
            'data' => $data,
        ];

        $encryptedData = $this->customEncrypt(serialize($cachedData));
        if ($encryptedData === false || file_put_contents($file, $encryptedData) === false) {
            throw new Exception("Failed to write cache file: $file");
        }
    }

    /**
     * Clears cache for a specific key or the entire cache directory.
     *
     * @param string|null $key The key for which cache needs to be cleared. If null, clears the entire cache.
     *
     * @return void
     */
    public function clearCache($key = null)
    {
        if ($key === null) {
            // Clear entire cache directory
            array_map('unlink', glob($this->cacheDir . '/*.cache'));
        } else {
            // Clear cache for specific key
            $file = $this->getCacheFileName($key);
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Generates the filename for a cache key.
     *
     * @param string $key The cache key.
     *
     * @return string The filename for the cache key.
     */
    private function getCacheFileName($key)
    {
        return $this->cacheDir . '/' . md5($key) . '.cache';
    }

    /**
     * Custom encryption method.
     *
     * @param string $data The data to encrypt.
     *
     * @return string|false The encrypted data, or false on failure.
     */
    private function customEncrypt($data)
    {
        // Your custom encryption logic here
        // For example, you can use base64 encoding for simplicity
        return base64_encode($data);
    }

    /**
     * Custom decryption method.
     *
     * @param string $data The data to decrypt.
     *
     * @return string|false The decrypted data, or false on failure.
     */
    private function customDecrypt($data)
    {
        // Your custom decryption logic here
        // For example, you can use base64 decoding for simplicity
        return base64_decode($data);
    }

    /**
     * Increment the cache hits counter.
     *
     * @return void
     */
    private function incrementCacheHits()
    {
        $this->cacheHits++;
    }

    /**
     * Increment the cache misses counter.
     *
     * @return void
     */
    private function incrementCacheMisses()
    {
        $this->cacheMisses++;
    }

    /**
     * Get the number of cache hits.
     *
     * @return int The number of cache hits.
     */
    public function getCacheHits()
    {
        return $this->cacheHits;
    }

    /**
     * Get the number of cache misses.
     *
     * @return int The number of cache misses.
     */
    public function getCacheMisses()
    {
        return $this->cacheMisses;
    }

    /**
     * Get the cache size.
     *
     * @return int The cache size in bytes.
     */
    public function getCacheSize()
    {
        $cacheFiles = glob($this->cacheDir . '/*.cache');
        $totalSize = 0;
        foreach ($cacheFiles as $file) {
            $totalSize += filesize($file);
        }
        return $totalSize;
    }
}
