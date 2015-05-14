<div class="wrap" style="background-color: #fff;padding: 20px;">
	<h2>Clients Tabs</h2>

		<table class="form-table albdesign_projects_reports_page">

			<tr valign="top">
				<td class="albdesign_projects_report_projects_tab_form">
					<form method="post" >

						<div>
							<div class="albdesign_input">
								<div class="albdesign_input_header">Client</div>
								<?php Albdesign_Projects_Clients_Helpers::get_all_as_dropdown() ;?>
							</div>
							
							<div class="albdesign_input">
								<div class="albdesign_input_header">Name</div>							
								<?php echo Albdesign_Projects_Clients_Helpers::create_input('first_name'); ?>
							</div>	
							
							<div class="albdesign_input">
								<div class="albdesign_input_header">Surname</div>	
								<?php echo Albdesign_Projects_Clients_Helpers::create_input('last_name'); ?>				

							</div>								
							
							<div class="albdesign_input">
								<div class="albdesign_input_header">Email</div>							
								<?php echo Albdesign_Projects_Clients_Helpers::create_input('email'); ?>	
							</div>
							
							<div class="albdesign_input">
								<div class="albdesign_input_header">Phone</div>
									<?php echo Albdesign_Projects_Clients_Helpers::create_input('phone'); ?>
							</div>		
							
							<div class="albdesign_input">
								<div class="albdesign_input_header">Mobile</div>
									<?php echo Albdesign_Projects_Clients_Helpers::create_input('mobile'); ?>
							</div>
					
							<div class="albdesign_input">
								<div class="albdesign_input_header">Skype</div>
									<?php echo Albdesign_Projects_Clients_Helpers::create_input('skype'); ?>
							</div>									
							
							<div class="albdesign_input">
								<div class="albdesign_input_header">&nbsp;</div>							
								<input type="submit" class="button button-primary button-large" name="<?php echo Albdesign_Projects_Reports_Page::get_slug();?>"  value="Search Clients">
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
					<?php echo Albdesign_Projects_Clients_Helpers::get_results_for_report(); ?>
				</td>
			</tr>
		</table>
		
</div>

