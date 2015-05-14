<div class="wrap" style="background-color: #fff;padding: 20px;">
	<h2>Invoices Tabs</h2>

		<table class="form-table albdesign_projects_reports_page">

			<tr valign="top">
				<td class="albdesign_projects_report_projects_tab_form">
					<form method="post" >

						<div>
							<div class="albdesign_input">
								<div class="albdesign_input_header">For Client</div>
								<?php Albdesign_Projects_Clients_Helpers::get_all_as_dropdown() ;?>
							</div>
							
							<div class="albdesign_input">
								<div class="albdesign_input_header">Project</div>							
								<?php Albdesign_Projects_Project_Helpers::get_all_as_dropdown() ;?>
							</div>	
							
							<div class="albdesign_input">
								<div class="albdesign_input_header">Status</div>	
								<?php echo Albdesign_Projects_Invoice_Helpers::dropdown_paid_status(); ?>				

							</div>								
							
							<div class="albdesign_input">
								<div class="albdesign_input_header">&nbsp;</div>							
								<input type="submit" class="button button-primary button-large" name="<?php echo Albdesign_Projects_Reports_Page::get_slug();?>"  value="Search Invoices">
							</div>
							
							<div class="albdesign_clear"></div>
						</div>
						
					</form>
				</td>
			</tr>
		</table>
		
		
		<table>
			<tr>
				<td>
					<?php echo Albdesign_Projects_Invoice_Helpers::get_results_for_report(); ?>
				</td>
			</tr>
		</table>
		
</div>

