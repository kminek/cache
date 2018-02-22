<?php

Kminek_Cache::setInstance(new Kminek_Cache());

if (!function_exists('cache_get')) {
    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    function cache_get($key, $default = null)
    {
        return Kminek_Cache::getInstance()->get($key, $default);
    }
}

if (!function_exists('cache_set')) {
    /**
     * @param string   $key
     * @param mixed    $value
     * @param null|int $ttl
     *
     * @return bool
     */
    function cache_set($key, $value, $ttl = null)
    {
        return Kminek_Cache::getInstance()->set($key, $value, $ttl);
    }
}

if (!function_exists('cache_delete')) {
    /**
     * @param string $key
     *
     * @return bool
     */
    function cache_delete($key)
    {
        return Kminek_Cache::getInstance()->delete($key);
    }
}

if (!function_exists('cache_clear')) {
    /**
     * @return bool
     */
    function cache_clear()
    {
        return Kminek_Cache::getInstance()->clear();
    }
}
