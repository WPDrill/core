<?php

namespace WPDrill;

class Option
{
    public static function get($keys, $default = null)
    {
        if (is_array($keys)) {
            return get_options($keys);
        }

        return get_option($keys, $default);
    }

    public static function set(string $key, $value): bool
    {
        return update_option($key, $value);
    }

    public static function delete(string $key): bool
    {
        return delete_option($key);
    }

    public static function all(): array
    {
        return wp_load_alloptions();
    }

    public static function has(string $key): bool
    {
        return array_key_exists($key, self::all());
    }

    public static function forget(string $key): bool
    {
        return self::delete($key);
    }

    public static function flush(): bool
    {
        return wp_cache_delete('alloptions', 'options');
    }

    public static function getPostMeta(int $postId, string $key, $default = null)
    {
        return get_post_meta($postId, $key, true) ?: $default;
    }

    public static function setPostMeta(int $postId, string $key, $value): bool
    {
        return update_post_meta($postId, $key, $value);
    }

    public static function deletePostMeta(int $postId, string $key): bool
    {
        return delete_post_meta($postId, $key);
    }

    public static function allPostMeta(int $postId): array
    {
        return get_post_meta($postId);
    }

    public static function hasPostMeta(int $postId, string $key): bool
    {
        return array_key_exists($key, self::allPostMeta($postId));
    }

    public static function forgetPostMeta(int $postId, string $key): bool
    {
        return self::deletePostMeta($postId, $key);
    }

    public static function flushPostMeta(int $postId): bool
    {
        return delete_post_meta($postId, '');
    }


}
