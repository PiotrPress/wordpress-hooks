<?php declare( strict_types = 1 );

namespace PiotrPress\WordPress\Hooks;

use function defined;
use function class_exists;

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( __NAMESPACE__ . '\Action' ) ) {
    class Action extends Filter {}
}