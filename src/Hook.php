<?php declare( strict_types = 1 );

namespace PiotrPress\WordPress;

\defined( 'ABSPATH' ) or exit;

if ( ! \class_exists( __NAMESPACE__ . '\Hook' ) ) {
    #[ \Attribute( \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE ) ]
    class Hook {
        public function __construct( private string $hook_name, private int $priority = 10 ) {}

        public function add( callable $callback, int $accepted_args = 1 ) : bool {
            return \add_filter( $this->hook_name, $callback, $this->priority, $accepted_args );
        }

        public static function up( object $object ) : void {
            foreach ( ( new \ReflectionClass( $object ) )->getMethods( \ReflectionMethod::IS_PUBLIC ) as $method )
                foreach ( $method->getAttributes( __CLASS__ ) as $attribute )
                     $attribute->newInstance()->add( [ $object, $method->getName() ], $method->getNumberOfParameters() );
        }
    }
}