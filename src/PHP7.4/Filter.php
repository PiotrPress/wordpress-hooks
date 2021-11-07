<?php declare( strict_types = 1 );

namespace PiotrPress\WordPress\Hooks;

use function defined;
use function class_exists;

use function add_filter;

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( __NAMESPACE__ . '\Filter' ) ) {
    class Filter {
        private string $tag;
        private int $priority = 10;

        public function __construct( string $tag, int $priority = 10 ) {
            $this->tag = $tag;
            $this->priority = $priority;
        }

        public function add( callable $function_to_add, int $accepted_args = 1 ) : void {
            add_filter( $this->tag, $function_to_add, $this->priority, $accepted_args );
        }
    }
}