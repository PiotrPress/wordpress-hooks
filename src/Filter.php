<?php declare(strict_types=1);

namespace PiotrPress\WordPress\Hooks;

use Attribute;

use function defined;
use function class_exists;

use function add_filter;

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( __NAMESPACE__ . '\Filter' ) ) {
    #[Attribute(Attribute::TARGET_METHOD|Attribute::IS_REPEATABLE)]
    class Filter {
        public function __construct(
            private string $tag,
            private int $priority = 10
        ) {}

        public function add( callable $function_to_add, int $accepted_args = 1 ) : void {
            add_filter( $this->tag, $function_to_add, $this->priority, $accepted_args );
        }
    }
}