<?php declare( strict_types = 1 );

namespace PiotrPress\WordPress\Hooks;

use ReflectionClass;
use ReflectionMethod;

use function defined;
use function trait_exists;

use function preg_match_all;
use function intval;

defined( 'ABSPATH' ) or exit;

if ( ! trait_exists( __NAMESPACE__ . '\Annotations' ) ) {
    trait Annotations  {
        public function add_hooks() : void {
            $object  = new ReflectionClass( $this );
            $methods = $object->getMethods( ReflectionMethod::IS_PUBLIC );

            foreach ( $methods as $method ) {
                if ( ! $comment = $method->getDocComment() ) continue;

                $pattern = '#\* @(?P<hook>filter|action)\s+(?P<tag>[a-z0-9\-\.\/_]+)(\s+(?P<priority>\d+))?#';
                if ( ! preg_match_all( $pattern, $comment, $hooks, PREG_SET_ORDER ) ) continue;

                foreach ( $hooks as $hook ) {
                    $priority = isset( $hook[ 'priority' ] ) ? intval( $hook[ 'priority' ] ) : 10;
                    $filter = new Filter( $hook[ 'tag' ], $priority );
                    $filter->add( [ $this, $method->getName() ], $method->getNumberOfParameters() );
                }
            }
        }
    }
}