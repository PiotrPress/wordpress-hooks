<?php declare( strict_types = 1 );

namespace PiotrPress\WordPress\Hooks;

\defined( 'ABSPATH' ) or exit;

if ( ! \class_exists( __NAMESPACE__ . '\Action' ) ) {
    #[ \Attribute( \Attribute::TARGET_FUNCTION | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE ) ]
    class Action extends Filter {}
}