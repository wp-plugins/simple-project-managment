<?php
class Albdesign_Projects_Settings_Option_Page{

	//Under which name to save the array of options
	static private $meta_name = 'albdesign_project_plugin_options';

	
	/*
	* Get saved value for WHAT option or return empty/default string
	*/
	public static function get($what){
	
		$get_option_array = get_option(self::$meta_name,true);

		if(is_array($get_option_array)){
		
			if(isset($get_option_array[$what])){
				return $get_option_array[$what];
			}else{
				return false;
			}
			
		}

		return false;
	}
	

	
	
	static function get_class_meta_name(){
		return self::$meta_name;
	}
	
	
	/*
	* Dropdown of found templates
	*/
	
	static function list_pdf_templates(){
		
		$found_templates = array();
		$array_of_templates = array();
		
		$return_html = '<select name="selected_pdf_template" id="selected_pdf_template">';

		$found_templates['template']['template1']= 'Default Template 1';
		$found_templates['template']['template2']= 'Default Template 2';
		

		//check if we are overriding from the theme folder
		if (file_exists(TEMPLATEPATH . '/albdesign_projects/invoice_templates/template/')){
			$found_templates['template']['theme']= 'Theme Template';
		}		

		$array_of_templates = $found_templates;
		
		//check if we have saved option
		$saved_option = Albdesign_Projects_Settings_Option_Page::get('pdf_template');
		
		foreach($array_of_templates as $single_template_array){
			
			foreach($single_template_array as $single_template_data_key => $single_template_data_value){

				$return_html.='<option value="'.$single_template_data_key.'" '.  selected($saved_option, $single_template_data_key,false) .' >'.$single_template_data_value.'</option>';
				
			}

		}			
		
		$return_html.='</select>';
		
		return $return_html;
		
	}
	

} //end class