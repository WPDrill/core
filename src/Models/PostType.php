<?php

namespace WPDrill\Models;

class PostType extends Model
{
    protected static $table = 'posts';

    public static ?string $postType = null;

    public static function __callStatic($method, $args) {
        $instance = parent::__callStatic( $method, $args );

        if ( self::$postType === null ) {
            return $instance;
        }

        return $instance->where('post_type', self::$postType);

    }
}
