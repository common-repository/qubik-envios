<?php
/*
	Plugin Name: Wanderlust Qubik
	Plugin URI: https://qubik-tech.com/
	Description: Qubik te permite cotizar el valor de un envío.
	Version: 0.2
	Author: Wanderlust Web Design
	Author URI: https://wanderlust-webdesign.com
	WC tested up to: 6.3.1
	Copyright: 2007-2022 wanderlust-webdesign.com
*/

require_once( 'includes/functions.php' );

/**
 * Plugin page links
*/
function woocommerce_qubik_plugin_links( $links ) {

	$plugin_links = array(
		'<a href="http://wanderlust-webdesign.com/">' . __( 'Soporte', 'woocommerce-shipping-qubik' ) . '</a>',
	);

	return array_merge( $plugin_links, $links );
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'woocommerce_qubik_plugin_links' );

/**
 * WooCommerce is active
*/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	/**
	 * woocommerce_init_shipping_table_rate function.
	 */
	function woocommerce_qubik_init() {
		include_once( 'includes/class-wc-shipping-qubik.php' );
	}
  add_action( 'woocommerce_shipping_init', 'woocommerce_qubik_init' ); 

	/**
	 * woocommerce_qubik_add_method function.
	 */
	function woocommerce_qubik_add_method( $methods ) {
		$methods[ 'qubik_wanderlust' ] = 'WC_Shipping_Qubik';
		return $methods;
	}

	add_filter( 'woocommerce_shipping_methods', 'woocommerce_qubik_add_method' );

	/**
	 * woocommerce_qubik_scripts function.
	 */
	function woocommerce_qubik_scripts() {
		wp_enqueue_script( 'jquery-ui-sortable' );
	}

	add_action( 'admin_enqueue_scripts', 'woocommerce_qubik_scripts' );
	
	$qubik_settings = get_option( 'woocommerce_qubik_settings', array() );
	
}