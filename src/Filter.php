<?php declare( strict_types = 1 );

namespace PiotrPress\WordPress\Hooks;

\defined( 'ABSPATH' ) or exit;

if ( ! \class_exists( __NAMESPACE__ . '\Filter' ) ) {
    #[ \Attribute( \Attribute::TARGET_FUNCTION | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE ) ]
    class Filter {
        public function __construct( private string $name, private int $priority = 10 ) {}

        public function add( callable $callback, int $count = 1 ) : bool {
            return \add_filter( $this->name, $callback, $this->priority, $count );
        }

        public function remove( callable $callback, int $count = 1 ) : bool {
            return \remove_filter( $this->name, $callback, $this->priority, $count );
        }
    }
}