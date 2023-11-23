<?php declare( strict_types = 1 );

namespace PiotrPress\WordPress;

use PiotrPress\WordPress\Hooks\Filter as Hook;
use PiotrPress\CacheInterface;
use PiotrPress\Cacher;

\defined( 'ABSPATH' ) or exit;

if ( ! \class_exists( __NAMESPACE__ . '\Hooks' ) ) {
    class Hooks {
        protected static ?CacheInterface $cache = null;

        public static function add( object $object = null, string $callback = '', CacheInterface $cache = null ) : void {
            self::call( 'add', $object, $callback, $cache );
        }

        public static function remove( object $object = null, string $callback = '', CacheInterface $cache = null ) : void {
            self::call( 'remove', $object, $callback, $cache );
        }

        protected static function call( string $method, object $object = null, string $callback = '', CacheInterface $cache = null ) : void {
            if ( ! $object and ! $callback ) return;

            self::$cache ??= new Cacher( 'php://memory' );
            $cache = $cache ?? self::$cache;

            foreach ( ( $cache )->get( ( $object ? $object::class : 'null' ) . ( $callback ? ".$callback" : '' ), [ self::class, 'get' ], $object, $callback )
            as $hook ) ( new Hook( $hook[ 'name' ], $hook[ 'priority' ] ) )->$method( $object ? [ $object, $hook[ 'callback' ] ] : $hook[ 'callback' ], $hook[ 'count' ] );
        }

        public static function get( object $object = null, string $callback = '' ) : array {
            if ( ! $object and ! $callback ) return [];

            try {
                if ( ! $object ) $functions[] = new \ReflectionFunction( $callback );
                else $functions = ( new \ReflectionClass( $object ) )->getMethods( \ReflectionMethod::IS_PUBLIC );
            } catch ( \Exception ) { return []; }

            foreach ( $functions ?? [] as $function )
                foreach ( $function->getAttributes( Hook::class, \ReflectionAttribute::IS_INSTANCEOF ) as $attribute )
                    if ( ! $callback or $callback === $function->getName() )
                        $hooks[] = [
                            'name' => $attribute->getArguments()[ 'name' ] ?? $attribute->getArguments()[ 0 ] ?? '',
                            'callback' => $function->getName(),
                            'priority' => $attribute->getArguments()[ 'priority' ] ?? $attribute->getArguments()[ 1 ] ?? 10,
                            'count' => $function->getNumberOfParameters()
                        ];

            return $hooks ?? [];
        }
    }
}