<div class="wrap" style="background-color: #fff;padding: 20px;">
	<h2>Projects Tab</h2>

		<?php do_action('albwppm_projects_report_page_before_form_table'); ?>
	
		<table class="form-table albdesign_projects_reports_page">

			<tr valign="top">
				<td class="albdesign_projects_report_projects_tab_form">
				
					<?php do_action('albwppm_projects_report_page_before_form'); ?>
				
					<form method="post" >

						<div>
							
							<div class="albdesign_input">
								<div class="albdesign_input_header">Client</div>
								<?php Albdesign_Projects_Clients_Helpers::get_all_as_dropdown() ;?>
							</div>
							
							<div class="albdesign_input">
								<div class="albdesign_input_header">Priority</div>							
								<?php echo Albdesign_Projects_Project_Helpers::dropdown_priorities(); ?>
							</div>	
							
							<div class="albdesign_input">
								<div class="albdesign_input_header">Progress</div>	
								<?php echo Albdesign_Projects_Project_Helpers::dropdown_progress(); ?>								

							</div>								
							
							<div class="albdesign_input">
								<div class="albdesign_input_header">Status</div>							
								<?php echo Albdesign_Projects_Project_Helpers::dropdown_statuses(); ?>
							</div>
							
							<div class="albdesign_input">
								<div class="albdesign_input_header">Start Date</div>
									<?php echo Albdesign_Projects_Project_Helpers::dropdown_before_exactly_after('start_date'); ?>
							</div>
							
							
							<div class="albdesign_input">
								<div class="albdesign_input_header">End Date</div>
									<?php echo Albdesign_Projects_Project_Helpers::dropdown_before_exactly_after('target_end_date'); ?>
							</div>

							<div class="albdesign_input">
								<div class="albdesign_input_header">Actual end Date</div>
									<?php echo Albdesign_Projects_Project_Helpers::dropdown_before_exactly_after('actual_end_date'); ?>
							</div>						

							<div class="albdesign_input">
								<div class="albdesign_input_header">&nbsp;</div>							
								<input type="submit" class="button button-primary button-large" name="<?php echo Albdesign_Projects_Reports_Page::get_slug();?>"  value="Search Projects">
							</div>
							
							<div class="albdesign_clear"></div>
						</div>
						
					</form>
					
					<?php do_action('albwppm_projects_report_page_after_form'); ?>
					
				</td>
			</tr>

		</table>
		
		
		<?php do_action('albwppm_projects_report_page_before_report_table'); ?>
		
		<table>
			<tr>
				<td>
					<?php echo Albdesign_Projects_Project_Helpers::get_results_for_report(); ?>
				</td>
			</tr>
		</table>
		
		<?php do_action('albwppm_projects_report_page_after_report_table'); ?>

		
</div>