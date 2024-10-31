<?php
  add_action('wp_ajax_qubik_save_keys', 'qubik_save_keys', 1);
  add_action('wp_ajax_nopriv_qubik_save_keys', 'qubik_save_keys', 1);
  add_action('woocommerce_checkout_update_order_meta', 'order_sucursal_main_update_order_meta_qubik', 10);
  add_action('add_meta_boxes', 'woocommerce_qubik_box_add_box');
  add_action('wp_ajax_qubik_get_etiqueta', 'qubik_get_etiqueta', 1);
  add_action('wp_ajax_nopriv_qubik_get_etiqueta', 'qubik_get_etiqueta', 1);   
  add_action('wp_ajax_qubik_imponer', 'qubik_imponer', 1);
  add_action('wp_ajax_nopriv_qubik_imponer', 'qubik_imponer', 1); 
  add_action('wp_ajax_qubik_get_rate', 'qubik_get_rate', 1);
  add_action('wp_ajax_nopriv_qubik_get_rate', 'qubik_get_rate', 1);
  add_action('woocommerce_api_qubikreturn', 'callback_handler_qubik' );
  add_action( 'wp_footer', 'qubik_checkout_extra');
  add_action( 'woocommerce_after_order_notes', 'qubik_order_sucursal_main' );
  add_action('wp_ajax_qubik_get_map', 'qubik_get_map', 1);
  add_action('wp_ajax_nopriv_qubik_get_map', 'qubik_get_map', 1); 
  add_action('wp_ajax_qubik_check_sucursales', 'qubik_check_sucursales', 1);
  add_action('wp_ajax_nopriv_qubik_check_sucursales', 'qubik_check_sucursales', 1);
  add_action('wp_ajax_check_sucursales', 'check_qubik_sucursales', 1);
  add_action('wp_ajax_nopriv_check_sucursales', 'check_qubik_sucursales', 1);


  add_action('wp_ajax_check_admision_qubik', 'check_admision_qubik', 1);
  add_action('wp_ajax_nopriv_check_admision_qubik', 'check_admision_qubik', 1); 

  function qubik_order_sucursal_main( $checkout ) {
    global $woocommerce, $wp_session;
  

    echo '<div id="order_sucursal_main" style="display:none; margin-bottom: 50px;"><img class="qubik-logo" src="'. plugins_url( 'assets/img/qubik.png', __FILE__ ) . '"></br></br></br>';
      echo '<small>Elegí tu punto Qubik en el listado.</small>';
      echo '<div id="order_sucursal_main_result_cargando">Cargando Puntos...';echo '</div>';
      echo '<div id="order_sucursal_main_result" style="display:none;">Cargando Puntos...';echo '</div>';
    echo '</div>';
  
  }

  function qubik_checkout_extra(){ 
    if ( is_checkout() ) { ?>
      <script type="text/javascript">
        jQuery(document).ready(function () {  
        jQuery('#calc_shipping_postcode').attr({ maxLength : 4 });
        jQuery('#billing_postcode').attr({ maxLength : 4 });
        jQuery('#shipping_postcode').attr({ maxLength : 4 });

              jQuery("#calc_shipping_postcode").keypress(function (e) {
              if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
              }
              });
              jQuery("#billing_postcode").keypress(function (e) { 
              if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) { 
              return false;
              }
              });
              jQuery("#shipping_postcode").keypress(function (e) {  
              if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
              return false;
              }
              });
          
                  
            jQuery('#billing_postcode').focusout(function () {
              if (jQuery('#ship-to-different-address-checkbox').is(':checked')) {
                var state = jQuery('#shipping_state').val();
                var post_code = jQuery('#shipping_postcode').val();
              } else {
                var state = jQuery('#billing_postcode').val();
                var post_code = jQuery('#billing_postcode').val();
              }
              
              var selectedMethod = jQuery('input:checked', '#shipping_method').attr('id');
              var selectedMethodb = jQuery( "#order_review .shipping .shipping_method option:selected" ).val();
              if (selectedMethod == null) {
                  if(selectedMethodb != null){
                    selectedMethod = selectedMethodb;
                  } else {
                    return false;
                  }
              }           
        });
        });

 

        jQuery(document).ready(toggleCustomBox);
        jQuery(document).on('change', '#shipping_method input:radio', toggleCustomBox);
        jQuery(document).on('change', '#order_review .shipping .shipping_method', toggleCustomBox);
        
      </script>

      <style type="text/css">
         #order_sucursal_main h3 {
            text-align: left;
            padding: 5px 0 5px 115px;
        }
        .qubik-logo {
          position: absolute;
          margin: 0px;
        }
      </style>
    <?php }
  } 

   
    add_filter('woocommerce_billing_fields', 'qubik_dni_woocommerce_billing_fields');

    function qubik_dni_woocommerce_billing_fields($fields) {

        $fields['billing_dni'] = array(
            'label' => __('DNI', 'woocommerce'), // Add custom field label
            'placeholder' => _x('Ingrese su DNI', 'placeholder', 'woocommerce'), // Add custom field placeholder
            'required' => true, // if field is required or not
            'clear' => false, // add clear or not
            'type' => 'text', // add field type
            'class' => array('my-dni')    // add class name
        );

        return $fields;
    }

    add_action( 'woocommerce_admin_order_data_after_billing_address', 'qubik_dni_display_admin_order_meta', 10, 1 );

    function qubik_dni_display_admin_order_meta($order) {
        echo '<p><strong>'.__('DNI').':</strong> ' . get_post_meta( $order->get_id(), '_billing_dni', true ) . '</p>';
    }
  
  function qubik_register_session(){
      if( !session_id() )
          @session_start();
  }
  add_action('init','qubik_register_session');

  add_action('woocommerce_checkout_process', 'validate_qubik_process');
  function validate_qubik_process() {
    global $woocommerce, $wp_session;
    $chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
    $_SESSION['chosen_shipping'] = json_encode($chosen_methods[0] );
 

  }


  function order_sucursal_main_update_order_meta_qubik( $order_id ) {
    global $woocommerce, $post;
    if( !session_id() )
          session_start();
   
        update_post_meta( $order_id, '_chosen_shipping', $_SESSION['chosen_shipping'] );
     
  }
 

  function woocommerce_qubik_box_add_box() {
    add_meta_box( 'woocommerce-qubik-box', __( 'Detalles Qubik', 'woocommerce-qubik' ), 'woocommerce_qubik_box_create_box_content', 'shop_order', 'side', 'default' );
  }

  function woocommerce_qubik_box_create_box_content() {
    global $post;
      $site_url = get_site_url();
      $order = wc_get_order( $post->ID );
      $qubik_settings = get_post_meta($post->ID, 'qubik_settings', true);
      $shipping = $order->get_items( 'shipping' );
      $qubik_settings = json_decode($qubik_settings, true); 
          
      $transaccionid = get_post_meta($post->ID, '_qubik_transaccionid', true);
      $codigotransaccion = get_post_meta($post->ID, '_qubik_codigotransaccion', true);
      $puntoentrega = get_post_meta($post->ID, 'puntoentrega', true);
      $sucursal = get_post_meta($post->ID, 'sucursal', true);
    
 
      echo '<div class="qubik-single">';
      echo '<strong>Modalidad de envío</strong></br>';
      foreach($shipping as $method){
        if($method['method_id'] == 'qubik_wanderlust'){
          
          if($puntoentrega){
            echo '<strong>Retira por </strong></br>' . $sucursal;
          } else {
             echo $method['name'] .'</br></br>';
            
             echo '<strong>Enviar a:</strong></br>';
            print_r($order->get_formatted_shipping_address());
          }
            
          $shipping_settings_id = $method['instance_id'];         
        }
      }
    
      $shipping_settings = get_option('woocommerce_qubik_wanderlust_'.$shipping_settings_id.'_settings', true);   
        
      echo '</div>';

 
        //ETIQUETA
        $qubik_shipping_label_tracking = get_post_meta($post->ID, '_tracking_number', true);
        $qubik_shipping_label_tracking_link = get_post_meta($post->ID, '_custom_tracking_link', true);
        $etiqueta_url = get_post_meta($post->ID, '_etiqueta_qubik', true); 
               
        if(!empty($etiqueta_url)){
          echo  '<div style="position: relative; width: 100%; height: 60px;" ><a style=" width: 100%; text-align: center; background: #ffc107; color: white; padding: 10px; margin: 10px 0px; float: left; text-decoration: none; box-sizing: border-box;" href="'. $etiqueta_url .'" target="_blank">Imprimir Etiqueta</a></div>';
        }
        
        if (empty($etiqueta_url) ){ ?>

        <style type="text/css">
          #generar-qubik, #editar-qubik, #manual-qubik-generar, #obtener-a-qubik, #obtener-m-qubik  {
            background: #337ab7;
            color: white;
            width: 100%;
            text-align: center;
            height: 40px;
            padding: 0px;
            line-height: 37px;
            margin-top: 20px;
            clear:both;
          }
          #editar-qubik {
            background: #d24040;
          }
          #manual-qubik {
            display:none;
          }

        </style>

        <img id="qubik_loader" style="display:none; max-width: 65px; height: auto; margin: 10px 95px; position: relative;" src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/img/qubik.png'; ?>">

        <div id="obtener-a-qubik" class="button" data-id="<?php echo $post->ID; ?>">Obtener Etiqueta</div>


        <div class="qubik-single-label"> </div> 

        <script type="text/javascript">   

          jQuery('body').on('click', '#obtener-a-qubik',function(e){ 
            e.preventDefault();
            var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
            var dataid = jQuery(this).data("id");
            var metodo = jQuery(this).data("metodo");
            jQuery(this).hide();
            jQuery('#qubik_loader').fadeIn();
            jQuery.ajax({
              type: 'POST',
              cache: false,
              url: ajaxurl,
              data: {action: 'qubik_imponer',dataid: dataid,},
              success: function(data, textStatus, XMLHttpRequest){ 
                jQuery('#qubik_loader').fadeOut();
                jQuery(".qubik-single-label").fadeIn(400);
                jQuery(".qubik-single-label").html('');
                jQuery(".qubik-single-label").append(data);
              },
              error: function(MLHttpRequest, textStatus, errorThrown){ }
            });
          });         

        </script>
      <?php }  
    
  }

function get_token($user, $pass){
 
  
     $data = array(
                "username" => $user,
                "password" => $pass,
                "rememberMe" => 'false',
                "authority" => 'ROLE_SHOP_ADMIN'
            );
            $body = json_encode($data);
        
     
    $endpoint = 'https://publicapi.qubik-tech.com/api/authenticate';

 
    $options = [
        'body'        => $body,
        'headers'     => [
          'Content-Type'  => 'application/json'
        ],
        'timeout'     => 60,
        'redirection' => 5,
        'blocking'    => true,
        'httpversion' => '1.0',
        'sslverify'   => false,
        'data_format' => 'body',
    ];
 

    
    $response = wp_remote_post( $endpoint, $options );  
     
  return $response['body'];

}


function check_admision_qubik(){
  global $woocommerce, $post, $wp_session;
    $order_id  = $_POST['dataid'];
        
    $delivery_zones = WC_Shipping_Zones::get_zones();

    foreach($delivery_zones as $zones){
        foreach($zones['shipping_methods'] as $methods){ 
          if($methods->id =='qubik_wanderlust'){
            if($methods->enabled == 'yes'){
              $api_key = $methods->instance_settings['api_key'];
              $user_key = $methods->instance_settings['user_key'];
              $secret_key = $methods->instance_settings['secret_key'];
            }
          }
        }
    }
 
   
    $token = get_token($user_key, $secret_key);
    $token = json_decode($token);
    $token = $token->id_token;
  
  
    $endpoint = 'https://publicapi.qubik-tech.com/api/shop-depots';

    $body = $dataenvia;

    $options = [
        'body'        => $body,
        'headers'     => [
          'Authorization' => 'Bearer '. $token,
          'Content-Type'  => 'application/json'
        ],
        'timeout'     => 60,
        'redirection' => 5,
        'blocking'    => true,
        'httpversion' => '1.0',
        'sslverify'   => false,
        'data_format' => 'body',
    ];
 

    
    $response = wp_remote_get( $endpoint, $options ); 
  echo '<pre style="display:none;">';print_r($body);echo' $body</pre>';
  echo '<pre style="display:none;">';print_r($token);echo' $token</pre>';
  echo '<pre style="display:none;">';print_r($response);echo'</pre>';
   if ( !is_wp_error( $response ) ) {
      $response = json_decode($response['body']);
echo '<select id="pv_centro_qubik_estandar" name="pv_centro_qubik_estandar">';

     foreach($response as $sucursales){
 
          echo '<option value="'. $sucursales->id.'">'. $sucursales->name . '</option>';
        }
      
        echo '</select>'; 
     
   }
  
      
      
        
  die();
  
}

  /* GENERAR ETIQUETA */
  function qubik_imponer() { 
    global $woocommerce, $post, $wp_session;
    $order_id  = $_POST['dataid'];
    
    $origen_datos = get_post_meta($order_id, '_origen_datos', true);
    $puntoentrega = get_post_meta($order_id, 'puntoentrega', true);
    $chosen_shipping = get_post_meta($order_id, '_chosen_shipping', true);
    $dhi = get_post_meta($order_id, '_billing_dni', true);
    $paquetes = get_post_meta($order_id, 'paquetes', true);
    
    $delivery_zones = WC_Shipping_Zones::get_zones();

    foreach($delivery_zones as $zones){
        foreach($zones['shipping_methods'] as $methods){ 
          if($methods->id =='qubik_wanderlust'){
            if($methods->enabled == 'yes'){
              $api_key = $methods->instance_settings['api_key'];
              $user_key = $methods->instance_settings['user_key'];
              $secret_key = $methods->instance_settings['secret_key'];
        $deposito_id = $methods->instance_settings['deposito_id'];
            }
          }
        }
    }
 
    $order = wc_get_order( $order_id ); 
  
    $token = get_token($user_key, $secret_key);
    $token = json_decode($token);
    $token = $token->id_token;
    
           $data = array(
                "Apellido" => $order->get_shipping_last_name(),
                "Nombre" => $order->get_shipping_first_name(),
                "Calle" => $order->get_shipping_address_1(),
          "Referencias"  => $order->get_shipping_address_2(),
                "Numero" => '0',
                "Cantidad_Bultos" => 1,
                "Ciudad" => $order->get_shipping_city(),
                "Codigo_Postal" => $order->get_shipping_postcode(),
                "Id_Interno" => 'WC-'.$order_id,
          "originId" => $deposito_id,
                "Mail" => $order->get_billing_email(),
                "Provincia" => 'cordoba',
                "Telefono" => $order->get_billing_phone(),
                "Valor" => $order->get_total(),
            );
            $datas = json_encode($data);
        
            $dataenvia = '['.$datas.']';
 
    
    $endpoint = 'https://publicapi.qubik-tech.com/api/public-order/multiple';

    $body = $dataenvia;

    $options = [
        'body'        => $body,
        'headers'     => [
          'Authorization' => 'Bearer '. $token,
          'Content-Type'  => 'application/json'
        ],
        'timeout'     => 60,
        'redirection' => 5,
        'blocking'    => true,
        'httpversion' => '1.0',
        'sslverify'   => false,
        'data_format' => 'body',
    ];
 

    
    $response = wp_remote_post( $endpoint, $options );  
   
    if ( !is_wp_error( $response ) ) {

      update_post_meta( $order_id, 'qubik_results', $response['body']);

      $response = json_decode($response['body']);
      
      $response_pdf = wp_remote_get( 'https://publicapi.qubik-tech.com/api/order/pdf/'.$response[0]->id, $options );
 
       
       $save_path = plugin_dir_path ( __DIR__ ) . 'etiquetas/';
            $save_url = plugin_dir_url(dirname(__FILE__)) . 'etiquetas/';          
            $pdfdata = base64_decode($response_pdf['body']);
             file_put_contents($save_path . $response[0]->id . '.pdf', $pdfdata);
            $date = strtotime( date('Y-m-d') );


            $etiqueta =  $save_url . $response[0]->id .'.pdf';
            
            
            $date = strtotime( date('Y-m-d') );
            update_post_meta($order_id, '_tracking_number', $response[0]->id);
            update_post_meta($order_id, '_custom_tracking_provider', 'Qubik'); 
            update_post_meta($order_id, '_custom_tracking_link', 'https://#'. $response[0]->id);
            update_post_meta($order_id, '_date_shipped', $date);
            update_post_meta($order_id, '_etiqueta_qubik', $etiqueta);
              
       
           
          echo  '<div  style="position: relative; width: 100%; height: 60px;" ><a style=" width: 225px; text-align: center;background: #643494;color: white;padding: 10px;margin: 10px;float: left;text-decoration: none;" href="'. $etiqueta .'" target="_blank">IMPRIMIR ETIQUETA</a></div>';
          echo  '<div  style="position: relative; width: 100%; height: 60px;" ><a style=" width: 225px; text-align: center;background: #643494;color: white;padding: 10px;margin: 10px;float: left;text-decoration: none;" href="#" target="_blank">'.$response[0]->id.'</a></div>';

    }
 
     
         
    
      die();
          
    
    
     
  }

 
  function qubik_admin_notice() {
    global $wp_session;
      ?>
      <div class="notice error my-acf-notice is-dismissible" >
          <p><?php print_r($wp_session['qubik_notice'] ); ?></p>
      </div>
      <?php
  }


            add_action( 'woocommerce_api_qubikreturn', 'webhook_call' );

 
function webhook_call() {
 
          header( 'HTTP/1.1 200 OK' );
      $postBody = file_get_contents('php://input');
          $responseipn = json_decode($postBody);
  
  
  if($responseipn->order->clientReference){
    
    $search = 'WC-' ;
    $order_id = str_replace($search, '', $responseipn->order->clientReference) ;
    $order = wc_get_order($order_id);
    update_post_meta($order_id, 'estado', $responseipn->status );
    update_post_meta($order_id, 'estado_full', $postBody );
    $order->add_order_note( 'Qubik - Estado del envio: ' . $responseipn->status);
    
    if($responseipn->status == 'DELIVERED'){
      $order->update_status( 'wc-completed' );
    }
    
  }
          
  
        }
   


?>