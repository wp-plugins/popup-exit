<?php
/**
 *
 * @package   ChChPopUpClose
 * @author    Chop-Chop.org <shop@chop-chop.org>
 * @license   GPL-2.0+
 * @link      https://shop.chop-chop.org
 * @copyright 2014 
 *
 * @wordpress-plugin
 * Plugin Name:       Pop-Up CC Exit
 * Plugin URI:        http://shop.chop-chop.org
 * Description:       An elegant Pop Up in just a few clicks.
 * Version:           1.1.0
 * Author:            Chop-Chop.org
 * Author URI:        http://chop-chop.org
 * Text Domain:       cc-pop-up-locale
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
 
define( 'CHCH_PFC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CHCH_PFC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-chch-pop-up-close.php' ); 


add_action( 'plugins_loaded', array( 'ChChPopUpClose', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * The code below is intended to to give the lightest footprint possible.
 */
 
	
if (is_admin()) { 
	
	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-chch-pop-up-close-admin.php' );
	add_action( 'plugins_loaded', array( 'ChChPopUpCloseAdmin', 'get_instance' ) );

}
