<?php
class Albdesign_Projects_Invoice_Helpers{

	/*
	* Returns a list of invoices associated with client ... Used on extra columns and inside client CPT
	*/
	public static function get_invoices_for_client_extra_columns($clientid,$return_or_show='return'){
	
		if($clientid && ( $clientid > 0)){
			//echo $clientid;
			
			$invoicesAssociateWithClient = 'No invoices';

			//get all projects for this client
			$get_invoices_for_clients_params =array(
				'showposts'=>-1,
				'post_type' => 'albdesign_invoice',
				'post_status' => 'publish',
				'meta_key'=>'albdesign_projects_invoices_client_field_id',
				'meta_value'=> $clientid
			);
			$query_invoices_for_client = new WP_Query();
			$results_invoices_for_client = $query_invoices_for_client->query($get_invoices_for_clients_params);

			
			//if we have at least one invoice for this client
			if(sizeof($results_invoices_for_client)>=1){
			
				$invoicesAssociateWithClient='';
			
				foreach($results_invoices_for_client as $single_invoice_for_client){
					
					//Invoice edit link
					$invoicesAssociateWithClient.= '<a href="post.php?post='.$single_invoice_for_client->ID .'&action=edit">'. $single_invoice_for_client->post_title .'</a>';
					
					//Invoice Status
					if(get_post_meta($single_invoice_for_client->ID , '_albdesign_invoice_notes', true)){
					
						$invoiceStatus = get_post_meta($single_invoice_for_client->ID , '_albdesign_invoice_notes', true);

						$invoiceStatusToDisplay = (isset($invoiceStatus['status'])) ? ucfirst($invoiceStatus['status']) : 'Not set';
					}
					
					$invoicesAssociateWithClient.= ' ' . $invoiceStatusToDisplay ;
					$invoicesAssociateWithClient.= '<br>';
				} //end foreach
			
				
				return $invoicesAssociateWithClient;
			
			} else {
				
				//existing client but no invoices associated with him
				return apply_filters('albwppm_no_invoices_for_this_client',$invoicesAssociateWithClient ,'no_invoices_found');
			}

		}
		
		return false;
	}
	
	
	/*
	* Returns invoice total value
	*/
	public static function calculate_total($invoice_id , $actualTotal ,$returnNewValueOrDiscount='newvalue'){

		$invoice_id = (int) $invoice_id;
		
		$invoice_meta = get_post_meta($invoice_id,'_invoice_discount_and_vat',true);

		//if we have i.e subtotal set ... it means we are good to go
		if(!isset($invoice_meta['invoice_subtotal'])){
			return false;
		}

		$discountValueEntered = $invoice_meta['discountValue'];
		$discountTypeEntered  = $invoice_meta['discountType'];
		
		if( $discountValueEntered &&  $discountTypeEntered != 'none' ){
			
			if($discountTypeEntered=='percent'){
				//check so discounted value isnt lower than 0
				
				$valueAfterDiscount = $actualTotal - ( $actualTotal * $discountValueEntered/100 );

				if( $valueAfterDiscount > 0 ){
					
					if($returnNewValueOrDiscount=='newvalue'){
						$value_to_return =  $valueAfterDiscount;
					}else{
						$value_to_return =  $discountValueEntered ;
					}
				}

			}
			
			if($discountTypeEntered=='amount'){

				//check so discounted value isnt lower than 0
				if( $actualTotal - $discountValueEntered > 0 ){

					if($returnNewValueOrDiscount=='newvalue'){
						$toreturn  = $actualTotal - $discountValueEntered;

						$value_to_return =   $toreturn;
					}else{
						$value_to_return =  $discountValueEntered  ;
					}
				}
				
			}
			
			if(isset($invoice_meta['vat'])){
				if($invoice_meta['vat']>0){
					return number_format ($value_to_return  +   ($value_to_return * $invoice_meta['vat']/100),2) ;
				}else{
					return number_format($value_to_return,2);
					
				}
			}
			
			//return $value_to_return 
			
		}else{
			if(isset($invoice_meta['vat'])){
				if($invoice_meta['vat']>0){
					return number_format ($actualTotal  +   ($actualTotal * $invoice_meta['vat']/100),2) ;
				}else{
					return number_format($actualTotal,2);
				}
			}
		}
	}
	
	
	/*
	* Calculate new value after discount,vat 
	*/
	public static function calculateDiscount($invoice_id , $actualTotal ,$returnNewValueOrDiscount='newvalue'){

		$invoice_id = (int) $invoice_id;
		
		$invoice_meta = get_post_meta($invoice_id,'_invoice_discount_and_vat',true);
	
		//if we have i.e subtotal set ... it means we are good to go
		if(!isset($invoice_meta['invoice_subtotal'])){
			return false;
		}
	
		$discountValueEntered = $invoice_meta['discountValue'];
		$discountTypeEntered  = $invoice_meta['discountType'];
		
		if( $discountValueEntered &&  $discountTypeEntered != 'none' ){
			
			if($discountTypeEntered=='percent'){
				//check so discounted value isnt lower than 0
				
				$valueAfterDiscount = $actualTotal - ( $actualTotal * $discountValueEntered/100 );

				if( $valueAfterDiscount > 0 ){
					
					if($returnNewValueOrDiscount=='newvalue'){
						return $valueAfterDiscount;
					}else{
						return $discountValueEntered ;
					}
				}
				
				return false;		
			}
			
			if($discountTypeEntered=='amount'){

				//check so discounted value isnt lower than 0
				if( $actualTotal - $discountValueEntered > 0 ){

					if($returnNewValueOrDiscount=='newvalue'){
						 $toreturn  = $actualTotal - $discountValueEntered;

						return  $toreturn;
					}else{
						return $discountValueEntered + ' ' ;
					}
				}
				return false;
			}
			
		}else{
			return false;
		}
	}	

	
	
	/*
	* Return number of all invoices by default , OPTIONS is array of additional WP_QUERY params
	*/
	
	static function get_all($options=array()){
	
		$query = array('posts_per_page' => -1, 'post_type' => 'albdesign_invoice');
		
		if (isset($options['args']) ) {
			$query = array_merge($query,(array)$options['args']);
		}

	
		//get all invoices
		$query_all = new WP_Query();
		$results_all = $query_all->query($query);
		
		return sizeof($results_all) ;
	}
	
	
	/*
	* Return number of paid invoices
	*/
	static function get_paid(){
	
		$paidInvoicesCount = 0;
	
		
		$get_invoices_params =array(
			'showposts'=>-1,
			'post_type' => 'albdesign_invoice',
			'post_status' => 'publish',
		);
		$query_invoices = new WP_Query();
		$results_invoices = $query_invoices->query($get_invoices_params);

		
		//if we have at least one invoice
		if(sizeof($results_invoices)>=1){
		
			foreach($results_invoices as $single_invoice){
				
				//Invoice Status
				if(get_post_meta($single_invoice->ID , '_albdesign_invoice_notes', true)){
					$invoiceStatus = get_post_meta($single_invoice->ID , '_albdesign_invoice_notes', true);
					//print_r($invoiceStatus);
					//die();
					if(isset($invoiceStatus)){
						if($invoiceStatus['status']=='paid'){
							$paidInvoicesCount++;
						}
					}
				}

			} //end foreach

		} //end sizeof
		
		return $paidInvoicesCount;
		
	}		
	
	/*
	* Returns % of paid invoices 
	*/
	static function get_paid_invoices_percent(){
		
		if(self::get_all() <= 0 ){
			return 0;
		}
		
		return ( self::get_paid() / self::get_all() );
	}
	
	
	/*
	* Get the price amount of paid invoices
	*/
	
	static function get_invoices_amount_by_status($status){
		
		$total_amount = 0;
	
		//get all paid invoices
		$get_invoices_params =array(
			'showposts'=>-1,
			'post_type' => 'albdesign_invoice',
			'post_status' => 'publish',
		);
		$query_invoices = new WP_Query();
		$results_invoices = $query_invoices->query($get_invoices_params);
		
		//if we have at least one invoice
		if(sizeof($results_invoices)>=1){
		
			foreach($results_invoices as $single_invoice){
				
				if(self::get_invoice_notes_value_by_invoice_id($single_invoice->ID,'status')==$status){
				
					$invoice_subtotal = self::get_invoice_discount_and_value_by_invoice_id($single_invoice->ID,'invoice_subtotal');
					if($invoice_subtotal>0){
						$total_amount+=  $invoice_subtotal;
					}
				
				}

			} 

		} //end sizeof
		
		return $total_amount;		
		
	}

	
	/*
	* Get invoice meta value by invoice ID
	*/
	static function get_invoice_meta_value_by_invoice_id($id,$meta=''){
		
		if($id=='' || $meta == ''){
			return 'Not Set';
		}

		$meta_value = get_post_meta($id,$meta,true);
		
		if($meta_value=='not_set'){
			return 'Not Set';	
		}
		
		return ($meta_value) ? $meta_value : 'Not Set';
		
	}
	
	/*
	* Get invoice notes
	*/
	static function get_invoice_notes_value_by_invoice_id($id,$meta=''){
		
		if($id=='' || $meta == ''){
			return 'Not Set';
		}
		
		$value_to_return = 'Not Set';

		$meta_value_array = get_post_meta($id,'_albdesign_invoice_notes',true);
		
		if(isset($meta_value_array)){
			if(isset($meta_value_array[$meta])){
				if($meta_value_array[$meta]=='not_set'){
					return $value_to_return;
				}
				$value_to_return = $meta_value_array[$meta];
			}
		}
		return $value_to_return;
		
	}	
	
	/*
	* Get invoice price/vat/amount
	*/
	static function get_invoice_discount_and_value_by_invoice_id($id,$meta=''){
		
		if($id=='' || $meta == ''){
			return 'Not Set';
		}
		
		$value_to_return = 'Not Set';

		$meta_value_array = get_post_meta($id,'_invoice_discount_and_vat',true);
		
		if(isset($meta_value_array)){
			if(isset($meta_value_array[$meta])){
				if($meta_value_array[$meta]=='not_set'){
					return $value_to_return;
				}
				$value_to_return = $meta_value_array[$meta];
			}
		}
		return $value_to_return;
		
	}		
	
	
	/*
	* Get invoices by status ... completed , ongoing , hold
	*/
	static function get_by_status($status){
	
		switch ($status){
		
			case 'unpaid':
				$which_status = 'unpaid';
				break;
				
			case 'paid':
				$which_status = 'paid';
				break;
				
			case 'overdue':
				$which_status = 'overdue';
				break;
				
			case 'cancelled':
				$which_status = 'cancelled';
				break;					

			default :
				$which_status = 'paid';				
				
		}
	

	
		$invoicesFound = 0;
	
		//get all invoices
		$get_invoices_params =array(
			'showposts'=>-1,
			'post_type' => 'albdesign_invoice',
			'post_status' => 'publish',
		);
		$query_invoices = new WP_Query();
		$results_invoices = $query_invoices->query($get_invoices_params);

		
		//if we have at least one invoice
		if(sizeof($results_invoices)>=1){
		
			foreach($results_invoices as $single_invoice){
				
				//Invoice Status
				if(get_post_meta($single_invoice->ID , '_albdesign_invoice_notes', true)){
					$invoiceStatus = get_post_meta($single_invoice->ID , '_albdesign_invoice_notes', true);
					if(isset($invoiceStatus)){
						if($invoiceStatus['status']==$which_status){
							$invoicesFound++;
						}
					}
				}

			} //end foreach

		} //end sizeof
		
		return $invoicesFound;	

	}	
	
	
	/*
	* Lists possible invoice payment status as dropdown
	*/
	
	static function dropdown_paid_status(){
	
		$selected ='';
	
		//check if we have a status selected 
		if(isset($_POST['albdesign_invoice_status_for_report_page']) && $_POST['albdesign_invoice_status_for_report_page']){
			$selected = $_POST['albdesign_invoice_status_for_report_page'];
		}
		
		?><select  name="albdesign_invoice_status_for_report_page">
			<option value="">All</option>
			<option value="unpaid" <?php echo ($selected == 'unpaid') ? ' selected = "selected" ' : ''; ?>>Unpaid</option>
			<option value="paid" <?php echo ($selected == 'paid') ? ' selected = "selected" ' : ''; ?>>Paid</option>
			<option value="overdue" <?php echo ($selected == 'overdue') ? ' selected = "selected" ' : ''; ?>>Overdue</option>
			<option value="cancelled" <?php echo ($selected == 'cancelled') ? ' selected = "selected" ' : ''; ?>>Cancelled</option>
		</select>
		
		<?php
	}		
	
	/*
	* Create INPUT FIELD
	*/
	static function create_input($input_name,$placeholder=''){
		
		$selected_value ='';
		
		//check if we have a value set for the field
		if(isset($_POST[$input_name]) && $_POST[$input_name]!=''){
			$selected_value = $_POST[$input_name];
		}	

		echo '<input type="text" name="'.$input_name.'" placeholder="'.$placeholder.'" value="'.$selected_value.'">';
		
	}	
	
	
	/*
	*  REPORT SECTION BEGINS
	*/
	
	static function get_results_for_report(){
	
	
		if(sizeof($_POST)<1 ){
			return;
		}	
		
		//default args to return all invoices
		$default_args = array(
			'showposts'=>-1,
			'post_type' => 'albdesign_invoice',
			'post_status' => 'publish',		
			
			'meta_query' => array(
				'relation' => 'AND',
			)
			
		);			


		//check if client is set
		if(isset($_POST['albdesign_projects_reports_projectTab_clients_list']) && $_POST['albdesign_projects_reports_projectTab_clients_list']>0){

			$prep_client = array(
							'key' => 'albdesign_projects_invoices_client_field_id' , 
							'value' => $_POST['albdesign_projects_reports_projectTab_clients_list'] , 
							'compare' => '='
							); 

			array_push( $default_args['meta_query'], $prep_client);
			
		}		
		
		
		//check if project is set
		if(isset($_POST['albdesign_projects_invoices_project_field_id']) && $_POST['albdesign_projects_invoices_project_field_id']>0){

			$prep_project = array(
							'key' => 'albdesign_projects_invoices_project_field_id' , 
							'value' => $_POST['albdesign_projects_invoices_project_field_id'] , 
							'compare' => '='
							); 

			array_push( $default_args['meta_query'], $prep_project);
			
		}		
		
		//check if status
		if(isset($_POST['albdesign_invoice_status_for_report_page']) && $_POST['albdesign_invoice_status_for_report_page']!=''){

			$prep_status = array(
							'key' => '_albdesign_invoice_notes' , 
							'value' => serialize(strval($_POST['albdesign_invoice_status_for_report_page'])) , 
							'compare' => 'LIKE'
							); 

			array_push( $default_args['meta_query'], $prep_status);
			
		}			
		
		
		//get all invoices
		$query_all_invoices = new WP_Query();
		$results_all_invoices = $query_all_invoices->query($default_args);			
		$invoices_found = (sizeof($results_all_invoices)>1)  ? ' invoices ' : ' invoice ' ; 	 
	 
		
	 
		echo '<div style="padding: 20px;padding-bottom:0px;"><strong>Found ' . sizeof($results_all_invoices) .' '. $invoices_found .'</strong></div>';
		
		
		//if we have at least one invoice ... show the table
		if(sizeof($results_all_invoices)>=1){
			
			?>
			<div style="color:red;padding: 20px;">
			
			<table class="wp-list-table widefat fixed posts">
				<thead>
					<tr>
	
						<th scope="col"  class="check-column manage-column column-title sortable desc " style="padding-top:0px;width: 3em;">
							<a ><span><strong>Title</strong></span></a>
						</th>		
						<th scope="col"  class="check-column manage-column column-title sortable desc " style="padding-top:0px;width: 3em;">
							<a ><span><strong>Status</strong></span></a>
						</th>	
						<th scope="col"  class="check-column manage-column column-title sortable desc " style="padding-top:0px;width: 3em;">
							<a ><span><strong>Amount</strong></span></a>
						</th>							
						<th scope="col"  class="check-column manage-column column-title sortable desc " style="padding-top:0px;width: 3em;">
							<a ><span><strong>To be paid by</strong></span></a>
						</th>	
						<th scope="col"  class="check-column manage-column column-title sortable desc " style="padding-top:0px;width: 3em;">
							<a ><span><strong>Paid on</strong></span></a>
						</th>							
						<th scope="col"  class="check-column manage-column column-title sortable desc " style="padding-top:0px;width: 3em;">
							<a ><span><strong>Client</strong></span></a>
						</th>		
						<th scope="col"  class="manage-column column-title sortable desc" style="width: 3em;">
							<a ><span><strong>Project</strong></span></a>
						</th>									
						
					</tr>
				</thead>
				<tfoot>
					<tr>
	
						<th scope="col"  class="manage-column column-title sortable desc" style="width: 3em;">
							<a ><span><strong>Title</strong></span></a>
						</th>		
						<th scope="col"  class="check-column manage-column column-title sortable desc " style="padding-top:0px;width: 3em;">
							<a ><span><strong>Status</strong></span></a>
						</th>	
						<th scope="col"  class="check-column manage-column column-title sortable desc " style="padding-top:0px;width: 3em;">
							<a ><span><strong>Amount</strong></span></a>
						</th>						
						<th scope="col"  class="check-column manage-column column-title sortable desc " style="padding-top:0px;width: 3em;">
							<a ><span><strong>To be paid by</strong></span></a>
						</th>		
						<th scope="col"  class="check-column manage-column column-title sortable desc " style="padding-top:0px;width: 3em;">
							<a ><span><strong>Paid on</strong></span></a>
						</th>							
						<th scope="col"  class="manage-column column-title sortable desc" style="width: 3em;">
							<a ><span><strong>Client</strong></span></a>
						</th>	
						<th scope="col"  class="manage-column column-title sortable desc" style="width: 3em;">
							<a ><span><strong>Project</strong></span></a>
						</th>							
			

					</tr>
				</tfoot>	
				<tbody id="the-list">	
	
			<?php
			
			$row_counter = 0;
			$color = '';
			
			foreach ($results_all_invoices as $single_invoice){ 
				
				if($row_counter%2==0){
					$color='alternate';
				}else{
					$color='';
				}
			
				?>
				
				<tr class=" hentry  iedit <?php echo $color ;?> widefat">
				
					
					<td class="check-column" style="padding:9px 0px 8px 10px;">
						<a href="<?php echo get_edit_post_link($single_invoice->ID);?>"><strong><?php echo $single_invoice->post_title ;?></strong></a>
					</td>	
					
					<td class="check-column" style="padding:9px 0px 8px 10px;">
						<?php echo ucwords(self::get_invoice_notes_value_by_invoice_id($single_invoice->ID,'status'));?>
					</td>
					<td class="check-column" style="padding:9px 0px 8px 10px;">
						<?php 
						$invoice_subtotal = self::get_invoice_discount_and_value_by_invoice_id($single_invoice->ID,'invoice_subtotal');
						if($invoice_subtotal>0){
							//($invoice_id , $actualTotal ,$returnNewValueOrDiscount='newvalue'){
							echo self::calculate_total($single_invoice->ID,$invoice_subtotal);
						}
						?>
					</td>					
					<td class="check-column" style="padding:9px 0px 8px 10px;">
						<?php echo self::get_invoice_notes_value_by_invoice_id($single_invoice->ID,'toBePaidOn');?>
					</td>	
					<td class="check-column" style="padding:9px 0px 8px 10px;">
						<?php echo self::get_invoice_notes_value_by_invoice_id($single_invoice->ID,'paidOn');?>
					</td>					
					<td class="check-column" style="padding:9px 0px 8px 10px;">
						<?php 
							$client_id_for_invoice = self::get_invoice_meta_value_by_invoice_id($single_invoice->ID,'albdesign_projects_invoices_client_field_id');
							if($client_id_for_invoice>0){
							echo '<a href="'.get_edit_post_link($client_id_for_invoice).'">'.get_the_title($client_id_for_invoice).'</a>';
						}?>
					</td>	

					<td class="check-column" style="padding:9px 0px 8px 10px;">
						<?php 
							$project_id_for_invoice = self::get_invoice_meta_value_by_invoice_id($single_invoice->ID,'albdesign_projects_invoices_project_field_id');
							if($project_id_for_invoice>0){
							echo '<a href="'.get_edit_post_link($project_id_for_invoice).'">'.get_the_title($project_id_for_invoice).'</a>';
						}?>
					</td>					
					
				</tr>
				
				<?php
				
				$row_counter++ ; 
				
			} //end foreach
			
			?>
			
					</tbody>
				</table>	
			</div>
			
			<?php
		}				
	 
	 
	}
	
	/*
	*  REPORT SECTION ENDS
	*/
	
	

} //end class