<?php declare( strict_types = 1 );

namespace PiotrPress\WordPress;

use PiotrPress\WordPress\Hooks\Filter;
use PiotrPress\WordPress\Hooks\Action;

\defined( 'ABSPATH' ) or exit;

if ( ! \class_exists( __NAMESPACE__ . '\Hooks' ) ) {
    class Hooks {
        public static function add( object $object = null, string $callback = '' ) : void {
            self::call( 'add', $object, $callback );
        }

        public static function remove( object $object = null, string $callback = '' ) : void {
            self::call( 'remove', $object, $callback );
        }

        protected static function call( string $hook_method, object $object = null, string $callback = '' ) : void {
            try {
                if ( ! $object ) foreach ( self::getAttributes( $function = new \ReflectionFunction( $callback ) ) as $attribute )
                    $attribute->newInstance()->$hook_method( $function->getName(), $function->getNumberOfParameters() );
                else foreach ( ( new \ReflectionClass( $object ) )->getMethods( \ReflectionMethod::IS_PUBLIC ) as $method )
                    foreach ( self::getAttributes( $method ) as $attribute )
                        if ( ! $callback or $callback === $method->getName() )
                            $attribute->newInstance()->$hook_method( [ $object, $method->getName() ], $method->getNumberOfParameters() );
            } catch ( \Exception ) {}
        }

        protected static function getAttributes( \ReflectionFunction | \ReflectionMethod $callback ) : array {
            return \array_filter( $callback->getAttributes(), fn( $attribute ) => \in_array( $attribute->getName(), [ Filter::class, Action::class ] ) );
        }
    }
}