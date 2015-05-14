jQuery(document).ready(function(){

	//if CLIENT dropdown is changed 
	jQuery("select#albdesign_projects_invoices_client_field_id").on("change", function(clientDropdown) { 
	
			//if selected a client ID
			if (clientDropdown.val > 0){

				
				
				//remove any project checkbox on "Related to project"
				jQuery('#albdesign_projects_invoices_project_list').text('');
				get_projects_items_on_invoice_ajax();
				
				
				
				/*
				
				//get client infos
				jQuery.ajax({
					  type: 'POST',
					  url: ajaxurl,
					  beforeSend: function( ) {
						jQuery('table.albdesignInvoicePageTable').css({'opacity':'0.2'});
					  },
					  data: {
						action: 'albdesign_project_invoice_cpt_backend_ajax',
						clientID:  clientDropdown.val,
						whathappend: 'clientDropdownChanged' 
					  },
					  dataType: "json",
					  success: function(response, textStatus, XMLHttpRequest){
						
							jQuery('table.albdesignInvoicePageTable').css({'opacity':'1'});

							//check response 
							if(response!=null){
								
								console.log('On CLIENT CHANGE ');
								console.log(response);
								
								//remove all option except the 1st one
								jQuery('#albdesign_projects_invoices_project_field_id option[value!="-1"]').remove();
								
								//jQuery("#albdesign_projects_invoices_task_field_id").empty();
													
								
								//add the response
								//jQuery('#albdesign_projects_invoices_project_field_id').append( jQuery.map(response, function(v){ 
								//		return jQuery('<option>', { val: v.project_id , text: v.project_title ,'data-attr-albdesign_project_price': //v.project_price}); 
								//		}) 
								//);
								
								//remove any project checkbox on "Related to project"
								jQuery('#albdesign_projects_invoices_project_list').text('');
								
								jQuery('#albdesign_projects_invoices_project_list').append( jQuery.map(response, function(v){ 
										return jQuery('<input type="checkbox" data-attr-albdesign_projectID='+ v.project_id +' value="'+ v.project_title+'"  data-attr-albdesign_projectPrice="' + v.project_price  + '" >' + v.project_title +' <br>'); 
									}
									));
								
									
									
								
								//refresh the select2 dropdown
								jQuery('#albdesign_projects_invoices_project_field_id').trigger("change"); 
								
							}
							
							if(response===null){
							
								//remove all option except the 1st on PROJECT
								jQuery('#albdesign_projects_invoices_project_field_id option[value!="-1"]').remove();
								
								//remove any project checkbox on "Related to project"
								jQuery('#albdesign_projects_invoices_project_list').text('');								
								
								//refresh the select2 dropdown on PROJECT
								jQuery('#albdesign_projects_invoices_project_field_id').trigger("change");	

								//clear TASK
								//jQuery("#albdesign_projects_invoices_task_field_id").empty();
								
							}
						
					  },
					  error: function(MLHttpRequest, textStatus, errorThrown){
						console.log(errorThrown);
					  }
				});	
				
				*/
				
			}
			
			//Dont associate to CLIENT  was selected
			if (clientDropdown.val == '-1'){

				//clear PROJECT and TASK dropdown
				jQuery('#albdesign_projects_invoices_project_field_id option[value!="-1"]').remove();
				jQuery('#albdesign_projects_invoices_project_field_id').trigger("change");
				
				//jQuery("#albdesign_projects_invoices_task_field_id").empty();

			}
		
	}); //end on.change CLIENT 

	
	
	/*

	//if PROJECT dropdown is changed 
	jQuery("select#albdesign_projects_invoices_project_field_id").on("change", function(projectsDropdown) {
		
		//if selected a project ID
		if(projectsDropdown.val > 0){

			console.log(projectsDropdown.val);
			
			var projectTitle = projectsDropdown.val;
			var project_price_attr = jQuery(this).children('option:selected').data('attr-albdesign_project_price');
			
			console.log('RESPONSE ON PROJECT CHANGE ');
			console.log('Project PPRICE ' + project_price_attr);
			
			
			//get tasks for the project selected
			jQuery.ajax({
				  type: 'POST',
				  url: ajaxurl,
				  beforeSend: function( ) {
					jQuery('table.albdesignInvoicePageTable').css({'opacity':'0.2'});
				  },
				  data: {
					action: 'albdesign_project_invoice_cpt_backend_ajax',
					projectID:  projectsDropdown.val,
					whathappend: 'projectDropdownChanged' 
				  },
				  dataType: "json",
				  success: function(response, textStatus, XMLHttpRequest){
					
						jQuery('table.albdesignInvoicePageTable').css({'opacity':'1'});

						//check response 
						if(response!=null){
							
							console.log('RESPONSE ON PROJECT CHANGE ');
							console.log(response);
							
							//remove all option from TASKS
							//jQuery('#albdesign_projects_invoices_task_field_id').empty();
							
							//add the response to TASKS
							//jQuery('#albdesign_projects_invoices_task_field_id').append( jQuery.map(response, function(v){ 
							//		return jQuery('<input type="checkbox" data-attr-albdesign_taskID='+ v.task_id +' value="'+ v.task_title+'"  data-attr-albdesign_taskPrice="' + v.task_price  + '" >' + v.task_title +' '+ v.task_status +'<br>'); 
							//}));

						}
											
						if(response===null){

							//Clear TASKS
							//jQuery('#albdesign_projects_invoices_task_field_id').empty();

						}
				  },
				  error: function(MLHttpRequest, textStatus, errorThrown){
					console.log(errorThrown);
				  }
			});	
			
			
		
		}
		
		// Was selected "No project selected" .... clear TASK
		if (projectsDropdown.val == '-1'){

			//Clear TASKS
			//jQuery("#albdesign_projects_invoices_task_field_id").empty();

		}		

	}); //end if PROJECT dropdown is changed 
	
	
	*/
	
	
	//IF A PROJECT CHECKBOX IS CHANGED ... start
	
		jQuery("#albdesign_projects_invoices_project_list").on("change",  'input' , function(taskDropdown) {
			
			//Project was checked ... add it to table
			if (this.checked) {
				
				var projectPrice = 0;
				var checkboxChecked = jQuery(this);
				
				var projectTitle = checkboxChecked.val();
				var projectID = checkboxChecked.attr('data-attr-albdesign_projectID');
				var project_price_attr = checkboxChecked.attr('data-attr-albdesign_projectPrice');
			

				if (typeof project_price_attr !== typeof undefined && project_price_attr !== false) {
				
					projectPrice =  project_price_attr;
					
				}
				
				jQuery('#PersonTableContainer').jtable('addRecord', {
					record: {
						invoiceRowId : projectID,
						ItemName: projectTitle,
						ItemUnitCost: projectPrice,
						ItemQuantity: 1,
						ItemTotalCost: 1,
						is_project :'yes',
					}
				});
			
				
			} else {
				
				//Task was unchecked ... remove it 
				var checkboxUnchecked = jQuery(this);
				var projectID = checkboxUnchecked.attr('data-attr-albdesign_projectID');
				
				jQuery.ajax({
					url: ajaxurl,
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'albdesign_project_items_for_invoice_ajax',
						invoiceAction : 'removeExistingItemFromInvoice' ,
						invoiceItemID : projectID,
						invoiceID	  : jQuery('input#post_ID').val(),
					},
					success: function (data) {
						jQuery('#PersonTableContainer').jtable('reload');
						
					},
					error: function () {
						
					}
				});			

				
			}

		}); 
	
	//IF A PROJECT CHECKBOX IS CHANGED ... ends 
	
	
	
	
	/*
		//if TASK dropdown is changed 

		jQuery("#albdesign_projects_invoices_task_field_id").on("change",  'input' , function(taskDropdown) {
			
			//Task was checked ... add it to table
			if (this.checked) {
				
				var taskPrice = 0;
				var checkboxChecked = jQuery(this);
				
				var taskTitle = checkboxChecked.val();
				var taskID = checkboxChecked.attr('data-attr-albdesign_taskid');
				var task_price_attr = checkboxChecked.attr('data-attr-albdesign_taskprice');
			

				if (typeof task_price_attr !== typeof undefined && task_price_attr !== false) {
				
					taskPrice =  task_price_attr;
					
				}
				
				jQuery('#PersonTableContainer').jtable('addRecord', {
					record: {
						invoiceRowId : taskID,
						ItemName: taskTitle,
						ItemUnitCost: taskPrice,
						ItemQuantity: 1,
						ItemTotalCost: 1,
					}
				});
			
				
			} else {
				
				//Task was unchecked ... remove it 
				var checkboxUnchecked = jQuery(this);
				var taskID = checkboxUnchecked.attr('data-attr-albdesign_taskid');
				
				jQuery.ajax({
					url: ajaxurl,
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'albdesign_project_items_for_invoice_ajax',
						invoiceAction : 'removeExistingItemFromInvoice' ,
						invoiceItemID : taskID,
						invoiceID	  : jQuery('input#post_ID').val(),
					},
					success: function (data) {
						jQuery('#PersonTableContainer').jtable('reload');
						
					},
					error: function () {
						
					}
				});			

				
			}

		}); //end if TASK dropdown is changed 
	
	*/
	
	// START INVOICE TABLE
	
	jQuery('#PersonTableContainer').jtable({
            title: 'Items on invoice',
            actions: {
                listAction: function (postData, jtParams) {
								
								jQuery('.albdesign_apply_vat_loader').css('display','inline-block');
								
								return jQuery.Deferred(function ($dfd) {
									jQuery.ajax({
										url: ajaxurl,
										type: 'POST',
										dataType: 'json',
										data: {
											action: 'albdesign_project_items_for_invoice_ajax',
											invoiceAction : 'getItemsForInvoice',
											invoiceID : jQuery('input#post_ID').val()
										},
										success: function (response) {
											
											$dfd.resolve(response);
											jQuery('.albdesign_apply_vat_loader').css('display','none');
										},
										error: function () {
											$dfd.reject();
										}
									});
								});
							},
                createAction: function (postData) {

								return $.Deferred(function ($dfd) {

									$.ajax({
										url: ajaxurl,
										type: 'POST',
										dataType: 'json',
										data: {
											action: 'albdesign_project_items_for_invoice_ajax',
											invoiceAction : 'addItemToInvoice',
											invoiceID : jQuery('input#post_ID').val(),
											itemData : postData
											
										},
										success: function (data) {

											$dfd.resolve(data);
										},
										error: function () {
											$dfd.reject();
										}
									});
								});
							},
                updateAction: function(postData) {
								
								return $.Deferred(function ($dfd) {
									$.ajax({
										url: ajaxurl,
										type: 'POST',
										dataType: 'json',
										data: {
											action: 'albdesign_project_items_for_invoice_ajax',
											invoiceAction : 'updateExistingItemOnInvoice',
											itemData : postData,
											invoiceID	  : jQuery('input#post_ID').val(),
										},
										success: function (data) {
											$dfd.resolve(data);
											
											$('#albdesign_pdf_preview_in_browser').hide();
											$('#albdesign_preview_invoice').trigger('click');
											$('#albdesign_pdf_preview_in_browser').show();
											
										},
										error: function () {
											$dfd.reject();
										}
									});
								});
							},
							
                deleteAction:function (postData) {
				
								
								return $.Deferred(function ($dfd) {
									$.ajax({
										url: ajaxurl,
										type: 'POST',
										dataType: 'json',
										data: {
											action: 'albdesign_project_items_for_invoice_ajax',
											invoiceAction : 'removeExistingItemFromInvoice' ,
											invoiceItemID : postData.invoiceRowId,
											invoiceID	  : jQuery('input#post_ID').val(),
										},
										success: function (data) {
											$dfd.resolve(data);
										},
										error: function () {
											$dfd.reject();
										}
									});
								});
							},
            },
            fields: {
                invoiceRowId: {
                    key: true,
                    list: false
                },
                ItemName: {
                    title: 'Item',
                    width: '50%'
                },
                ItemUnitCost: {
                    title: 'Unit Price',
                    width: '15%'
                },
                ItemQuantity: {
                    title: 'Quantity',
                    width: '15%',

                },
				ItemTotalCost : {
					title: 'Line Total',
					width: '15%',
					edit : false,
					create: false
				}
            },
			
			messages: {
				addNewRecord: albwppm.table_invoice_add_record_title
			},
			
			recordsLoaded: function (event, data) {updateTotalInvoiceValue(data) },			
			recordUpdated: function (event, data) { jQuery('#PersonTableContainer').jtable('load'); },			
			recordDeleted: function (event, data) { jQuery('#PersonTableContainer').jtable('load'); },			
			recordAdded: function (event, data) { jQuery('#PersonTableContainer').jtable('load'); },			
					
			
        });	
		

		
		function updateTotalInvoiceValue ( data ){
			
				var hiddenArrayForPDF=[];
				hiddenArrayForPDF['discount']=0; 
				hiddenArrayForPDF['vat']=0; 
 
                var total = 0;
                jQuery.each(data.records, function(index, record){
                    total += Number(record.ItemTotalCost);
					
					var hiddenObjectForPDF = {};
					hiddenObjectForPDF.itemName = record.ItemName;
					hiddenObjectForPDF.ItemQuantity = record.ItemQuantity;
					hiddenArrayForPDF.push(hiddenObjectForPDF);    
					hiddenArrayForPDF['invoiceItems'] = hiddenArrayForPDF;

				});
				
				newTotal =  total.toFixed(2);
				jQuery('span#totalInvoiceCost').html(newTotal);
                
				
				//calculate DISCOUNT
				if(calculateDiscount(newTotal,'newvalue')){
					newTotal = calculateDiscount(newTotal,'newvalue');
					jQuery('span#totalInvoiceCost').html(newTotal);
					hiddenArrayForPDF['discount']=calculateDiscount(newTotal,'discountValue'); 
				}

				//calculate VAT 
				var VATField = jQuery('input#invoice_vat_value').val();

				if( jQuery.isNumeric(VATField)){

					calculatedVat =  (Number(newTotal) + Number( newTotal * VATField/100 )).toFixed(2);
					
					jQuery('span#totalInvoiceCost').text(calculatedVat);
					hiddenArrayForPDF['vat']=VATField; 
				}
				
				hiddenArrayForPDF['totalValue']=jQuery('span#totalInvoiceCost').text(); 
				
				//update hidden field
				albdesign_projects_invoice_details = hiddenArrayForPDF;
				
        }
		
		
		
		//UPDATE VAT & DISCOUNT was clicked
		jQuery('input#albdesign_apply_vat_btn').click(function(){
		
			jQuery('.albdesign_apply_vat_loader').css('display','inline-block');
			
			var discountValueEntered = jQuery('input#invoice_discount_value').val();
			var discountTypeEntered = jQuery('select#invoice_discount_type option:selected').val();	
			var vatValueEntered = jQuery('input#invoice_vat_value').val();	

			if(!jQuery.isNumeric(discountValueEntered)){ discountValueEntered=0; }
			if(!jQuery.isNumeric(vatValueEntered)){ vatValueEntered=0; }
			
			albdesign_projects_functions.disable_invoice_buttons();
		
			//save vat,discount
			jQuery.ajax({
				url: ajaxurl,
				type: 'POST',
				dataType: 'json',
				data: {
					action			: 'albdesign_project_invoice_save_vat_discount_ajax',
					vatValue 		: vatValueEntered,
					discountType 	: discountTypeEntered,
					discountValue	: discountValueEntered,
					invoiceID	 	: jQuery('input#post_ID').val(),
				},
				success: function (data) {

					if(data.invoiceUpdated == 'yes'){
						jQuery('#PersonTableContainer').jtable('load');

						jQuery('#albdesign_preview_invoice').trigger('click');
					}
					
					if(data.invoiceUpdated == 'NoItemsOnInvoice'){
						alert('No Items on Invoice. Please add at least one item');
						albdesign_projects_functions.disable_invoice_buttons();

					}

					jQuery('.albdesign_apply_vat_loader').css('display','none');
					
					albdesign_projects_functions.enable_invoice_buttons();
					
				},
				error: function () { }
			});	
		
			
			
		});
	

		function calculateDiscount(actualTotal,returnNewValueOrDiscount){
		
			var discountValueEntered = jQuery('input#invoice_discount_value').val();
			var discountTypeEntered = jQuery('select#invoice_discount_type option:selected').val();
			
			if( jQuery.isNumeric(discountValueEntered) &&  discountTypeEntered != 'none' ){
				
				if(discountTypeEntered=='percent'){
					//check so discounted value isnt lower than 0
					
					var valueAfterDiscount = actualTotal - (Number( actualTotal * discountValueEntered/100 )).toFixed(2);
					
					if( valueAfterDiscount > 0 ){
						
						
						if(returnNewValueOrDiscount=='newvalue'){
							return valueAfterDiscount;
						}else{
							return discountValueEntered + ' %';
						}
					}
					
					return false;		
				}
				
				if(discountTypeEntered=='amount'){
					
					//check so discounted value isnt lower than 0
					if( actualTotal - discountValueEntered > 0 ){
						
						if(returnNewValueOrDiscount=='newvalue'){
							return actualTotal - discountValueEntered;
						}else{
							return discountValueEntered + ' ' ;
						}
					}
					return false;
				}
				
			}else{
				return false;
			}
			
			
		} //ends calculateDiscount
		

		
	    //Load invoice items
        jQuery('#PersonTableContainer').jtable('load');
		

		//IF any projects is on invoice ... show "Related to Project" checkboxes
		function get_projects_items_on_invoice_ajax(){
			jQuery.ajax({
				url: ajaxurl,
				beforeSend: function( ) {
					jQuery('table.albdesignInvoicePageTable').css({'opacity':'0.2'});
					
				},
				type: 'POST',
				dataType: 'json',
				data: {
					action			: 'get_and_check_projects_checkbox_list_ajax',
					clientID	 	: jQuery('select#albdesign_projects_invoices_client_field_id option:selected').val(),
					invoiceID : jQuery('input#post_ID').val(),
				},
				success: function (response) {

					jQuery('table.albdesignInvoicePageTable').css({'opacity':'1'});
				
					console.log('DATA PER AUTOFILL');
					console.log(response);
				
					if(response){
						
						jQuery('#albdesign_projects_invoices_project_list').append( jQuery.map(response, function(v){ 
							
							var is_checked='';
							
							if(v.is_on_invoice_already=='yes'){
								is_checked = ' checked="checked" ';
							}
							
							return jQuery('<input type="checkbox" data-attr-albdesign_projectID='+ v.project_id +' value="'+ v.project_title+'"  data-attr-albdesign_projectPrice="' + v.project_price  + '" '+ is_checked +' >' + v.project_title +' <br>'); 
						}
						
						));
					
					} //if response

				},
				error: function () { }
			});
			
		} //ends get_projects_items_on_invoice_ajax
		
		
		get_projects_items_on_invoice_ajax();
		

});