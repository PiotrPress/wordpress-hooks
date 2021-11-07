<?php declare( strict_types = 1 );

namespace PiotrPress\WordPress\Hooks;

use ReflectionClass;
use ReflectionMethod;

use function defined;
use function trait_exists;

use function token_get_all;
use function file_get_contents;
use function array_walk;
use function is_array;
use function strpos;
use function preg_match;
use function intval;

defined( 'ABSPATH' ) or exit;

if ( ! trait_exists( __NAMESPACE__ . '\Attributes' ) ) {
    trait Attributes {
        public function add_hooks() : void {
            $object  = new ReflectionClass( $this );
            $methods = $object->getMethods( ReflectionMethod::IS_PUBLIC );

            $tokens = token_get_all( file_get_contents( $object->getFilename() ) );
            array_walk( $tokens, function( $token ) use( $methods ) {
                $pattern = '/#\[(?P<hook>Filter|Action)\(((["\'])(?P<tag>[a-z0-9\-\.\/_]+)\3)([,]?\s+(?P<priority>\d+))?\)\]/';
                if( is_array( $token ) &&
                    ( $token[ 0 ] == T_COMMENT ) &&
                    ( 0 === strpos( $token[ 1 ], '#' ) ) &&
                    preg_match( $pattern, $token[ 1 ], $hook ) )
                    foreach ( $methods as $method )
                        if ( $method->getStartLine() > $token[ 2 ] ) {
                            $hook[ 'priority' ] = isset( $hook[ 'priority' ] ) ? intval( $hook[ 'priority' ] ) : 10;
                            $filter = new Filter( $hook[ 'tag' ], $hook[ 'priority' ] );
                            $filter->add( [ $this, $method->getName() ], $method->getNumberOfParameters() );
                            return;
                        }
            } );
        }
    }
}