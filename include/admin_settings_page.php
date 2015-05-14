<?php
//image upload

//enqueue image upload scripts
wp_enqueue_script( 'albdsgprojectplugin-image-upload',untrailingslashit( plugins_url( '../assets/admin/' , __FILE__ ) ).'/js/image_upload.js' ,array( 'jquery', 'media-upload', 'thickbox' ) );
wp_enqueue_style( 'thickbox' );


// form was submited , save option

if( isset( $_POST[Albdesign_Projects_Settings_Option_Page::get_class_meta_name()] ) ) {


	$opts_array = array();
	
	$opts_array['company_name'] 						= (isset($_POST['company_name'])) ? sanitize_text_field($_POST['company_name']) : '';
	$opts_array['company_logo_img'] 					= (isset($_POST['company_logo_img'])) ? sanitize_text_field($_POST['company_logo_img']) : '';
	$opts_array['company_address'] 						= (isset($_POST['company_address'])) ? esc_textarea ($_POST['company_address']) : '';
	$opts_array['company_email'] 						= (isset($_POST['company_email'])) ? sanitize_text_field($_POST['company_email']) : '';
	$opts_array['company_website'] 						= (isset($_POST['company_website'])) ? sanitize_text_field($_POST['company_website']) : '';
	$opts_array['company_mobile'] 						= (isset($_POST['company_mobile'])) ? sanitize_text_field($_POST['company_mobile']) : '';
	$opts_array['invoice_terms'] 						= (isset($_POST['invoice_terms'])) ? esc_textarea($_POST['invoice_terms']) : '';
	$opts_array['invoice_default_vat'] 					= (isset($_POST['invoice_default_vat'])) ? sanitize_text_field($_POST['invoice_default_vat']) : '';
	$opts_array['invoice_default_currency'] 			= (isset($_POST['invoice_default_currency'])) ? sanitize_text_field($_POST['invoice_default_currency']) : '';
	$opts_array['invoice_default_currency_position'] 	= (isset($_POST['invoice_default_currency_position'])) ? sanitize_text_field($_POST['invoice_default_currency_position']) : 'left';
	$opts_array['pdf_template'] 						= (isset($_POST['selected_pdf_template'])) ? sanitize_text_field($_POST['selected_pdf_template']) : 'template1';
	
	
	
	update_option( Albdesign_Projects_Settings_Option_Page::get_class_meta_name() ,$opts_array);
}


	
?>

<div class="wrap" style="background-color: #fff;padding: 20px;">
	<h2>Settings Page</h2>
	<form method="post" >
		<table class="form-table">

			<tr valign="top">
				<th scope="row">Company Name </th>
				<td  colspan="3">
					<input type="text" name="company_name" value="<?php echo Albdesign_Projects_Settings_Option_Page::get('company_name');?>">
				</td>
			</tr>
			
			<tr valign="top">
				<th scope="row">Company Address </th>
				<td  colspan="3">
				
					<textarea class="at-textarea large-text" name="company_address" id="company_address" cols="60" rows="10" autocomplete="off"><?php echo Albdesign_Projects_Settings_Option_Page::get('company_address');?></textarea>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">Company Email </th>
				<td  colspan="3">
					<input type="text" name="company_email" value="<?php echo Albdesign_Projects_Settings_Option_Page::get('company_email');?>">
				</td>
			</tr>	

			<tr valign="top">
				<th scope="row">Company Website </th>
				<td  colspan="3">
					<input type="text" name="company_website" value="<?php echo Albdesign_Projects_Settings_Option_Page::get('company_website');?>">
				</td>
			</tr>		

			<tr valign="top">
				<th scope="row">Company Mobile </th>
				<td  colspan="3">
					<input type="text" name="company_mobile" value="<?php echo Albdesign_Projects_Settings_Option_Page::get('company_mobile');?>">
				</td>
			</tr>			
			
			<tr valign="top">
				<th scope="row">Company Logo </th>
				<td  colspan="3">
	
					<input type="text" name="company_logo_img" id="company_logo_img" value="<?php echo Albdesign_Projects_Settings_Option_Page::get('company_logo_img');?>"> <span id="albdesign_company_image_button" >Upload</span>

					<div id="company_logo_img_preview"></div>
				</td>
			</tr>	

			<tr valign="top">
				<th scope="row">Invoice Template</th>
				<td  colspan="3">
					<?php echo Albdesign_Projects_Settings_Option_Page::list_pdf_templates();?>
				</td>
			</tr>				
			
			
			<tr valign="top">
				<th scope="row">Default Currency </th>
				<td  colspan="3">
					<input type="text" name="invoice_default_currency" value="<?php echo Albdesign_Projects_Settings_Option_Page::get('invoice_default_currency');?>">
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">Currency Position </th>
				<td  colspan="3">
					<select  name="invoice_default_currency_position" >
						<option <?php selected( Albdesign_Projects_Settings_Option_Page::get('invoice_default_currency_position'), 'left'); ?>   value="left">Left of price </option >
						<option  <?php selected( Albdesign_Projects_Settings_Option_Page::get('invoice_default_currency_position'), 'right'); ?>    value="right">Right of price </option>
					</select>
					
				</td>
			</tr>			
			
			<tr valign="top">
				<th scope="row">Default VAT value </th>
				<td  colspan="3">
				
					<input type="text" name="invoice_default_vat" value="<?php echo Albdesign_Projects_Settings_Option_Page::get('invoice_default_vat');?>"> %
				</td>
			</tr>				
			
			<tr valign="top">
				<th scope="row">Invoice Terms </th>
				<td  colspan="3">
				
					<textarea class="at-textarea large-text" name="invoice_terms" id="invoice_terms" cols="60" rows="10" autocomplete="off"><?php echo Albdesign_Projects_Settings_Option_Page::get('invoice_terms');?></textarea>
				</td>
			</tr>			
			
			<tr>
				<td>
					<input type="submit" class="button button-primary button-large" name="<?php echo Albdesign_Projects_Settings_Option_Page::get_class_meta_name();?>"  value="Save Changes">
				</td>
			</tr>
			
			
		</table>
</div>