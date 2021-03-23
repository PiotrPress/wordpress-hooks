<?php declare(strict_types=1);

namespace PiotrPress\WordPress\Hooks;

use ReflectionClass;
use ReflectionMethod;

use function defined;
use function trait_exists;

use function substr;
use function strrchr;
use function is_numeric;
use function strlen;
use function strpos;

defined( 'ABSPATH' ) or exit;

if ( ! trait_exists( __NAMESPACE__ . '\Methods' ) ) {
    trait Methods {
        public function add_hooks() : void {
            $object  = new ReflectionClass( $this );
            $methods = $object->getMethods( ReflectionMethod::IS_PUBLIC );

            foreach ( $methods as $method ) {
                if ( '' === $this->getHook( $method->getName() ) ) continue;
                if ( '' === $priority = $this->getPriority( $method->getName() ) ) $priority = 10;
                $filter = new Filter( $this->getTag( $method->getName() ), (int)$priority );
                $filter->add( [ $this, $method->getName() ], $method->getNumberOfParameters() );
            }
        }

        protected function getPriority( string $method ) : string {
            $priority = substr( strrchr( $method, '_' ), 1 );

            return is_numeric( $priority ) ? $priority : '';
        }

        protected function getTag( string $method ) : string {
            if ( '' !== $priority = $this->getPriority( $method ) )
                $method = substr( $method, 0, strlen( $method ) - strlen( $priority ) - 1 );

            if ( $hook = $this->getHook( $method ) )
                $method = substr( $method, strlen( $hook ) + 1 );

            return $method;
        }

        protected function getHook( string $method ) : string {
            foreach ( [ 'filter', 'action' ] as $hook )
                if ( 0 === strpos( $method, $hook . '_' ) )
                    return $hook;

            return '';
        }
    }
}