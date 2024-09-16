<?php declare( strict_types = 1 );

namespace PiotrPress\WordPress;

use PiotrPress\WordPress\Hooks\Filter as Hook;
use PiotrPress\CacherInterface;
use PiotrPress\Cacher;

\defined( 'ABSPATH' ) or exit;

if( ! \class_exists( __NAMESPACE__ . '\Hooks' ) ) {
    class Hooks {
        protected static ?CacherInterface $cache = null;

        public static function add( object $object = null, string $callback = '', CacherInterface $cache = null ) : void {
            self::call( 'add', $object, $callback, $cache );
        }

        public static function remove( object $object = null, string $callback = '', CacherInterface $cache = null ) : void {
            self::call( 'remove', $object, $callback, $cache );
        }

        protected static function call( string $method, object $object = null, string $callback = '', CacherInterface $cache = null ) : void {
            if( ! $object and ! $callback ) return;

            self::$cache ??= new Cacher( 'php://memory' );
            $cache = $cache ?? self::$cache;

            foreach( ( $cache )->get( ( $object ? \get_class($object) : 'null' ) . ( $callback ? ".$callback" : '' ), [ self::class, 'get' ], $object, $callback )
            as $hook ) ( new Hook( $hook[ 'name' ], $hook[ 'priority' ] ) )->$method( $object ? [ $object, $hook[ 'callback' ] ] : $hook[ 'callback' ], $hook[ 'count' ] );
        }

        public static function get( object $object = null, string $callback = '' ) : array {
            if( ! $object and ! $callback ) return [];

            try {
                if( ! $object ) $functions[] = new \ReflectionFunction( $callback );
                else $functions = ( new \ReflectionClass( $object ) )->getMethods( \ReflectionMethod::IS_PUBLIC );
            } catch( \Exception $exception ) { return []; }

            if( \version_compare( \PHP_VERSION, '8.0', '>=' ) ) {
                foreach( $functions ?? [] as $function )
                    foreach( $function->getAttributes( Hook::class, \ReflectionAttribute::IS_INSTANCEOF ) as $attribute )
                        if( ! $callback or $callback === $function->getName() )
                            $hooks[] = [
                                'name' => $attribute->getArguments()[ 'name' ] ?? $attribute->getArguments()[ 0 ] ?? '',
                                'callback' => $function->getName(),
                                'priority' => $attribute->getArguments()[ 'priority' ] ?? $attribute->getArguments()[ 1 ] ?? 10,
                                'count' => $function->getNumberOfParameters()
                            ];
            } else {
                foreach( $functions ?? [] as $function ) $files[ $function->getFileName() ][] = $function;
                foreach( \array_unique( $files ?? [] ) ?? [] as $file => $functions ) {
                    $tokens = \token_get_all( @\file_get_contents( $file ) );
                    \array_walk( $tokens, function( $token ) use( $functions, &$hooks ) {
                        $pattern = '/#\[\s*(Action|Filter)\s*\(\s*[\'"](?P<name>[^\'"]+)[\'"]\s*(?:,\s*(?P<priority>\d+))?\s*\)\s*\]/';
                        if( \is_array( $token ) &&
                            ( $token[ 0 ] == T_COMMENT ) &&
                            ( 0 === \strpos( $token[ 1 ], '#' ) ) &&
                            \preg_match( $pattern, $token[ 1 ], $hook ) )
                            foreach( $functions as $function )
                                if( $function->getStartLine() > $token[ 2 ] )
                                    return $hooks[] = [
                                        'name' => $hook[ 'name' ] ?? '',
                                        'callback' => $function->getName(),
                                        'priority' => isset( $hook[ 'priority' ] ) ? \intval( $hook[ 'priority' ] ) : 10,
                                        'count' => $function->getNumberOfParameters()
                                    ];
                    } );
                }
            }

            return $hooks ?? [];
        }
    }
}