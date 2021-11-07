<?php declare( strict_types = 1 );

namespace PiotrPress\WordPress\Hooks;

use ReflectionClass;
use ReflectionMethod;

use function defined;
use function trait_exists;

use function array_merge;

defined( 'ABSPATH' ) or exit;

if ( ! trait_exists( __NAMESPACE__ . '\Attributes' ) ) {
    trait Attributes {
        public function add_hooks() : void {
            $object  = new ReflectionClass( $this );
            $methods = $object->getMethods( ReflectionMethod::IS_PUBLIC );

            foreach ( $methods as $method ) {
                $attributes = array_merge(
                    $method->getAttributes( __NAMESPACE__ . '\Action' ),
                    $method->getAttributes( __NAMESPACE__ . '\Filter' )
                );

                foreach ( $attributes as $attribute ) {
                    $filter = $attribute->newInstance();
                    $filter->add( [ $this, $method->getName() ], $method->getNumberOfParameters() );
                }
            }
        }
    }
}