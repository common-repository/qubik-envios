<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
 
class WC_Shipping_Qubik extends WC_Shipping_Method {

	/**
	 * Constructor
	 */
	public function __construct( $instance_id = 0 ) {
		
		$this->id                   = 'qubik_wanderlust';
		$this->instance_id 			 		= absint( $instance_id );
		$this->method_title         = __( 'Qubik', 'woocommerce-shipping-qubik' );
 		$this->method_description   = __( 'Qubik te permite cotizar el valor de un envío.', 'woocommerce' );
		$this->supports             = array(
			'shipping-zones',
			'instance-settings',
		);

		$this->init();
		
 		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );

	}
 
	public function init() {
		// Load the settings.
		$this->init_form_fields = include( 'data/data-settings.php' );
		$this->init_settings();
		$this->instance_form_fields = include( 'data/data-settings.php' );
	 
		// Define user set variables
		$this->title           = $this->get_option( 'title', $this->method_title );
		$this->api_key    		 = $this->get_option( 'api_key' );
 		$this->user_key    	 = $this->get_option( 'user_key' );
 		$this->secret_key    	 = $this->get_option( 'secret_key' );
		$this->origin_contacto = $this->get_option( 'origin_contacto' );
		$this->origin_email		 = $this->get_option( 'origin_email' );     
		$this->usa_seguro			 = ( $bool = $this->get_option( 'usa_seguro' ) ) && $bool == 'yes' ? true : false;
		$this->origin_observaciones	 = $this->get_option( 'origin_observaciones' );
    	$this->settings_envio_tipo = $this->get_option( 'settings_envio_tipo' );
    	$this->settings_etiquetas = $this->get_option( 'settings_etiquetas' ); 
 		$this->services        = $this->get_option( 'services', array( ));
    	$this->qpoints         = $this->get_option( 'qpoints' );
	}
 
	public function admin_options() {
		parent::admin_options();
	}  
  
 
	public function generate_service_html() {
		ob_start();
		include( 'data/services.php' );
		return ob_get_clean();
	}

 
		public function validate_service_field( $key ) {
						
 		$service_name       = isset( $_POST['service_name'] ) ? $_POST['service_name'] : array();
		$service_operativa  = isset( $_POST['service_seguro'] ) ? $_POST['service_seguro'] : array();
		$service_sucursal   = isset( $_POST['woocommerce_qubik_wanderlust_modalidad'] ) ? $_POST['woocommerce_qubik_wanderlust_modalidad'] : array();
		$service_enabled    = isset( $_POST['service_enabled'] ) ? $_POST['service_enabled'] : array();
		$service_gratis     = isset( $_POST['service_gratis'] ) ? $_POST['service_gratis'] : array();
		$services = array();

		if ( ! empty( $service_name ) && sizeof( $service_name ) > 0 ) {
			for ( $i = 0; $i <= max( array_keys( $service_name ) ); $i ++ ) {

				if ( ! isset( $service_name[ $i ] ) )
					continue;
		
				if ( $service_name[ $i ] ) {
  					$services[] = array(
 						'service_name'     =>  $service_name[ $i ],
            			'service_gratis'     =>  $service_gratis[ $i ],
						'seguro'     => isset( $service_operativa[ $i ] ) ? true : false,
						'woocommerce_qubik_wanderlust_modalidad' =>  $service_sucursal[ $i ] ,  
						'enabled'    => isset( $service_enabled[ $i ] ) ? true : false
					);
				}
			}
 
		}
			
		return $services;
	}

 
	public function calculate_shipping( $package = array() ) {
		global $wp_session, $woocommerce;
    	$customer = $woocommerce->customer;
   
 	  	$dimension_unit = esc_attr( get_option('woocommerce_dimension_unit' ));
 		$weight_unit = esc_attr( get_option('woocommerce_weight_unit' ));
 		$weight_multi = 0;
		$dimension_multi = 0;
 		if ($dimension_unit == 'm') { $dimension_multi =  100;}
 		if ($dimension_unit == 'cm') {  $dimension_multi =  1;}
 		if ($dimension_unit == 'mm') { $dimension_multi =  0.1;}
 		if ($weight_unit == 'kg') { $weight_multi =  1000;}
 		if ($weight_unit == 'g') {  $weight_multi =  1;}   
 		
 		$cart = $woocommerce->cart;
 		$items = $woocommerce->cart->get_cart();  
 
 		$articulos = array();
    	$productidweights = 0;
    
 		foreach($items as $item => $values) {

 		  $_product = wc_get_product( $values['product_id'] );
 		  $productidweights += $_product->get_weight() * $values['quantity'];
        
 		  $i = 0;
 		  $array = array();
 		  while ($i++ < $values['quantity'])
 		  {
           $articulos[] = array(
            'quantity' => 1,
            'declaredValue' => $_product->get_price(),
            'sizeHeight' => $_product->get_height() * $dimension_multi,
            'sizeWidth' => $_product->get_width() * $dimension_multi,
            'sizeDepth' => $_product->get_length() * $dimension_multi,
            'weight' => $_product->get_weight(),
            'weightUnit' => strtoupper($weight_unit),
          ); 
 		  }

 		}
     
          
 		if(!empty($package['destination']['postcode'])){  
      $_SESSION['Packages'] = json_encode($articulos);                  
      $params = array(
        "method" => array(
          "get_rates" => array(
            'api_key' => $this->api_key,
            'user_key' => $this->user_key,  
            'secret_key' => $this->secret_key,  
            'destZip' => $package['destination']['postcode'],
            'Packages' => $articulos, 
          )
        )
      );

      $qubik_response = wp_remote_post( 'https://wanderlust.codes/qubik/index.php', array(
        'body'    => $params,
      ) );
                
      if ( !is_wp_error( $qubik_response ) ) {
        $qubik_response = json_decode($qubik_response['body']);
         
        	$rate = array(
						'id' => sprintf("%s", $qubik_response->id ),
						'label' => sprintf("%s", 'Envio a domicilio Qubik'),
						'cost' => $qubik_response->cost,
						'calc_tax' => 'per_item',
						'package' => $package,
					);			
					if($qubik_response->cost > 1 ){            
						$this->add_rate( $rate );
					}
        
          
        
        if($this->qpoints == 'yes'){
          if('5009' == $package['destination']['postcode']){
  			    $punto = 'Blackpool cerro - Manuel E. Pizarro 2095';
          }
 	        if('5000' == $package['destination']['postcode']){
  			    $punto = 'Qubik - 25 de Mayo 589 esquina Bv Guzman';
          }         
	        if('5149' == $package['destination']['postcode']){
  			    $punto = 'Lomas de los carolinos - Estancia la Vigia';
          }
          
          $rate = array(
						'id' => sprintf("%s", 'woo_qubikenvios_sucursal' ),
						'label' => sprintf("%s", 'Envio a punto Qubik - '.$punto),
						'cost' => $qubik_response->cost / 2,
						'calc_tax' => 'per_item',
						'package' => $package,
					);			
					if($qubik_response->cost > 1 ){            
						$this->add_rate( $rate );
					}
        }
      }
    }
 	}
}