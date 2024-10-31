<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
 
return array(
	'enabled'           => array(
		'title'           => __( 'Activar Qubik', 'woocommerce-shipping-qubik' ),
		'type'            => 'checkbox',
		'label'           => __( 'Activar este método de envió', 'woocommerce-shipping-qubik' ),
		'default'         => 'no'
	),
	'qpoints'           => array(
		'title'           => __( 'Activar Qubik Points', 'woocommerce-shipping-qubik' ),
		'type'            => 'checkbox',
		'label'           => __( 'Activar Qubik Points', 'woocommerce-shipping-qubik' ),
		'default'         => 'no'
	),
	'title'             => array(
		'title'           => __( 'Título', 'woocommerce-shipping-qubik' ),
		'type'            => 'text',
		'description'     => __( 'Controla el título que el usuario ve durante el pago.', 'woocommerce-shipping-qubik' ),
		'default'         => __( 'Qubik', 'woocommerce-shipping-qubik' ),
		'desc_tip'        => true
	),
	
  'api'              => array(
		'title'           => __( 'Configuración de la API', 'woocommerce-shipping-qubik' ),
		'type'            => 'title',
		'description'     => __( '', 'woocommerce-shipping-qubik' ),
  ),
 	
  'user_key'     => array(
		'title'           => __( 'USUARIO', 'woocommerce-shipping-qubik' ),
		'type'            => 'text',
		'description'     => __( 'Usuario provisto por Qubik.', 'woocommerce-shipping-qubik' ),
		'default'         => __( '', 'woocommerce-shipping-qubik' ),
    'placeholder' => __( '', 'meta-box' ),
  ),	
  
  'secret_key'     => array(
		'title'           => __( 'CLAVE', 'woocommerce-shipping-qubik' ),
		'type'            => 'text',
		'description'     => __( 'Clave provisto por Qubik.', 'woocommerce-shipping-qubik' ),
		'default'         => __( '', 'woocommerce-shipping-qubik' ),
    'placeholder' => __( '', 'meta-box' ),
  ),		
    'deposito_id'     => array(
		'title'           => __( 'ID Deposito', 'woocommerce-shipping-qubik' ),
		'type'            => 'text',
		'description'     => __( 'Obtener el deposito en el boton que esta abajo.', 'woocommerce-shipping-qubik' ),
		'default'         => __( '', 'woocommerce-shipping-qubik' ),
    'placeholder' => __( '', 'meta-box' ),
  ),	
 'services'  => array(
        'type'            => 'service'
      ),
 
   
);

