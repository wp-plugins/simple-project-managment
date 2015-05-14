<?php
/*
Plugin Name: Simple Project Manager
Plugin URI: https://profiles.wordpress.org/albdesign
Description: Simple project management
Author: Albdesign
Version: 1.0.0
Author URI: https://profiles.wordpress.org/albdesign
*/


class Albdesign_Project_Management {

	private $plugin_slug ='albproject';
	private $singular_cpt_name = 'Project';
	private $plural_cpt_name = 'Projects';
	private static $instance = null;
	private $plugin_path;
	private $plugin_url;

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
	 * Initializes the plugin by setting localization, hooks, filters, and administrative functions.
	 */	
	function __construct(){
	
		$this->plugin_path = plugin_dir_path( __FILE__ );
		$this->plugin_url  = plugin_dir_url( __FILE__ );	

		// Register different CPT_s
		add_action( 'init', array($this,'register_cpts'), 0 );

		//add custom columns to PROJECTS
		add_action( 'manage_posts_custom_column' , array($this,'show_project_custom_columns'), 10, 2 );
		add_action( 'manage_edit-albdesign_project_columns' , array($this,'add_project_custom_columns'), 10, 2 );
	
		//add custom columns to TASK
		add_action( 'manage_posts_custom_column' , array($this,'show_task_custom_columns'), 10, 2 );
		add_action( 'manage_edit-albdesign_task_columns' , array($this,'add_task_custom_columns'), 10, 2 );	
		
		//add custom columns to CLIENTS
		add_action( 'manage_posts_custom_column' , array($this,'show_clients_custom_columns'), 10, 2 );
		add_action( 'manage_edit-albdesign_client_columns' , array($this,'add_clients_custom_columns'), 10, 2 );	
		
		//add custom columns to INVOICES
		add_action( 'manage_posts_custom_column' , array($this,'show_invoice_custom_columns'), 10, 2 );
		add_action( 'manage_edit-albdesign_invoice_columns' , array($this,'add_invoice_custom_columns'), 10, 2 );		
		
		//add ajax function to update client infos if associated with WP account
		add_action( 'wp_ajax_update_client_infos_if_associated_ajax', array( $this, 'update_client_infos_if_associated_ajax'));
		
		//add admin css
		add_action('admin_enqueue_scripts', array($this,$this->plugin_slug.'_admin_css'));

		//add admin js
		add_action('in_admin_footer', array($this,'admin_footer'));
		
		$this->run_plugin();	
		
		//add metaboxes
		add_action( 'admin_menu', array( $this, 'albproject_add_meta_boxes' ) );

		//remove ADD PROJECT from menu on the left
		add_action('admin_menu', array($this,'remove_or_add_submenu_pages'));
		
		//do extra checks when our CPT-s are saved/updated
		add_action('save_post',array($this,'save_post'));
		
		//remove QUICK EDIT
		add_filter('post_row_actions',array($this,'remove_quick_edit'),10,2);
		
		
		require_once('include/helpers/invoice.helper.class.php');
		require_once('include/invoices.table.metabox.php');
		
		
	}	
	
	/*
	* Remove QUIK EDIT on our CPT-s
	*/
	public function remove_quick_edit($actions ){
		
		global $post;
		
		if( $post->post_type == 'albdesign_project' ||  $post->post_type == 'albdesign_task'  ||  $post->post_type == 'albdesign_client'  ||  $post->post_type == 'albdesign_invoice'  ) {
			
			unset($actions['inline hide-if-no-js']);
			
		}
		
		return $actions;
		
	}
	
	public function run_plugin(){
	
		require('include/helpers.php');
		require_once('include/meta-box-class/my-meta-box-class.php');

	}
	

	function admin_footer(){
	
		global $pagenow, $typenow;
		if( $typenow=='albdesign_invoice' || $typenow =='albdesign_project' || $typenow == 'albdesign_task' || $typenow == 'albdesign_client' ){
			wp_enqueue_script( 'albdesign-projects-admin-script', $this->get_plugin_url() . 'assets/admin/js/admin.js', array( 'jquery' ), null, true );
		}

	}
	
	public function get_plugin_url() {
		return $this->plugin_url;
	}

	public function get_plugin_path() {
		return $this->plugin_path;
	}	
	
	/*
	* Admin Scripts,Styles
	*/
	public function albproject_admin_css(){
		
		global $pagenow, $typenow;
		
		if( $typenow=='albdesign_invoice' || $typenow =='albdesign_project' || $typenow == 'albdesign_task' || $typenow == 'albdesign_client' ){
			wp_enqueue_style($this->plugin_slug.'_admin', $this->get_plugin_url().'assets/admin/css/projects_admin.css');
		}
		 
		wp_register_script($this->plugin_slug.'-circular-diagram',$this->get_plugin_url().'assets/circle-diagram/js/circle-progress.js' ,array( 'jquery' ) );
		wp_register_style($this->plugin_slug.'-circular-diagram',$this->get_plugin_url().'assets/circle-diagram/css/style.css' );
	}
	
	/*
	* Return WP account infos if client is associated with an account
	*/
	public function update_client_infos_if_associated_ajax(){
		$userid = (int) $_POST['userID'];

		if( false == get_user_by( 'id', $userid ) ) {
			$return = array('albdesign_found_user' => 'not');
		}else{
			
			$userFound = get_user_by( 'id', $userid );
		
			$return = array(
					'albdesign_found_user' 	=>	'yes',
					'user_id' 				=>	$userFound->ID,
					'user_first_name' 		=>	$userFound->first_name,
					'user_last_name' 		=>	$userFound->last_name,
					'user_email' 			=>	$userFound->user_email,
					);
		}

		
		die(json_encode($return));
	}
	
	
	/*
	* Register the different CPT_s 
	*/
	function register_cpts() {

		require_once('include/cpts.php');
		
		$projects 	= new Albdesign_Register_CPT('Project','Projects');
		$tasks 		= new Albdesign_Register_CPT('Task','Tasks','edit.php?post_type=albdesign_project');
		$clients 	= new Albdesign_Register_CPT('Client','Clients','edit.php?post_type=albdesign_project');
		
		$invoices 	= new Albdesign_Register_CPT('Invoice','Invoices','edit.php?post_type=albdesign_project');
		
		//remove editor from INVOICE
		remove_post_type_support( 'albdesign_invoice', 'editor' );
		

		do_action($this->plugin_slug.'_add_new_cpt');
	}	
	
	//Remove "Add Project" from admin menu
	function remove_or_add_submenu_pages() { 
	
		remove_submenu_page('edit.php?post_type=albdesign_project', 'post-new.php?post_type=albdesign_project');
		
		//Add "Reports" as submenu to project
		add_submenu_page( 'edit.php?post_type=albdesign_project', 'Reports', 'Reports', 'manage_options', 'albdesign-project-Reports', array($this,'reports_submenu_page_callback' ));		
		
		//Add "Settings" as submenu to project
		add_submenu_page( 'edit.php?post_type=albdesign_project', 'Settings', 'Settings', 'manage_options', 'albdesign-project-settings', array($this,'settings_submenu_page_callback' ));

	}	
	
	
	/*
	*	Add metaboxes to CPT_s
	*/	
	function albproject_add_meta_boxes(){
	
		if (is_admin()){

			require_once('include/meta-box-class/my-meta-box-class.php');
			
			//Projects metaboxes
			require_once('include/projects.metabox.php');

			//Task metaboxes
			require_once('include/tasks.metabox.php');
			
			//Clients metaboxes
			require_once('include/clients.metabox.php');

			//Invoices metaboxes
			//require_once('include/invoices.metabox.php');
			require_once('include/invoices.table.metabox.php');

		} //end if is_admin
		
	}

	

	//Show additional columns on PROJECT list
	function show_project_custom_columns( $column, $post_id ) {
		require('include/projects_list_extra_columns.php');		
	}
	
	//Show additional columns on TASK list
	function show_task_custom_columns( $column, $post_id ) {
		require('include/tasks_list_extra_columns.php');		
	}	

	//Show additional columns on CLIENTS list
	function show_clients_custom_columns( $column, $post_id){
		require('include/clients_list_extra_columns.php');
	}
	
	function add_project_custom_columns($columns) {
		//remove default WP date column
		unset($columns['date']);
		
		$columns['title'] 					=	apply_filters('albwppm_projects_cpt_list_post_table_header_project_text','Project');
		$columns['deadline'] 				=	apply_filters('albwppm_projects_cpt_list_post_table_header_deadline_text','Deadline');
		$columns['status'] 	 				=	apply_filters('albwppm_projects_cpt_list_post_table_header_status_text','Status');
		$columns['get_tasks_for_project'] 	=	apply_filters('albwppm_projects_cpt_list_post_table_header_tasks_text','Tasks');
		$columns['client']	 				=	apply_filters('albwppm_projects_cpt_list_post_table_header_client_text','Client');
		$columns['earnings'] 				=	apply_filters('albwppm_projects_cpt_list_post_table_header_earning_text','Earning');

		return apply_filters('albwppm_projects_cpt_list_post_table_header_array',$columns);
		
		
	}

	
	function add_task_custom_columns($columns){
		//remove default WP date column
		unset($columns['date']);
		
		$columns['title'] 				= 	apply_filters('albwppm_task_cpt_list_post_table_header_task_title_text','Task');
		$columns['task_deadline'] 		= 	apply_filters('albwppm_task_cpt_list_post_table_header_task_deadline_text','Task Deadline');
		$columns['task_status'] 		= 	apply_filters('albwppm_task_cpt_list_post_table_header_task_status_text','Task Status');
		$columns['task_for_project']	= 	apply_filters('albwppm_task_cpt_list_post_table_header_task_project_text','Project');
		
		return apply_filters('albwppm_tasks_cpt_list_post_table_header_array',$columns);

	}
	

	function add_clients_custom_columns($columns){

		//remove default WP date column
		unset($columns['date']);	
	
		$columns['title'] 								=	apply_filters('albwppm_clients_cpt_list_post_table_header_client_name','Client');
		$columns['albdesign_projects_client_projects'] 	=	apply_filters('albwppm_clients_cpt_list_post_table_header_projects','Projects');
		$columns['albdesign_projects_client_invoices'] 	=	apply_filters('albwppm_clients_cpt_list_post_table_header_invoices','Invoices');
		$columns['albdesign_projects_client_reviews'] 	=	apply_filters('albwppm_clients_cpt_list_post_table_header_reviews','Reviews');		
		
		return apply_filters('albwppm_clients_cpt_list_post_table_header_array',$columns);

	}
	
	
	function show_invoice_custom_columns( $column, $post_id){
		require('include/invoice_list_extra_columns.php');
	}
	
	function add_invoice_custom_columns($columns){
	
		//remove default WP date column
		unset($columns['date']);
		
		$columns['title'] 										=	apply_filters('albwppm_invoice_cpt_list_post_table_header_invoice_name','Invoice');
		$columns['albdesign_projects_invoice_total'] 			=	apply_filters('albwppm_invoice_cpt_list_post_table_header_invoice_total','Total');
		$columns['albdesign_projects_invoice_status'] 			=	apply_filters('albwppm_invoice_cpt_list_post_table_header_invoice_status','Status');
		$columns['albdesign_projects_invoice_for_client'] 		=	apply_filters('albwppm_invoice_cpt_list_post_table_header_invoice_client_name','Client');

		return $columns;	
	}
	
	/*
	* Add "Reports" as submenu page
	*/
	function reports_submenu_page_callback(){
		require_once('include/admin_reports.class.php');
		require_once('include/helpers/invoice.helper.class.php');
		require_once('include/helpers/clients.helper.class.php');
		require_once('include/helpers/tasks.helper.class.php');
		require_once('include/admin_reports_page.php');

	}
	
	
	/*
	* Add "Settings" as submenu to project
	*/
	function settings_submenu_page_callback(){
		require_once('include/admin_settings_page.php');
	}
	

	/*
	* Additional functions when our CPT-s are saved
	*/
	function save_post($postID){
	
		if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || (defined('DOING_AJAX') && DOING_AJAX) || isset($_REQUEST['bulk_edit']) || false !== wp_is_post_revision( $postID )){
			return;
		}
	
		if(get_post_type($postID) == 'albdesign_project'){
			
			//start date
			if(isset($_POST['albdesign_project_start_date_field_id'])){
				if($_POST['albdesign_project_start_date_field_id']!=''){
					update_post_meta($postID,'albdesign_project_start_date_field_id_timestamp',self::convert_human_date_to_unix($_POST['albdesign_project_start_date_field_id']));
				}
			}

			//target end date
			if(isset($_POST['albdesign_project_target_end_date_field_id'])){
				if($_POST['albdesign_project_target_end_date_field_id']!=''){
					update_post_meta($postID,'albdesign_project_target_end_date_field_id_timestamp',self::convert_human_date_to_unix($_POST['albdesign_project_target_end_date_field_id']));
				}
			}			
			
			//end date
			if(isset($_POST['albdesign_project_end_date_field_id'])){
				if($_POST['albdesign_project_end_date_field_id']!=''){
					update_post_meta($postID,'albdesign_project_end_date_field_id_timestamp',self::convert_human_date_to_unix($_POST['albdesign_project_end_date_field_id']));
				}
			}
	
		}
	
	}
	


	/**
	 *  @brief Convert a date with format
	 *  
	 *  @param [in] $date 22-05-1981
	 *  @return unixtimestamp
	 *  
	 *  @details Details
	 */
	static function convert_human_date_to_unix($date){
		$converted_date = date_parse_from_format('d-m-Y', $date);
		$timestamp = mktime(0, 0, 0, $converted_date['month'], $converted_date['day'], $converted_date['year']);
		
		return $timestamp;
	}
	
}

//Start it all 
Albdesign_Project_Management::get_instance();

$GLOBALS['kari'] = Albdesign_Project_Management::get_instance();

//Options page helper class 
require_once('include/settings.options.class.php');