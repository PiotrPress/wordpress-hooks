<?php declare( strict_types = 1 );

namespace PiotrPress\WordPress\Hooks;

use function spl_autoload_register;
use function strpos;
use function substr;
use function strlen;
use function version_compare;

spl_autoload_register( function( $class_name ) {
    if ( 0 !== strpos( $class_name, __NAMESPACE__ ) ) return;
    $class_name = substr( $class_name, strlen( __NAMESPACE__ ) + 1 );

    switch ( true ) {
        case ( version_compare( PHP_VERSION, $php_version = '8.0', '>=' ) ) :
        case ( version_compare( PHP_VERSION, $php_version = '7.4', '>=' ) ) : break;
        default : return;
    }

    if ( file_exists( $file_name = __DIR__ . DIRECTORY_SEPARATOR . 'PHP' . $php_version . DIRECTORY_SEPARATOR . $class_name . '.php' ) )
        include $file_name;
} );