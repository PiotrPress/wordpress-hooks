<?php declare( strict_types = 1 );

namespace PiotrPress\WordPress\Hooks;

\defined( 'ABSPATH' ) or exit;

if ( ! \class_exists( __NAMESPACE__ . '\Filter' ) ) {
    #[ \Attribute( \Attribute::TARGET_FUNCTION | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE ) ]
    class Filter {
        public function __construct( private string $hook_name, private int $priority = 10 ) {}

        public function add( callable $callback, int $accepted_args = 1 ) : bool {
            return \add_filter( $this->hook_name, $callback, $this->priority, $accepted_args );
        }

        public function remove( callable $callback, int $accepted_args = 1 ) : bool {
            return \remove_filter( $this->hook_name, $callback, $this->priority, $accepted_args );
        }
    }
}