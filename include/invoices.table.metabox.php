<?php
/*
* Class to create the metabox for invoice tables
*/


class Albdesign_Invoice_Table_Metabox {

	private $plugin_path;
	private $plugin_url;
	private static $instance = null;
	
	private $mainPluginPath ='' ;

	
	/**
	 * Creates or returns an instance of this class.
	 */
	public static function get_instance() {
		// If an instance hasn't been created and set to $instance create an instance and set it to $instance.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}		
	
	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public function __construct() {
	
		$this->plugin_path = plugin_dir_path( __FILE__ );
		$this->plugin_url  = plugin_dir_url( __FILE__ );
		
		$this->mainPluginPath = dirname(dirname(__FILE__)) ;
		
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' )  );
		add_action( 'save_post', array( $this, 'save' ) );
		
		
		//add ajax function to update fields of table and dropdown 
		add_action( 'wp_ajax_albdesign_project_invoice_cpt_backend_ajax', array( $this, 'albdesign_project_invoice_cpt_backend_ajax'));
		
		//get items for existing invoice
		add_action( 'wp_ajax_albdesign_project_items_for_invoice_ajax', array( $this, 'albdesign_project_items_for_invoice_ajax'));
		
		//get items for existing invoice that have "is_project=yes"
		add_action( 'wp_ajax_get_projects_items_on_invoice_ajax', array( $this, 'get_projects_items_on_invoice_ajax'));		
		
		//Get all projects if a client is associated with invoice.
		add_action( 'wp_ajax_get_and_check_projects_checkbox_list_ajax', array( $this, 'get_and_check_projects_checkbox_list_ajax'));		
		
		//save invoice total,vat,discount
		add_action( 'wp_ajax_albdesign_project_invoice_save_vat_discount_ajax', array( $this, 'albdesign_project_invoice_save_vat_discount_ajax'));
		
		//add admin css,js
		add_action('admin_enqueue_scripts', array($this,'invoice_admin_css'));
		
		//add ajax for PDF preview
		add_action( 'wp_ajax_albdesign_project_invoice_preview_pdf_ajax', array( $this, 'albdesign_project_invoice_preview_pdf_ajax'));
		
		//remove default PUBLISH box
		//add_action( 'admin_menu', array($this,'remove_publish_box') );
		
		//Download PDF
		add_action( 'init',array($this,'albdesign_project_invoice_pdf_download'));
		
		
	}
	
	
	/*
	* Localize the JS scripts 
	*/
	
	function localize_scripts(){
		
		$translation_array = array(
			'table_invoice_add_record_title' => apply_filters('albwppm_table_invoice_add_record_title','Add new item')
		);

		wp_localize_script( 'albdesign-project-invoice-page', 'albwppm', $translation_array );
	}
	
	
	public function remove_publish_box(){
		
		//remove_meta_box( 'submitdiv', 'albdesign_invoice', 'side' );
		remove_meta_box( 'submitdiv', 'albdesign_invoice', 'normal' ); // Publish meta box
		remove_meta_box( 'commentsdiv', 'albdesign_invoice', 'normal' ); // Comments meta box
		remove_meta_box( 'revisionsdiv', 'albdesign_invoice', 'normal' ); // Revisions meta box
		remove_meta_box( 'authordiv', 'albdesign_invoice', 'normal' ); // Author meta box
		remove_meta_box( 'slugdiv', 'albdesign_invoice', 'normal' );	// Slug meta box
		remove_meta_box( 'tagsdiv-post_tag', 'albdesign_invoice', 'side' ); // Post tags meta box
		remove_meta_box( 'categorydiv', 'albdesign_invoice', 'side' ); // Category meta box
		remove_meta_box( 'postexcerpt', 'albdesign_invoice', 'normal' ); // Excerpt meta box
		remove_meta_box( 'formatdiv', 'albdesign_invoice', 'normal' ); // Post format meta box
		remove_meta_box( 'trackbacksdiv', 'albdesign_invoice', 'normal' ); // Trackbacks meta box
		remove_meta_box( 'postcustom', 'albdesign_invoice', 'normal' ); // Custom fields meta box
		remove_meta_box( 'commentstatusdiv', 'albdesign_invoice', 'normal' ); // Comment status meta box
		remove_meta_box( 'postimagediv', 'albdesign_invoice', 'side' ); // Featured image meta box
		remove_meta_box( 'pageparentdiv', 'albdesign_invoice', 'side' ); // Page attributes meta box
		
	}

	public function get_plugin_url() {
		return $this->plugin_url;
	}
	
	/*
	* Admin Scripts,Styles
	*/
	public function invoice_admin_css(){
		
		global $pagenow, $typenow;
		
		//which page are we
		$screen = get_current_screen();

		if( $typenow=='albdesign_invoice' || $typenow =='albdesign_project' || $typenow == 'albdesign_task' || $typenow == 'albdesign_client' ){
			wp_enqueue_script( 'at-meta-box', $this->get_plugin_url() .'meta-box-class/js/meta-box.js', array( 'jquery' ), null, true );
			wp_enqueue_style('at-multiselect-select2-css',  $this->get_plugin_url() . 'meta-box-class/js/select2/select2.css', array(), null);
			wp_enqueue_script('at-multiselect-select2-js', $this->get_plugin_url() . 'meta-box-class/js/select2/select2.js', array('jquery'), false, true);
		}

		
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		
		if($screen->post_type =='albdesign_invoice'){
			
			wp_enqueue_script('jquery-ui-datepicker');
		
			wp_enqueue_script( 'albdesign-project-invoice-page', plugin_dir_url(dirname(__FILE__)) .'assets/admin/js/invoices.admin.min.js', array( 'jquery' ), null, false );
			
			//jTable
			wp_enqueue_script( 'albdesign-project-invoice-jtable-jquery', plugin_dir_url(dirname(__FILE__)) .'assets/admin/jtable/jquery.jtable.js', array( 'jquery' ), null, true );	
			wp_enqueue_style('albdesign-project-invoice-jtable-css',  plugin_dir_url(dirname(__FILE__)) . 'assets/admin/jtable/jquery-ui.css', array(), null);
			wp_enqueue_style('albdesign-project-invoice-jtable-css3',   plugin_dir_url(dirname(__FILE__)) . 'assets/admin/jtable/themes/jqueryui/jtable_jqueryui.css', array(), null);
			wp_enqueue_style('albdesign-project-invoice-jtable-css2', plugin_dir_url(dirname(__FILE__)) .   'assets/admin/jtable/themes/metro/blue/jtable.css', array(), null);
			
			$this->localize_scripts();
		
		}

	}	
	
	
	public function albdesign_project_invoice_cpt_backend_ajax(){
		
		//if nothing set 
		if(!isset($_POST['whathappend'])){
			 die('NO whathappend action received');
		}
		
		//client dropdown changed ... find projects related to client
		if($_POST['whathappend']=='clientDropdownChanged'){
			

			$clientID = (int) $_POST['clientID'];

			//$projectsArray['found_any_project'] = 'no';
			
			//get all projects for this client
			$get_projects_for_clients_params =array(
				'showposts'=>-1,
				'post_type' => 'albdesign_project',
				'post_status' => 'publish',
				'meta_key'=>'albdesign_project_client_field_id',
				'meta_value'=> $clientID
			);
			
			$query_projects_for_client = new WP_Query();
			
			$results_projects_for_client = $query_projects_for_client->query($get_projects_for_clients_params);
			
			//if we have at least one project for this client
			if(sizeof($results_projects_for_client)>=1){
			
				//$projectsArray['found_any_project'] = 'yes';
			
				foreach($results_projects_for_client as $single_project_for_client){
					
					$project_price = get_post_meta($single_project_for_client->ID,'albdesign_project_estimate_field_id',true);
					
					$projectsArray['project_id']	= $single_project_for_client->ID;
					$projectsArray['project_title']	= $single_project_for_client->post_title;
					$projectsArray['project_price']	= $project_price;
					
					$projectsArrayToReturn[] = $projectsArray;
				}
				
				//return project id , project title 
				die(json_encode($projectsArrayToReturn));
			
			} //end sizeof			
			
			die();
		}
		
		
		//project dropdown changed ... get tasks for the selected project
		if($_POST['whathappend']=='projectDropdownChanged'){
		
			$projectID = (int) $_POST['projectID'];

			
			//get all tasks for this project
			$get_tasks_for_projects_params =array(
				'showposts'=>-1,
				'post_type' => 'albdesign_task',
				'post_status' => 'publish',
				'meta_key'=>'albdesign_task_for_project_field',
				'meta_value'=> $projectID
			);
			
			$query_tasks_of_project = new WP_Query();
			
			$results_tasks_for_project = $query_tasks_of_project->query($get_tasks_for_projects_params);
			
			//if we have at least one tasks for this project
			if(sizeof($results_tasks_for_project)>=1){
			
				foreach($results_tasks_for_project as $single_task_for_project){
				
					$task_status = get_post_meta($single_task_for_project->ID,'albdesign_task_status_task_field',true);
					
					$tasksArray['task_id']= $single_task_for_project->ID;
					$tasksArray['task_title']= $single_task_for_project->post_title;
					$tasksArray['task_price']= $single_task_for_project->ID;
					
					$tasksArray['task_status'] = ($task_status) ? albdesign_get_human_task_status_by_meta_value_as_bullet($task_status) : 'Not Set';
					
					$tasksArrayToReturn[] = $tasksArray;
				}
				
				//return task id , task title 
				die(json_encode($tasksArrayToReturn));
			
			} //end sizeof			
			
			die();
		}

		die();
	}
	
	
	/*
	*  Add,update,delete items on invoice
	*/
	function albdesign_project_items_for_invoice_ajax(){

	
		if(!isset($_POST['invoiceAction']) || !isset($_POST['invoiceID'])){
			die('No invoiceAction action received');
		}
		
		$invoiceID = (int) $_POST['invoiceID'];
	
		//Get all items for existing INVOICE
		if($_POST['invoiceAction']=='getItemsForInvoice'){

		
			//print_r($this->get_items_of_invoice($invoiceID));
		
			$res = array('Result' => 'OK', 'Records' => $this->get_items_of_invoice($invoiceID,true));

			die (json_encode($res));
		
		}
		
		//Add new item to INVOICE
		if($_POST['invoiceAction']=='addItemToInvoice'){

			//itemData is in URL form ... convert to array
			parse_str($_POST['itemData'], $itemArray);
			
			//set a random ID for this item 
			if(!isset($itemArray['invoiceRowId'])){
				$itemArray['invoiceRowId'] = time();
			}
			

			//calculate total for this item
			$itemArray['ItemTotalCost'] = $itemArray['ItemUnitCost'] * $itemArray['ItemQuantity'];
			
			$rezi = $this->add_items_to_invoice($invoiceID,$itemArray);
			
			$actual_items = $this->get_items_of_invoice($invoiceID,true);
		
			//send back last item inserted via the END($array) 
			$res = array('Result' => 'OK' ,  'Record' => end($actual_items));
			
			die (json_encode($res));
		
		}	

		//update existing item of INVOICE
		if($_POST['invoiceAction']=='updateExistingItemOnInvoice'){
		
			$invoiceID 	= $_POST['invoiceID'];
			
			//itemData is in URL form ... convert to array
			parse_str($_POST['itemData'], $itemArray);
			
			$actual_items = $this->get_items_of_invoice($invoiceID,true);
			
			foreach($actual_items as $single_item_k => $single_item_v){
			
				if ($single_item_v['invoiceRowId'] == $itemArray['invoiceRowId']){

					$actual_items[$single_item_k]['ItemName']		= $itemArray['ItemName'];
					$actual_items[$single_item_k]['ItemUnitCost']	= $itemArray['ItemUnitCost'];
					$actual_items[$single_item_k]['ItemQuantity']	= $itemArray['ItemQuantity'];
					$actual_items[$single_item_k]['ItemTotalCost']  = $itemArray['ItemUnitCost'] * $itemArray['ItemQuantity'];
					
				}

			}

			//delete all items
			delete_post_meta($invoiceID, '_items_on_invoice');

			//add the items
			update_post_meta($invoiceID, '_items_on_invoice',$actual_items);
			
			$res = array('Result' => 'OK');

			die (json_encode($res));
		
		} //end update existing item	
		
		
		//remove existing item from INVOICE
		if($_POST['invoiceAction']=='removeExistingItemFromInvoice'){
		
			$invoiceID 	= $_POST['invoiceID'];
			$itemID 	= $_POST['invoiceItemID'];
		
			$actual_items = $this->get_items_of_invoice($invoiceID,true);
			
			foreach($actual_items as $single_item_k => $single_item_v){
			
				if ($single_item_v['invoiceRowId'] == $itemID){

					unset($actual_items[$single_item_k]);
					
				}

			}
			
			$new_items_array = array_values($actual_items);

			//delete all items
			delete_post_meta($invoiceID, '_items_on_invoice');

			//add the items
			update_post_meta($invoiceID, '_items_on_invoice',$new_items_array);
		
			$res = array('Result' => 'OK');

			die (json_encode($res));
		
		} //end remove existing item		
		
		die();
	}
	
	
	/*
	* Returns all items associated to an invoice
	*/
	function get_items_of_invoice($invoiceID,$as_single=false){
		
		if (!$invoiceID){
			return false;
		}
		
		$items_meta = get_post_meta($invoiceID,'_items_on_invoice',$as_single);
		
		if(!$items_meta){
		
			return false;
			
		} else {
			
			return $items_meta;
			
		}
		
	} 
	
	
	/*
	* Add items to invoice
	*/
	function add_items_to_invoice($invoiceID,$newItemData){
		
		//get actual items on invoice
		$actual_items = $this->get_items_of_invoice($invoiceID,true);
		
		$actual_items[]= $newItemData;
		
		if(update_post_meta($invoiceID,'_items_on_invoice',$actual_items)){
		
			return $this->get_items_of_invoice($invoiceID,true);
			
		}
		
			return false;

	}
	
	
	/*
	* Returns all "project" items in a given invoice
	*/
	function get_projects_items_on_invoice_ajax($invoice_id){
		
		$list_of_projects_in_invoice = array();
		$found_projects = 0;
		$response = array();
		$return_project_array = array();
		
		$find_projects_in_invoice_items = get_post_meta($invoice_id,'_items_on_invoice',true);

		if($find_projects_in_invoice_items){
			foreach ($find_projects_in_invoice_items as $find_projects_in_invoice_item_single){
				foreach($find_projects_in_invoice_item_single as $find_projects_in_invoice_item_single_data_key => $find_projects_in_invoice_item_single_data_value){
					
					if(isset($find_projects_in_invoice_item_single_data_key)){
						if($find_projects_in_invoice_item_single_data_key=='is_project'){
							$list_of_projects_in_invoice[]= $find_projects_in_invoice_item_single['invoiceRowId'];
							//$list_of_projects_in_invoice['project_title']= $find_projects_in_invoice_item_single['ItemName'];
							//$list_of_projects_in_invoice['project_price']= $find_projects_in_invoice_item_single['ItemTotalCost'];
							$return_project_array[] = $list_of_projects_in_invoice;
						}
					}
					
				}
				
			}
		}

		//if we have any "project" on invoice
		if(count($list_of_projects_in_invoice) > 0){
			//return array of project id ... make values unique ... reset keys
			return array_values(array_unique($list_of_projects_in_invoice));
		}

		return false;

	}	
	
	

	/*
	* Get all projects for client on invoice if client is associated with invoice.
	* Returns all projects as checkbox for "related projects" on "invoice" edit page
	*/
	function get_and_check_projects_checkbox_list_ajax(){

			if(!isset( $_POST['clientID']) || !isset( $_POST['invoiceID'])){
				die();
			}
			
			$clientID = (int) $_POST['clientID'];
			$invoiceID = (int) $_POST['invoiceID'];
			
			$projects_on_invoice = array();
			
			if($invoiceID <= 0 || $clientID <= 0 ){
				die();
			}
			
			//get all projects for this client
				$get_projects_for_clients_params =array(
					'showposts'=>-1,
					'post_type' => 'albdesign_project',
					'post_status' => 'publish',
					'meta_key'=>'albdesign_project_client_field_id',
					'meta_value'=> $clientID
				);
				
				
				
				$query_projects_for_client = new WP_Query();
				
				$results_projects_for_client = $query_projects_for_client->query($get_projects_for_clients_params);
				
				if(sizeof($results_projects_for_client)>=1){
				
					//we found projects for client.Check if project is already on invoice
					if($this->get_projects_items_on_invoice_ajax($invoiceID)){
						$projects_on_invoice = $this->get_projects_items_on_invoice_ajax($invoiceID);
					}				
				
					foreach($results_projects_for_client as $single_project_for_client){
						
						$project_price = get_post_meta($single_project_for_client->ID,'albdesign_project_estimate_field_id',true);
						
						$projectsArray['project_id']	= $single_project_for_client->ID;
						$projectsArray['project_title']	= $single_project_for_client->post_title;
						$projectsArray['project_price']	= $project_price;
						
						//if project is also on invoice
						if(in_array($projectsArray['project_id'],$projects_on_invoice)){
							$projectsArray['is_on_invoice_already']	= 'yes';
						}else{
							$projectsArray['is_on_invoice_already']	= 'no';
						}
						
						$projectsArrayToReturn[] = $projectsArray;
					}

					//($projectsArrayToReturn);
					//return project id , project title , project_price
					die(json_encode($projectsArrayToReturn));	
					
				} //end sizeof

		
			
			die();
		
	} 
	
	
	/*
	* Save,update,delete invoice`s VAT,DISCOUNT
	*/
	
	function albdesign_project_invoice_save_vat_discount_ajax(){
	
		$response = array('invoiceUpdated' => 'no');
		
		$invoiceID = $_POST['invoiceID'];
	
		if($invoiceID > 0 ){
			
			$invoice_array['vat'] 			= $_POST['vatValue'];
			$invoice_array['discountType'] 	= $_POST['discountType'];
			$invoice_array['discountValue'] = $_POST['discountValue'];
		
			//get invoice items
			$invoice_items = $this->get_items_of_invoice($invoiceID,true);
			
			if($invoice_items){

				//calculate subtotal ( without vat,discount )
				$subtotal = 0 ; 
				foreach($invoice_items as $single_invoice_item){
					$subtotal+=$single_invoice_item['ItemTotalCost'];
				}
				
				$invoice_array['invoice_subtotal'] = $subtotal;
				
				
				update_post_meta($invoiceID,'_invoice_discount_and_vat',$invoice_array);
				
				$response = array('invoiceUpdated' => 'yes');
			
			}else{
				
				$response = array('invoiceUpdated' => 'NoItemsOnInvoice');
				
			}

		}
		
		die(json_encode($response));

	}
	
	/*
	* Get saved Discount,Vat or return default 0
	*/
	static function get_vat_or_discount($what){
		
		global $post;
		
		$saved_post_meta = get_post_meta($post->ID,'_invoice_discount_and_vat',true);

		if($saved_post_meta){
			if(isset($saved_post_meta[$what])){
				return $saved_post_meta[$what];
			}
		}
		
		//get default VAT from SETTINGS
		if($what=='vat'){
			if(Albdesign_Projects_Settings_Option_Page::get('invoice_default_vat') > 0){
				return Albdesign_Projects_Settings_Option_Page::get('invoice_default_vat');
			}
		}
		
		//return failsafe value
		return 0;
	}
	
	/**
	 * Adds the meta box container.
	 */
	public function add_meta_box( $post_type ) {
            $post_types = array('albdesign_invoice'); 
            if ( in_array( $post_type, $post_types )) {
				add_meta_box(
					'some_meta_box_name'
					,__( 'Prepare invoice', 'albdesign_project' )
					,array( $this, 'render_meta_box_content' )
					,$post_type
					,'advanced'
					,'high'
				);
            }
	}

	/**
	 * Save the meta when the post is saved.
	 */
	public function save( $post_id ) {

		// Check if our nonce is set.
		if ( ! isset( $_POST['albdesign_projects_invoices_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['albdesign_projects_invoices_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'albdesign_projects_invoices_box' ) )
			return $post_id;

		// If this is an autosave, our form has not been submitted,
                //     so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;

		// Check the user's permissions.
		if ( 'albdesign_invoice' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;
	
		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}

		
		//	prettyprint($_POST);
		//die();
		
		$save_invoice_notes_date_status = array();
		
		//save "show default terms also" on this invoice
		$save_invoice_notes_date_status['invoice_personal_notes'] 		= ($_POST['invoice_specific_personal_notes']!='') 			? esc_textarea($_POST['invoice_specific_personal_notes']) : '';
		
		if(isset($_POST['albdesign_show_general_invoice_terms_also'])){
			$save_invoice_notes_date_status['show_default_terms'] 		= ($_POST['albdesign_show_general_invoice_terms_also']=='false') 	? 'no' : 'yes';
		}
		
		
		$save_invoice_notes_date_status['specific_invoice_terms']		= ($_POST['invoice_specific_public_notes']!='') 		? esc_textarea($_POST['invoice_specific_public_notes']) : '';
		$save_invoice_notes_date_status['status']						= ($_POST['albdesign_projects_invoices_status_field_id']!='') 		? $_POST['albdesign_projects_invoices_status_field_id'] : 'unpaid';
		$save_invoice_notes_date_status['toBePaidOn']					= ($_POST['albdesign_invoice_to_be_paid_by_date_field_id']!='') 		? $_POST['albdesign_invoice_to_be_paid_by_date_field_id'] : '';
		$save_invoice_notes_date_status['paidOn']						= ($_POST['albdesign_invoice_paid_date_field_id']!='') 	? $_POST['albdesign_invoice_paid_date_field_id'] : '';
		$save_invoice_notes_date_status['invoice_currency']				= ($_POST['invoice_currency']!='') 	? $_POST['invoice_currency'] : Albdesign_Projects_Settings_Option_Page::get('invoice_default_currency');
		$save_invoice_notes_date_status['invoice_currency_position']	= ($_POST['invoice_currency_position']!='') 	? $_POST['invoice_currency_position'] : Albdesign_Projects_Settings_Option_Page::get('invoice_default_currency_position');
		
		update_post_meta($post_id,'albdesign_projects_invoices_client_field_id',$_POST['albdesign_projects_invoices_client_field_id']);
		
		update_post_meta($post_id,'_albdesign_invoice_notes',$save_invoice_notes_date_status);

		// Sanitize the user input.
		//$mydata = sanitize_text_field( $_POST['myplugin_new_field'] );

		// Update the meta field.
		//update_post_meta( $post_id, '_my_meta_value_key', $mydata );
	}


	/**
	 * Show metabox 
	 */
	public function render_meta_box_content( $post ) {
		wp_nonce_field( 'albdesign_projects_invoices_box', 'albdesign_projects_invoices_box_nonce' );

		// Get existing meta
		$value = get_post_meta( $post->ID, '_my_meta_value_key', true );

		?>
		
		<script type="text/javascript">

			jQuery(document).ready(function() {
				jQuery('#albdesign_invoice_paid_date_field_id , #albdesign_invoice_to_be_paid_by_date_field_id').datepicker({
				});
			});

		</script>
		
		
	
		
			<table class="form-table">
				<tbody>
					<tr>
						<td>
							<table class="form-table albdesignInvoicePageTable">
								<tbody>
									
									<?php
										//get invoice meta
										
										if($post->ID){
											$invoice_notes_date_status = get_post_meta($post->ID,'_albdesign_invoice_notes',true);
											$invoice_personal_note = (isset($invoice_notes_date_status['invoice_personal_notes'])) ? esc_textarea($invoice_notes_date_status['invoice_personal_notes']) : '';
											$invoice_specific_terms = (isset($invoice_notes_date_status['specific_invoice_terms'])) ? esc_textarea($invoice_notes_date_status['specific_invoice_terms']) : '';
											$invoice_show_default_terms = (isset($invoice_notes_date_status['show_default_terms'])) ? 'checked="checked"' : '';
											$invoice_status = (isset($invoice_notes_date_status['status'])) ? $invoice_notes_date_status['status'] : 'unpaid';
											$invoice_to_be_paid_on = (isset($invoice_notes_date_status['toBePaidOn'])) ? $invoice_notes_date_status['toBePaidOn'] : '';
											$invoice_paid_on = (isset($invoice_notes_date_status['paidOn'])) ? $invoice_notes_date_status['paidOn'] : '';
											$invoice_currency_position = (isset($invoice_notes_date_status['invoice_currency_position'])) ? $invoice_notes_date_status['invoice_currency_position'] : Albdesign_Projects_Settings_Option_Page::get('invoice_default_currency_position');
											
											$invoice_currency = (isset($invoice_notes_date_status['invoice_currency'])) ? $invoice_notes_date_status['invoice_currency'] : Albdesign_Projects_Settings_Option_Page::get('invoice_default_currency');
										}
									?>
									
									<tr>
										<td class="at-field" style="vertical-align: top;" >
											<div><?php echo apply_filters('albwppm_invoice_cpt_single_private_notes_label','Invoice Private Notes'); ?></div>
											<textarea name="invoice_specific_personal_notes" id="invoice_specific_personal_notes" style="width:100%" rows="5"><?php echo $invoice_personal_note;?></textarea>
										</td>

										<td class="at-field" style="vertical-align: top;" colspan="2" >
											<div> <?php echo apply_filters('albwppm_invoice_cpt_single_public_notes_label','Invoice Public Notes ( visible on invoice )');?> </div>
											<textarea name="invoice_specific_public_notes" id="invoice_specific_public_notes" style="width:100%"  rows="5"><?php echo $invoice_specific_terms; ?></textarea>
											<div>
												
												<input type="checkbox" name="albdesign_show_general_invoice_terms_also" id="albdesign_show_general_invoice_terms_also" <?php echo $invoice_show_default_terms;?> ><?php echo apply_filters('albwppm_invoice_cpt_single_show_general_terms_also_label','Show general terms also');?>
											</div>
										</td>										

									</tr>

									<tr>
										<td class="at-field" style="vertical-align: top;" >
											<div> <?php echo apply_filters('albwppm_invoice_cpt_single_invoice_status_label','Invoice status');?> </div>
											<select class="at-posts-select" name="albdesign_projects_invoices_status_field_id" id="albdesign_projects_invoices_status_field_id">
												<option value="unpaid" 		<?php selected($invoice_status,'unpaid');?> ><?php echo apply_filters('albwppm_invoice_cpt_single_invoice_status_unpaid_dropdown_option_text','Unpaid');?></option>
												<option value="paid"  		<?php selected($invoice_status,'paid');?> ><?php echo apply_filters('albwppm_invoice_cpt_single_invoice_status_paid_dropdown_option_text','Paid');?></option>
												<option value="overdue" 	<?php selected($invoice_status,'overdue');?> ><?php echo apply_filters('albwppm_invoice_cpt_single_invoice_status_overdue_dropdown_option_text','Overdue');?></option>
												<option value="cancelled"  	<?php selected($invoice_status,'cancelled');?> ><?php echo apply_filters('albwppm_invoice_cpt_single_invoice_status_cancelled_dropdown_option_text','Cancelled');?></option>
											</select>
										</td>
										
										<td class="at-field"  style="vertical-align: top;" >
											<div><?php echo apply_filters('albwppm_invoice_cpt_single_invoice_to_be_paid_by_date_label','To be paid by date');?></div>
											<input type="text" name="albdesign_invoice_to_be_paid_by_date_field_id" id="albdesign_invoice_to_be_paid_by_date_field_id" rel="d MM, yy" value="<?php echo $invoice_to_be_paid_on;?>" size="30">
										</td>			
										<td class="at-field"  style="vertical-align: top;" >
											<div> <?php echo apply_filters('albwppm_invoice_cpt_single_invoice_paid_date_label','Paid date');?> </div>
											<input type="text" name="albdesign_invoice_paid_date_field_id" id="albdesign_invoice_paid_date_field_id"  value="<?php echo $invoice_paid_on;?>" size="30">
										</td>											
									</tr>
									
									<tr>
										<td class="at-field" valign="top">
											<div class="at-label">
												<label for="albdesign_projects_invoices_client_field_id"><?php echo apply_filters('albwppm_invoice_cpt_single_invoice_client_label','Client');?> </label>
											</div>
		
											<select class="at-posts-select" name="albdesign_projects_invoices_client_field_id" id="albdesign_projects_invoices_client_field_id">
												<option value="-1"><?php echo apply_filters('albwppm_invoice_cpt_single_invoice_client_no_client_selected_option_text','No client selected');?></option>
												<?php
													//get all clients
													$get_all_clients_params =	array(
																					'showposts'		=>	-1,
																					'post_type'		=>	'albdesign_client',
																					'post_status'	=> 'publish',
													);
													
													$query_all_clients = new WP_Query();
													$results_all_clients = $query_all_clients->query($get_all_clients_params);
													
													//if we have a client
													if(sizeof($results_all_clients) >= 1 ){ 
														foreach($results_all_clients as $results_single_client){
														
															$selected = '';

															if($post->ID){
																//get client ID saved as meta of invoice
																$clientIDOnInvoiceMeta = get_post_meta($post->ID,'albdesign_projects_invoices_client_field_id',true);
																if($clientIDOnInvoiceMeta){
																	if($clientIDOnInvoiceMeta == $results_single_client->ID){
																		$selected ='selected="selected" ';
																	}
																}
															}
														
															echo '<option value="'.$results_single_client->ID.'" '.$selected.'> '.$results_single_client->post_title.' </option>';
														}														
													}
												?>
											</select>
										</td>
										
										<!-- 
										<td class="at-field" valign="top">
											<div class="at-label">
												<label for="albdesign_projects_invoices_project_field_id">Related to project </label>
											</div>
									
											<select class="at-posts-select" name="albdesign_projects_invoices_project_field_id" id="albdesign_projects_invoices_project_field_id">
												<option value="-1">No project selected</option>
											</select>
										</td>
										
										-->
										
										<td class="at-field" valign="top">
											<div class="at-label">
												<label for="albdesign_projects_invoices_project_list"><?php echo apply_filters('albwppm_invoice_cpt_single_invoice_related_to_project_label','Related to project');?></label>
											</div>

											<span  id="albdesign_projects_invoices_project_list" ></span>
											
										</td>										
										
										
										<!-- 
										<td class="at-field" valign="top">
											<div class="at-label">
												<label for="albdesign_projects_invoices_task_field_id">Related to task </label>
											</div>

											<span  id="albdesign_projects_invoices_task_field_id" ></span>
											
										</td>
										-->

									</tr>
									
									<tr>
										<td class="at-field" valign="top" colspan="3">
											<div id="PersonTableContainer"></div>
										</td>
										
									</tr>	
									<tr>

										<td class="at-field" valign="top" >
										
											<div>
												<div class="albdesign_discount_title"><span > <?php echo apply_filters('albwppm_invoice_cpt_single_invoice_discount_label','Discount');?> </span> </div>
												
												<div class="albdesign_discount_form">
												
													<input type="text" name="invoice_discount_value" class="albdesign_invoice_discount_fields" id="invoice_discount_value"  value="<?php echo Albdesign_Invoice_Table_Metabox::get_vat_or_discount('discountValue') ;?>">
												
													<select name="invoice_discount_type" id="invoice_discount_type"  class="albdesign_invoice_discount_fields" >
														<option value="none" 	<?php selected( Albdesign_Invoice_Table_Metabox::get_vat_or_discount('discountType') ,'0' );?> ><?php echo apply_filters('albwppm_invoice_cpt_single_invoice_discount_none_option_text','None');?></option>
														<option value="percent" <?php selected( Albdesign_Invoice_Table_Metabox::get_vat_or_discount('discountType') , 'percent');?> ><?php echo apply_filters('albwppm_invoice_cpt_single_invoice_discount_percent_option_text','Percent');?></option>
														<option value="amount" 	<?php selected( Albdesign_Invoice_Table_Metabox::get_vat_or_discount('discountType') , 'amount' );?> ><?php echo apply_filters('albwppm_invoice_cpt_single_invoice_discount_amount_option_text','Amount');?></option>
													</select>
													
												</div>
												
												<div class="albdesign_clear"></div>
											</div>	
											
											<div>
												<div class="albdesign_vat_title">
													<span ><?php echo apply_filters('albwppm_invoice_cpt_single_invoice_vat_label','VAT');?> </span>    
												</div>

												<div class="albdesign_vat_form">
													<input type="text" name="invoice_vat_value"  class="albdesign_invoice_discount_fields" id="invoice_vat_value"  value="<?php echo Albdesign_Invoice_Table_Metabox::get_vat_or_discount('vat') ;?>"> %
												</div>
																					
												<div class="albdesign_clear"></div>

											</div>
											
											<div style="display:none">
												<p><?php echo apply_filters('albwppm_invoice_cpt_single_invoice_total_label','VAT');?> <span id="totalInvoiceCost">0</span> </p>
											</div>
											
										</td>
										
										

										<td class="at-field" colspan="3" valign="top" style="vertical-align: top;">
												<div>
													<div class="albdesign_vat_title">
														<span > <?php echo apply_filters('albwppm_invoice_cpt_single_invoice_currency_label','Currency');?> </span>    
													</div>
													<div class="albdesign_vat_form">
														<input type="text" name="invoice_currency"  class="albdesign_invoice_discount_fields" id="invoice_currency"  value="<?php echo $invoice_currency ;?>"> 
													</div>
													<div class="albdesign_clear"></div>
												</div>
												<div>
													<div class="albdesign_vat_title">
														<span > <?php echo apply_filters('albwppm_invoice_cpt_single_invoice_currency_position_label','Currency position');?> </span>    
													</div>
													<div class="albdesign_vat_form">
														<select  name="invoice_currency_position" id="invoice_currency_position">
															<option <?php selected( $invoice_currency_position, 'left'); ?>   value="left"><?php  echo apply_filters('albwppm_invoice_cpt_single_invoice_currency_position_left_of_price_option_text','Left of price');?> </option >
															<option  <?php selected(  $invoice_currency_position, 'right'); ?>    value="right"><?php echo apply_filters('albwppm_invoice_cpt_single_invoice_currency_position_right_of_price_option_text','Right of price');?> </option>
													</select>
													</div>
													<div class="albdesign_clear"></div>
												</div>
										</td>
										
												
										
									</tr>
									
									<tr>
										<td class="at-field" valign="top" colspan="3">
												<input type="button" value="<?php echo apply_filters('albwppm_invoice_cpt_single_invoice_apply_vat_discount_button_text','1. Apply VAT and discount');?>" class="button button-primary button-large albdesign_invoice_page_buttons" id="albdesign_apply_vat_btn">
												
												<input type="button" id="albdesign_preview_invoice" value="<?php echo apply_filters('albwppm_invoice_cpt_single_invoice_generate_invoice_button_text','2. Generate invoice');?>" class="button button-primary button-large albdesign_invoice_page_buttons">
												
												
												<form method="post">
													<input type="submit" id="albdesign_projects_download_invoice" name="albdesign_projects_download_invoice" value="<?php echo apply_filters('albwppm_invoice_cpt_single_invoice_download_invoice_button_text','3. Download invoice');?>" class="button button-primary button-large albdesign_invoice_page_buttons">
												</form>
												
												<span class="albdesign_apply_vat_loader albdesign_loading_ajax">Loading</span>
												
												
												<?php do_action('albwppm_before_invoice_preview'); ?>
												
												<div id="htmlForPdf"></div> 
												
										</td>
									</tr>
									
									<tr>
										<td class="at-field" valign="top" colspan="3">
											
											
												<span class="invoice_preview_text"><?php echo apply_filters('albwppm_invoice_cpt_single_invoice_pdf_invoice_preview_label','Pdf invoice preview');?></span>
											
												<div id="albdesign_pdf_preview_in_browser"></div> 
											
												
													<script>
															jQuery(document).ready(function () {

																	//Check if we have already an invoice in DB
																	<?php
																		if(Albdesign_Invoice_Table_Metabox::maybe_get_existing_pdf_data($post->ID)){
																			?>
																			
																				jQuery('#albdesign_pdf_preview_in_browser').html('<iframe style="width:100%;height:400px" src="data:application/pdf;base64,<?php echo Albdesign_Invoice_Table_Metabox::maybe_get_existing_pdf_data($post->ID) ?>"></iframe>');
																			
																			<?php
																		}
																	?>
																	jQuery('#albdesign_preview_invoice').click(function () {
																		
																		albdesign_projects_functions.disable_invoice_buttons();
																		
																		var clientID = jQuery('#albdesign_projects_invoices_client_field_id').val();
																		jQuery.ajax({
																			url: ajaxurl,
																			type: 'POST',
																			data: {
																				specificNotes 		: jQuery('#invoice_specific_public_notes').val(),
																				privateNotes 		: jQuery('#invoice_specific_personal_notes').val(),
																				showDefaultTerms 	: jQuery('#albdesign_show_general_invoice_terms_also').is(':checked'),
																				invoiceStatus 		: jQuery('#albdesign_projects_invoices_status_field_id').val(),
																				invoiceToBePaid		: jQuery('#albdesign_invoice_to_be_paid_by_date_field_id').val(),
																				invoicePaidOnDate	: jQuery('#albdesign_invoice_paid_date_field_id').val(),
																				invoiceCurrency		: jQuery('#invoice_currency').val(),
																				invoiceCurrencyPosition	: jQuery('#invoice_currency_position').val(),
																				action	  			: 'albdesign_project_invoice_preview_pdf_ajax',
																				clientID			: clientID,
																				invoiceID 			: <?php echo (isset($post->ID)) ? $post->ID : 0 ; ?>
																				
																			},
																			success: function (data) {
																					jQuery('#albdesign_pdf_preview_in_browser').html('<iframe style="width:100%;height:400px" src="data:application/pdf;base64,'+data+'"></iframe>');
																					
																					albdesign_projects_functions.enable_invoice_buttons();
																			},
																			error: function () {
																					console.log('NUKKK na erdhi PDF');		
																			}
																		});
																		
																		
																	});
																
															});
														</script>

										</td>
									</tr>									
									
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>	
		
		<script>
			var albdesign_projects_invoice_details;
		</script>
		
		<style>
			.select2-container-multi .select2-choices .select2-search-field input{
				height:inherit !important;
			}
		</style>
		
		<?php
	} //end render_metabox_content 
	
	
	


	/*
	* Template helper
	*/
	public  function locate_template($file,$atts=array()){

			$template_from_plugin_or_theme = Albdesign_Projects_Settings_Option_Page::get('pdf_template');

			if($template_from_plugin_or_theme=='theme'){
			//check if file exists on theme folder
				if (file_exists(TEMPLATEPATH . '/albdesign_projects/invoice_templates/template/'.$file)){
					$return_template = TEMPLATEPATH .'/albdesign_projects/invoice_templates/template/'.$file;
				}
			}
			else {
				
				//set default theme
				if($template_from_plugin_or_theme==''){
					$template_from_plugin_or_theme='template1';
				}
				
				//no overridings. use the templates from plugin folder
				$return_template = $this->mainPluginPath . '/invoice_templates/'.$template_from_plugin_or_theme.'/'.$file;
			}
			
			return $return_template;
		
	}

	
	/*
	* Get client infos based on invoice id
	*/
	public function get_client_info($invoiceID,$what){
	
		$no_client_found = apply_filters('albwppm_invoice_cpt_single_invoice_no_client_found_label','No Client Found');
	
		$client_id = get_post_meta($invoiceID,'albdesign_projects_invoices_client_field_id',true);
		
		
		if(!$client_id || $client_id <= 0 ){
			return $no_client_found ;
		}
		
		if(!get_post($client_id)){
			return $no_client_found ;
		}

		
		
		if($what=='client_id'){
			return ($client_id > 0 ) ? $client_id :  '-1';
		}
		
		$return_string ='';
		
		if($what=='first_name'){
			
			$return_string = (get_post_meta($client_id,'albdesign_client_first_name_field_id',true)!= false) ? get_post_meta($client_id,'albdesign_client_first_name_field_id',true) : '' ;
		}
		
		if($what=='middle_name'){		
			$return_string =  (get_post_meta($client_id,'albdesign_client_middle_name_field_id',true)!= false) ? get_post_meta($client_id,'albdesign_client_middle_name_field_id',true) : '' ;		
		}
		
		if($what=='last_name'){		
			$return_string =  (get_post_meta($client_id,'albdesign_client_last_name_field_id',true)!= false) ? get_post_meta($client_id,'albdesign_client_last_name_field_id',true) : '' ;
		}
		
		if($what=='address'){		
			$return_string = (get_post_meta($client_id,'albdesign_client_address_field_id',true)!= false) ? nl2br(get_post_meta($client_id,'albdesign_client_address_field_id',true)) : '' ;		
		}
		
		
		return $return_string;
		
		
	}
	
	/*
	* Returns invoice specific currency or default currency
	*/
	public function get_currency($invoice_id=''){

		if($invoice_id!=''){
			return Albdesign_Projects_Invoice_Helpers::get_invoice_notes_value_by_invoice_id($invoice_id,'invoice_currency');
		}
		return Albdesign_Projects_Settings_Option_Page::get('invoice_default_currency');
	}
	
	/*
	* Returns currency position ... left or right of the price
	*/
	public function is_currency_on_right_side_of_price($invoice_id=''){
		
		if($invoice_id!=''){
			return Albdesign_Projects_Invoice_Helpers::get_invoice_notes_value_by_invoice_id($invoice_id,'invoice_currency_position');
			
		}
		return  Albdesign_Projects_Settings_Option_Page::get('invoice_default_currency_position');
	}
	
	/*
	* Returns currency and price 
	*/
	public function format_price_and_currency($price, $invoice_id=''){

		if($invoice_id!=''){
	
			if($this->is_currency_on_right_side_of_price($invoice_id)=='right'){
				return  $price . ' ' . $this->get_currency( $invoice_id) ;
			}
		
		}
		
		return apply_filters('albwppm_invoice_cpt_single_invoice_format_price_and_currency',$this->get_currency($invoice_id) . ' ' . $price, $this->get_currency($invoice_id) , $price);
	}
	
	/*
	* Generate PDF invoice
	*/
	public  function albdesign_project_invoice_preview_pdf_ajax(){

		$invoice_id = (int) $_POST['invoiceID'];
		$clientID   = $_POST['clientID'];
		
		$download_pdf_file = false;
		
		if($invoice_id <= 0){
			die();
		}
	
		if(isset($_POST['download_pdf'])){
			$download_pdf_file=true;
		}
	
	
		//save specific notes,date,status for invoice
		$save_invoice_notes_date_status = array();
		
		//save "show default terms also" on this invoice
		$save_invoice_notes_date_status['invoice_personal_notes'] 	= ($_POST['privateNotes']!='') 			? esc_textarea($_POST['privateNotes']) : '';
		$save_invoice_notes_date_status['show_default_terms'] 		= ($_POST['showDefaultTerms']=='false') 	? 'no' : 'yes';
		$save_invoice_notes_date_status['specific_invoice_terms']	= ($_POST['specificNotes']!='') 		? esc_textarea($_POST['specificNotes']) : '';
		$save_invoice_notes_date_status['status']					= ($_POST['invoiceStatus']!='') 		? $_POST['invoiceStatus'] : 'unpaid';
		$save_invoice_notes_date_status['toBePaidOn']				= ($_POST['invoiceToBePaid']!='') 		? $_POST['invoiceToBePaid'] : '';
		$save_invoice_notes_date_status['paidOn']					= ($_POST['invoicePaidOnDate']!='') 	? $_POST['invoicePaidOnDate'] : 'yes';
		$save_invoice_notes_date_status['invoice_currency']			= ($_POST['invoiceCurrency']!='') 	? $_POST['invoiceCurrency'] : Albdesign_Projects_Settings_Option_Page::get('invoice_default_currency');
		$save_invoice_notes_date_status['invoice_currency_position'] = ($_POST['invoiceCurrencyPosition']!='') 	? $_POST['invoiceCurrencyPosition'] : Albdesign_Projects_Settings_Option_Page::get('invoice_default_currency_position');

		update_post_meta($invoice_id,'_albdesign_invoice_notes',$save_invoice_notes_date_status);
	
		//Update client associated with invoice
		update_post_meta($invoice_id,'albdesign_projects_invoices_client_field_id',$clientID);
		
	
	
		ob_start();
		
		//Company infos
		$company_name = Albdesign_Projects_Settings_Option_Page::get('company_name');
		
		if(Albdesign_Projects_Settings_Option_Page::get('company_logo_img')){
			$company_logo = '<img src="'. Albdesign_Projects_Settings_Option_Page::get('company_logo_img').'">';
		}else{
			$company_logo ='';
		}
		
		$company_address = nl2br(Albdesign_Projects_Settings_Option_Page::get('company_address'));
		
		$company_email = nl2br(Albdesign_Projects_Settings_Option_Page::get('company_email'));
		
		$company_website = nl2br(Albdesign_Projects_Settings_Option_Page::get('company_website'));
		
		$company_mobile = nl2br(Albdesign_Projects_Settings_Option_Page::get('company_mobile'));
		
		//Client infos
		$client_first_name 	= $this->get_client_info($invoice_id,'first_name');
		$client_middle_name = $this->get_client_info($invoice_id,'middle_name');
		$client_last_name 	= $this->get_client_info($invoice_id,'last_name');
		$client_address 	= $this->get_client_info($invoice_id,'address');


		//get invoice items meta
		$items_array = get_post_meta($invoice_id,'_items_on_invoice',true);
		
		//get invoice discount,vat etc
		$invoice_subtotal_meta = get_post_meta($invoice_id,'_invoice_discount_and_vat',true);
		
		//get invoice terms,status,dates
		$invoice_notes_date_status = get_post_meta($invoice_id,'_albdesign_invoice_notes',true);		
		
		//get general invoice terms ...check if should be shown for this invoice
		if(isset($invoice_notes_date_status['show_default_terms'])){
			$invoice_general_terms_and_conditions = ( $invoice_notes_date_status['show_default_terms']=='yes' )  ?  nl2br(Albdesign_Projects_Settings_Option_Page::get('invoice_terms')) : '';
		}
		
		//get specific invoice terms 
		$invoice_specific_terms = (isset($invoice_notes_date_status['specific_invoice_terms'])) ? nl2br($invoice_notes_date_status['specific_invoice_terms']) : '';
		
		//Get Dompdf 
		require_once ( $this->mainPluginPath .'/assets/admin/dompdf/dompdf_config.inc.php');

		//read CSS file of template
		$css_content = file_get_contents($this->locate_template('style.css'));


		
		
		$vat = (isset($invoice_subtotal_meta['vat']) && $invoice_subtotal_meta['vat'] > 0 ) ? $invoice_subtotal_meta['vat'] . ' %'  :  '';

		$discount =''; 
		
		if(isset($invoice_subtotal_meta['discountType']) && $invoice_subtotal_meta['discountValue']> 0 ) {
			if(isset($invoice_subtotal_meta['discountType'])){
				
				//format the discount if its a number 
				if ( $invoice_subtotal_meta['discountType'] =='amount'){
					$discount = number_format(  $invoice_subtotal_meta['discountValue'] , 2 );
					
					$discount = $this->format_price_and_currency($discount,$invoice_id);
					
				}else {
					$discount =  $invoice_subtotal_meta['discountValue'] . ' %';
				}
				
			}
		}
		
		
		
		$subtotal = (isset($invoice_subtotal_meta['invoice_subtotal'])) ?  number_format ( $invoice_subtotal_meta['invoice_subtotal'],2) : '';
		
		$subtotal =  $this->format_price_and_currency($subtotal,$invoice_id);
		
		$subtotal_not_formated = (isset($invoice_subtotal_meta['invoice_subtotal'])) ?  $invoice_subtotal_meta['invoice_subtotal'] : '';
		
		if(isset($invoice_subtotal_meta['discountValue'])){
			if($invoice_subtotal_meta['discountValue'] > 0 &&  $invoice_subtotal_meta['discountType'] !='none') {

				$subtotal_after_discount =  number_format ( Albdesign_Projects_Invoice_Helpers::calculateDiscount($invoice_id,$subtotal_not_formated ,'newvalue'),2) ;
				$subtotal_after_discount =   $this->format_price_and_currency($subtotal_after_discount,$invoice_id);
				$subtotal_after_discount_not_formated =  Albdesign_Projects_Invoice_Helpers::calculateDiscount($invoice_id,$subtotal_not_formated ,'newvalue') ;

			}else{
				$subtotal_after_discount= false ;
				$subtotal_after_discount_not_formated = false;
			}
		}else{
			$subtotal_after_discount= false ;
			$subtotal_after_discount_not_formated = false;			
		}
		
		//if we have discount
		if($subtotal_after_discount == true && $subtotal_after_discount_not_formated > 0 ){
			$total  = number_format ($subtotal_after_discount_not_formated  +   ($subtotal_after_discount_not_formated * $invoice_subtotal_meta['vat']/100),2) ;
			
			$total = $this->format_price_and_currency($total,$invoice_id);
			
		}else{
			//dont have discount
			if (isset($invoice_subtotal_meta['vat']) && $invoice_subtotal_meta['vat'] > 0 ){
				$total_with_vat  = number_format ($subtotal_not_formated  +   ($subtotal_not_formated * $invoice_subtotal_meta['vat']/100),2) ;
			
				$total = $this->format_price_and_currency($total_with_vat,$invoice_id);
				
			}else{

				$total  = $this->format_price_and_currency(  $subtotal_not_formated   ,$invoice_id);
			
			}
		}

		
		ob_start();
		
		require ($this->locate_template('template.php'));
		
		$html = ob_get_clean();

		//print_r($html); die();

		$dompdf = new DOMPDF();
		$dompdf->load_html($html );
		$dompdf->render();

		$pdf_as_base64 = base64_encode($dompdf->output());
		
		//save invoice base64 data into DB
		update_post_meta($invoice_id,'albdesign_projects_invoices_pdf_base64',$pdf_as_base64);
		
		//$dompdf->stream("sample.pdf", array('Attachment'=>'0'));
		die($pdf_as_base64 );	
	}
	
	
	/*
	* Check and return if we have a invoice PDF base64 data in DB
	*/
	
	public function maybe_get_existing_pdf_data($invoiceID){
		
		if(isset($invoiceID)){
			
			$existing_data = get_post_meta($invoiceID,'albdesign_projects_invoices_pdf_base64',true);
			
			if($existing_data){
				return $existing_data;
			}
			
		}
		
		return false;
		
	}
	
	/*
	* Download PDF
	*/
	public function albdesign_project_invoice_pdf_download(){
		
		if(isset($_POST['post_ID'])){
			if($_POST['post_ID'] <= 0){
				return;
			}else{
				$invoice_id = (int)$_POST['post_ID'];
			}
		}
		
		if(isset($_POST['albdesign_projects_download_invoice'])){

			$saved_pdf_base64 = get_post_meta($invoice_id,'albdesign_projects_invoices_pdf_base64',true);

			$saved_pdf_base64= $this->maybe_get_existing_pdf_data($invoice_id);
			
			if($saved_pdf_base64){

				$filename= apply_filters('albwppm_download_pdf_file_name', 'Invoice_'.$invoice_id.'.pdf' ,$invoice_id);
				header('Content-type: application/pdf');
				header('Content-disposition: attachment; filename="'.$filename.'"');
				echo base64_decode($saved_pdf_base64); 
				exit();
				
			}
		}

	}
} //end class

//Start it all 
Albdesign_Invoice_Table_Metabox::get_instance();	