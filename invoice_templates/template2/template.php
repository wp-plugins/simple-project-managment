<html>
	<head>
	<style type="text/css" >
		<?php echo apply_filters('albwppm_invoice_pdf_css',$css_content); ?>
	</style>
	</head>
	<body>
	
		<table class="invoice_header_table" > 
			<tr class="company_info"> 
				<td style="width:80%"> 

						<div class="invoice_provider"> 
							<?php echo apply_filters('albdesign_projects_invoice_pdf_company_logo','Company'); ?>
						</div>
				
						<?php echo apply_filters('albwppm_invoice_pdf_company_logo',$company_logo); ?>

						<div>	
							<span class="company_name"><?php echo apply_filters('albwppm_invoice_pdf_company_title',$company_name); ?></span>
						</div>
						
						<div>	
							<span class="company_name"><?php echo apply_filters('albwppm_invoice_pdf_company_address',$company_address); ?></span>
						</div>			
				</td>
				<td >  
				
					<div class="invoice_client"> 
						<?php echo apply_filters('albdesign_projects_invoice_pdf_client_title','Client'); ?>
					</div>
					
					<span class="client_name"> 
						<?php echo apply_filters('albwppm_invoice_pdf_client_first_name',$client_first_name); ?> 
						<?php echo apply_filters('albwppm_invoice_pdf_client_middle_name',$client_middle_name); ?> 
						<?php echo apply_filters('albwppm_invoice_pdf_client_last_name',$client_last_name); ?> 
						
					</span>
					
					<div>
						<?php echo apply_filters('albwppm_invoice_pdf_client_address',$client_address); ?>
					</div>
					
				</td>
			</tr>
			<tr>
				<td > &nbsp; </td>
				<td class="invoice_id"> 
					<?php echo apply_filters('albwppm_invoice_pdf_invoice_id','Invoice : #'.$invoice_id, $invoice_id); ?>
				</td>
			</tr>
		</table>

		
		<table class="order_items">
			<thead>
				<tr>
					<td class="description_title"> <?php echo apply_filters('albwppm_invoice_pdf_description_table_header','Description'); ?></td>
					<td> <?php echo apply_filters('albwppm_invoice_pdf_price_table_header','Price'); ?> </td>
					<td> <?php echo apply_filters('albwppm_invoice_pdf_quantity_table_header','Quantity'); ?> </td>
					<td> <?php echo apply_filters('albwppm_invoice_pdf_total_table_header','Total'); ?> </td>
				</tr>	
			</thead>
			<tbody>
				<?php foreach($items_array as $single_item) {  ?>
					<tr>
						<td class="item_name"><?php echo apply_filters('albwppm_invoice_pdf_single_item_name_value',$single_item['ItemName']); ?> </td>
						<td class="unit_cost"> <?php echo apply_filters('albwppm_invoice_pdf_single_item_unit_cost_value',$single_item['ItemUnitCost']); ?> </td>
						<td class="item_quantity"><?php echo apply_filters('albwppm_invoice_pdf_single_item_quantity_value',$single_item['ItemQuantity']); ?> </td>
						<td class="item_total"><?php echo apply_filters('albwppm_invoice_pdf_single_item_total_cost_value',$single_item['ItemTotalCost']); ?></td>
					</tr>
				<?php } ?>
				
				<tr class="before_subtotal_separator"> <td colspan="4" class="no-borders"> &nbsp; </td></tr>
			
				
					<tr>
						<td class="no-borders"> </td>

						<?php if($subtotal!=''){ ?>
							<td colspan="2" class="subtotal_title"> <?php echo apply_filters('albwppm_invoice_pdf_subtotal_table_header','Subtotal'); ?> </td>
							<td><?php echo apply_filters('albwppm_invoice_pdf_subtotal_value',$subtotal); ?> </td>
						<?php } ?>
					</tr>	
				

				<?php if($discount!=''){ ?>
					<tr>
						<td class="no-borders"> &nbsp; </td>
						<td colspan="2"  class="discount_title"> <?php echo apply_filters('albwppm_invoice_pdf_discount_table_header','Discount'); ?> </td>
						<td><?php echo apply_filters('albwppm_invoice_pdf_discount_value',$discount); ?>  </td>
					</tr>	
				<?php } ?>	

				<?php if($subtotal_after_discount !=''){ ?>
					<tr>
						<td class="no-borders"> &nbsp; </td>
						<td colspan="2"  class="subtotal_after_discount_title"> <?php echo apply_filters('albwppm_invoice_pdf_subtotal_after_discount_table_header','Subtotal after discount'); ?> </td>
						<td> <?php echo apply_filters('albwppm_invoice_pdf_subtotal_after_discount_value',$subtotal_after_discount); ?>  </td>
					</tr>	
				<?php } ?>

				<?php if($vat!=''){ ?>
					<tr>
						<td class="no-borders"> &nbsp; </td>
						<td colspan="2"  class="vat_title"> <?php echo apply_filters('albwppm_invoice_pdf_vat_table_header','Discount'); ?> </td>
						<td><?php echo apply_filters('albwppm_invoice_pdf_vat_value',$vat); ?> </td>
					</tr>	
				<?php } ?>


				
			</tbody>
			
			<tfoot>
					<tr class="before_total_separator">
						<td colspan="4" class="no-borders"> &nbsp;  </td>
					</tr>
					
					<?php if($total!=''){ ?>
						<tr >
							<td class="no-borders"> &nbsp; </td>
							<td colspan="2"  class="balance_due_title"> <?php echo apply_filters('albwppm_invoice_pdf_balance_header','Balance'); ?>  </td>
							
							<td> <?php echo $total ; ?>  </td>
						</tr>	
					<?php } ?>	
			</tfoot>

		</table>

		<?php if($invoice_specific_terms!=''){ ?>
			<div > 
				<span class="invoice_specific_terms_title">  <?php echo apply_filters('albwppm_invoice_pdf_specific_terms_header','Specific terms'); ?>   </span>
				<?php echo apply_filters('albwppm_invoice_pdf_specific_terms',$invoice_specific_terms); ?>
			
			</div>
		<?php } ?>

		<?php if($invoice_general_terms_and_conditions!=''){ ?>
			<div > 
				<span class="invoice_general_terms_title">  <?php echo apply_filters('albwppm_invoice_pdf_general_terms_header','General terms'); ?>  </span>
				<?php echo apply_filters('albwppm_invoice_pdf_general_terms',$invoice_general_terms_and_conditions); ?>
				
			</div>
		<?php } ?>
		
		
		<div id="footer">
		
			<?php if($company_name!=''){ ?>
				<div>	
					<span class="company_title"><?php echo apply_filters('albwppm_invoice_pdf_company_title_footer',$company_name); ?></span>
				</div>
			<?php } ?>
			
			<?php if($company_email!=''){ ?>
				<span class="company_email_title"><?php echo apply_filters('albwppm_invoice_pdf_email_footer_header','Email :'); ?> </span><?php echo $company_email; ?>
			<?php } ?>
			
			<?php if($company_website!=''){ ?>
				<span class="company_website_title"><?php echo apply_filters('albwppm_invoice_pdf_email_website_header','Web :'); ?> </span><?php echo $company_website; ?> 
			<?php } ?>
			
			<?php if($company_mobile!=''){ ?>
				<span class="company_mobile_title"><?php echo apply_filters('albwppm_invoice_pdf_email_mobile_header','Email :'); ?> </span><?php echo $company_mobile; ?> 
			<?php } ?>
		</div>

		
	</body>
<html>