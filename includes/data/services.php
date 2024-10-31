 
<tr valign="top" id="packing_options">
	

	<th scope="row" class="titledesc"><?php _e( 'Modalidades de envio', 'wc_wanderlust' ); ?></th>
	<td class="forminp">
		 
			<a href="#" id="depositos" class="button deposito">OBTENER DEPOSITO</a>

		
		  
		<script type="text/javascript">
  			jQuery(document).ready(function () {
 			 
				jQuery('.deposito').click(function(){
					jQuery('.deposito').html('BUSCANDO DEPOSITOS');
					 
					var test = 'test';
						jQuery.ajax({
				    		type: 'POST',
				    		cache: false,
								url: ajaxurl,
				    		data: {
 									action: 'check_admision_qubik',
									test: test,
				    		},
				    		success: function(data, textStatus, XMLHttpRequest){
									jQuery("#depositos").hide();
								
										 
											jQuery("#depositos").parent().append(data);
 											 
												var selectList = jQuery('#pv_centro_qubik_estandar option');
												 
												jQuery('#pv_centro_qubik_estandar').html(selectList);
												jQuery("#pv_centro_qubik_estandar").prepend("<option value='0' selected='selected'>Depositos Disponibles</option>");	
										},
								error: function(MLHttpRequest, textStatus, errorThrown){
									alert(errorThrown);
										}
						});
				});	
				
		
	 			   

			});
			
			jQuery(document).on('change', '#pv_centro_qubik_estandar', function() {

		 
				var conceptName = jQuery('#pv_centro_qubik_estandar').find(":selected").val();
				 
				jQuery('#woocommerce_qubik_wanderlust_deposito_id').val(conceptName);
					
					
				});
		</script>
	</td>
</tr>