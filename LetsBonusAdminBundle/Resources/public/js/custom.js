var admin = {
	manageHistoryOnShop: function(shopId, shopHistoryURL){
		$.ajax({
				type: "POST",
				url: shopHistoryURL,
				cache: false,
				dataType: "json",
				data: {
	                shopId: shopId,
				},
				beforeSend: function() {
					$("#shop_voucher_loader").show();
				},
				success: function(data)
				{	
					$( "ul[id$='_shopHistory']" ).hide();
					shopHistory.setLayoutForShops(data,shopId,shopHistoryURL);
				}
			});
	},
	cashbackSettingsShow: function(elements) {
		var html = '';
	
		$.each(elements, function(key, value){

			var text = value["title"];
			text = text.replace("&S", "'S");
			text = text.replace("&s", "'s");

			html += '<li class="shop" style="height: 60px;">';
			html += '<div class="media-left" style=" display: table-cell; float: left; vertical-align: top; padding-right: 10px;"><img src="'+value["image"]+'" style="width:50px;"></div>';
			html += '<div class="media-body"><h5>'+text+'</h5></div>';
			html += '</li>';
		});

		$("ul.sonata-ba-show-many-to-many li").not( ".shop").remove();
		$( "ul.sonata-ba-show-many-to-many" ).append(html);
	},

	cashbackSettings: function(URL,shopArr) {
			 $.ajax({
	                type:"POST",
	                dataType: "json",
	                url: URL,
	                success:function(data)
	                          { 	
	                         
	                          	cashBacklayout.setLayoutForShops(data,shopArr);
	                          	cashBacklayout.setLayoutForShopsJS();
	                         
	                          }
	                });
			 
	},
	manageVouchersOnShop: function(shopId, URL, loader){
		layout.setLoader(loader);
		$(document).on('change',".shop_voucher_program",function(){
			$.ajax({
				type: "POST",
				url: URL,
				cache: false,
				dataType: "json",
				data: {
	                voucherProgramId: $(".shop_voucher_program option:selected").val(),
				},
				beforeSend: function() {
					$("#shop_voucher_loader").show();
				},
				success: function(html)
				{	
					$("#shop_voucher_loader").hide();
					if(shopId){
						if(html.status==1 && html.message=='goif'){
							layout.editModeSelectedVouchersAppend(html);
						} else {
							layout.editModeremoveDefaultSelectedVouchers();
							layout.editModesetInitialLayoutTextBox();
							layout.editModeChangeVouchersByVoucherProgramm(html);
							layout.editModeloadVoucheronSelect(html);
						}
					}else{
						layout.createModeloadVoucheronSelectVoucherProgramms(html);
					}
				}
			});
		});		
	},
	manageNetworkCredentials: function(tradedoublerNetwork,zanoxNetwork,tdiNetwork,ebayNetwork,webgainsNetwork,cjNetwork,amazonNetwork,modeEdit){
		var network = {
			showCurrentNetwork:	function(net){
				if(net==amazonNetwork) {
					var AN = '*[class^="network_amazon_"]';
				}else if(net==cjNetwork) {
					var AN = '*[class^="network_cj_"]';
				}else if(net==ebayNetwork) {
					var AN = '*[class^="network_ebay_"]';
				}else if(net==tdiNetwork) {
					var AN = '*[class^="network_tdti_"]';
				}else if(net==tradedoublerNetwork) {
					var AN = '*[class^="network_tradedoubler_"]';
				}else if(net==webgainsNetwork) {
					var AN = '*[class^="network_webgains_"]';
				}else if(net==zanoxNetwork) {
					var AN = '*[class^="network_zanox_"]';
				}
				$(AN).each(function () {
					network.show($(this));
				});
			},
			hideAllNetwork:	function(){
				$('*[class^="network_"]').each(function () {
					network.hide($(this));
				});
			},
			removeUnselectedNetwork: function(net, all){
				all.each(function(){
					if(net == amazonNetwork) {
						if ($(this).text() != amazonNetwork) {
							$(this).remove();
						}
					}
					if(net == cjNetwork) {
						if ($(this).text() != cjNetwork) {
							$(this).remove();
						}
					}
					if(net == ebayNetwork) {
						if ($(this).text() != ebayNetwork) {
							$(this).remove();
						}
					}
					if(net == tdiNetwork) {
						if ($(this).text() != tdiNetwork) {
							$(this).remove();
						}
					}
					if(net == tradedoublerNetwork) {
						if ($(this).text() != tradedoublerNetwork) {
							$(this).remove();
						}
					}
					if(net == webgainsNetwork) {
						if ($(this).text() != webgainsNetwork) {
							$(this).remove();
						}
					}
					if(net == zanoxNetwork) {
						if ($(this).text() != zanoxNetwork) {
							$(this).remove();
						}
					}
				})
			},
			show:	function(net){
				net.attr('required', true);
				net.parent().parent().show();
			},
			hide:	function(net){
				net.attr('required', false);
				net.parent().parent().hide();
			},
			addMethods:	function(net, all){
				if(modeEdit){
					network.removeUnselectedNetwork(net, all);
					network.showCurrentNetwork(net);
				}else {
					network.hideAllNetwork();
					network.showCurrentNetwork(net);
				}
			},
			checkNetwork: function(net, all){
				if(net==amazonNetwork){
					network.addMethods(net, all);
				}else if(net==cjNetwork){
					network.addMethods(net, all);
				}else if(net==ebayNetwork){
					network.addMethods(net, all);
				}else if(net==tdiNetwork){
					network.addMethods(net, all);
				}else if(net==tradedoublerNetwork){
					network.addMethods(net, all);
				}else if(net==webgainsNetwork){
					network.addMethods(net, all);
				}else if(net==zanoxNetwork){
					network.addMethods(net, all);
				}else{
					network.hideAllNetwork();
				}
			}
		}
		$(document).ready(function(){
			$('*[class^="network_"]').each(function () {
				network.hide($(this));
			});
			var selectedNetwork = $(".selected_network option:selected").text();
			var allNetwork = $(".selected_network option");
			network.checkNetwork(selectedNetwork, allNetwork);
		})

		$(document).on('change',".selected_network",function(){
			var selectedNetwork = $(".selected_network option:selected").text();
			network.checkNetwork(selectedNetwork);
		})
			//$(documen)
			//$(document).on('change',".selected_network",function(){
				/*$.ajax
				({
					type: "POST",
					url: "{#{{ admin.generateUrl('loadcredentialsfields') }}#}",
					data: "id="+$(".network_selected option:selected").val()+"&value="+$(".network_selected option:selected").text(),
					cache: false,
					success: function(html)
					{
						//This is not sure ATM as I am still checking.
						$(".sonata-ba-form").html(html);
					}
				});*/
			//});
		//});
	}
}
var cashBacklayout = {
	
	setLayoutForShops: function(data,shopId){
		var html = '';
		var element = $('div.sonata-ba-field ul.list-unstyled').attr('id');
		var elementKey = element.split('_shop');
		var i = 0;
		html = '<div class="ui-widget-header ui-corner-all ui-multiselect-header ui-helper-clearfix"><ul class="ui-helper-reset"><li><a href="javascript:void(0);" class="ui-multiselect-all"><span>Seleccionar todas</span></a></li><li><a href="javascript:void(0);" class="ui-multiselect-none"><span>Deseleccionar todas</span></a></li></ul></div>';
		html += '<ul class="parent_cat">';
		$.each(data, function(key, value){
			
		html += '<li class="parent_cat_name">';
//		html += '<input type="checkbox" name="parent_cat" value="'+key+'">'+key;
		html += '<a href="javascript:void(0)" class="category_name">'+key+'</a>';
		html += '<ul id="'+elementKey[0]+'_'+i+'_shop" class="list-unstyled ">';
		
			
		$.each(value, function(value_key, value1){
			html += '<li class="shop_li" id="'+elementKey[0]+'_shop_li_'+value1['shop_id']+'">';
			html += '<label class="">';
			
			
			if($.isEmptyObject(shopId) == false)
			{
				var value_exists = $.inArray(value1['shop_id'], shopId);
				if ($.inArray(value1['shop_id'], shopId) != -1)
				{
					html += '<div aria-disabled="false" aria-checked="true" style="position: relative; " class="icheckbox_minimal checked">';
					html += '<input class="checkbox" style="position: absolute; opacity: 0;" id="'+elementKey[0]+'_shop_'+value1['shop_id']+'" name="'+elementKey[0]+'[shop][]" value="'+value1['shop_id']+'" type="checkbox" checked="checked">';
				}
				else
				{
					html += '<div aria-disabled="false" aria-checked="false" style="position: relative; " class="icheckbox_minimal">';
					html += '<input class="checkbox" style="position: absolute; opacity: 0;" id="'+elementKey[0]+'_shop_'+value1['shop_id']+'" name="'+elementKey[0]+'[shop][]" value="'+value1['shop_id']+'" type="checkbox">';
				}
			}
			else
			{
				html += '<div aria-disabled="false" aria-checked="false" style="position: relative; " class="icheckbox_minimal">';
				html += '<input class="checkbox" style="position: absolute; opacity: 0;" id="'+elementKey[0]+'_shop_'+value1['shop_id']+'" name="'+elementKey[0]+'[shop][]" value="'+value1['shop_id']+'" type="checkbox">';
			}
		
			
			/*html += '<ins style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255) none repeat scroll 0% 0%; border: 0px none; opacity: 0;" class="iCheck-helper"></ins>';*/
			html += '</div>';
			html += '<span>'+value1['vprogram_name']+'/'+value1['shop_title']+'</span>';
			html += '</label>';
			html += '</li>';
		});
		
		html += '</ul>';
		html += '</li>';
		
		i = i+1;
		});
		html += '</ul>';

		$( "div[id$='_shop'] div.sonata-ba-field" ).css({"overflow-y":"scroll","width":"100%","height":"300px"});
		$("div[id$='_shop'] div.sonata-ba-field ul.cashback_settings_shop_title").remove();
		$( "div[id$='_shop'] div.sonata-ba-field" ).append(html);
	
	},
	setLayoutForShopsJS: function(data){
		$( "ul[id$='_shop'] li" ).hover(
 				  function() {
							$(this).find("label div.icheckbox_minimal").addClass("hover");
							$(this).children('label').addClass("hover");
						}
				   ,
				    function() {
				    		$(this).find("label div.icheckbox_minimal").removeClass("hover");
							$(this).children('label').removeClass("hover");
				    }
				);

		$( '.shop_li' ).click(function() {

			if($(this).find("label div.icheckbox_minimal").hasClass("checked"))
			{
				$(this).find("label div.icheckbox_minimal").removeClass("checked");
				$(this).find("label div.icheckbox_minimal").attr('aria-checked', 'false');
				$(this).find("label div.icheckbox_minimal input.checkbox").attr("checked",false);
			}
			else
			{
				$(this).find("label div.icheckbox_minimal").addClass("checked");
				$(this).find("label div.icheckbox_minimal").attr('aria-checked', 'true');
				$(this).find("label div.icheckbox_minimal input.checkbox").attr("checked",true);
			}

		});

		$( "a.category_name" ).click(function() {
				$(this).next('ul').children("li").each(function() {
           			if($(this).find("label div.icheckbox_minimal").hasClass("checked"))
						{
							$(this).find("label div.icheckbox_minimal").removeClass("checked");
							$(this).find("label div.icheckbox_minimal").attr('aria-checked', 'false');
							$(this).find("label div.icheckbox_minimal input.checkbox").attr("checked",false);
						}
						else
						{
							$(this).find("label div.icheckbox_minimal").addClass("checked");
							$(this).find("label div.icheckbox_minimal").attr('aria-checked', 'true');
							$(this).find("label div.icheckbox_minimal input.checkbox").attr("checked",true);
						}

          });
		});

		$( "a.ui-multiselect-all" ).click(function() {
				
				$(".parent_cat").find("li.shop_li label div.icheckbox_minimal").addClass("checked");
				$(".parent_cat").find("li.shop_li label div.icheckbox_minimal").attr('aria-checked', 'true');
				$(".parent_cat").find("li.shop_li label div.icheckbox_minimal input.checkbox").attr("checked",true);
				
		});

		$( "a.ui-multiselect-none" ).click(function() {
				
				$(".parent_cat").find("li.shop_li label div.icheckbox_minimal").removeClass("checked");
				$(".parent_cat").find("li.shop_li label div.icheckbox_minimal").attr('aria-checked', 'false');
				$(".parent_cat").find("li.shop_li label div.icheckbox_minimal input.checkbox").attr("checked",false);
				
		});
	},
};
var shopHistory = {
	setLayoutForShops: function(data,shopId,shopHistoryURL){
			var html = '';
			var base_url = window.location.origin;
	
			var create_var = "'"+shopHistoryURL+"'";		
					html += '<table class="shop_history_admin_table_custom">';
					html += '<tr>';
					html += '<th>Título</th>';
					html += '<th>Descripción</th>';
					html += '<th>Condiciones</th>';
					html += '<th>Precio | PCT | LetsBonus</th>';
					html += '<th>Inicio</th>';
					html += '<th>Fin</th>';
					html += '<th>Usuario</th>';
					html += '<th>Acciones</th>';
					html += '</tr>';
					$("#shop_voucher_loader").hide();
					$(data).each(function(key, value){

						var previewUrl = base_url+"/tienda/"+value.shopHistory_slug;

						if(value.history_description) {
							var description = value.history_description;
						}else{
							var description = "-";
						}
						if(value.history_terms){
							var terms = value.history_terms;
						}else{
							var terms = "-";
						}
						if(value.history_cashbackprice){
							var cashbackprice = value.history_cashbackprice + '€';
						}else{
							var cashbackprice = '0€';
						}
						if(value.history_cashbackpercentage){
							var cashbackpercentage = value.history_cashbackpercentage + '%';
						}else{
							var cashbackpercentage = '0%';
						}
						if(value.history_letsbonuspercentage){
							var letsbonuspercentage = value.history_letsbonuspercentage + '%';
						}else{
							var letsbonuspercentage = '0%';
						}
						html += '<tr>';
						html += '<th>'+ value.history_title +'</th>';

						html += '<th>'+ description +'</th>';
						html += '<th>'+ terms +'</th>';
						html += '<th>'+ cashbackprice + ' | ' + cashbackpercentage + ' | ' + letsbonuspercentage + '</th>';

						html += '<th>'+ value.history_startdate +'</th>';
						html += '<th>'+ value.history_enddate +'</th>';
						html += '<th>'+ value.administrator +'</th>';
						html += '<th>';
						html += '<div class="btn-group">';
						html += '<a title="preview" target="_blank" class="btn btn-sm btn-default view_link" href='+previewUrl+'>';
						html += '<i class="glyphicon glyphicon-zoom-in"></i>';
						html += 'Preview';
						html += '</a>';
						html += '<a title="clone" onclick="shopHistory.addCloneForm('+value.history_id+','+shopId+','+value.administrator_id+','+create_var+')" class="btn btn-sm btn-default edit_link" href="#">';
						html += '<i class="glyphicon glyphicon-edit"></i>';
						html += 'Clone';
						html += '</a>';
						html += '</div>';
						html += '</th>';
						html += '</tr>';
					});
					html += '</table>';
					html += '<div class="well well-small form-actions">';
					
					html += '<button onclick="shopHistory.addShopHistoryForm('+shopId+','+create_var+')" name="btn_create_and_edit" id="create_shop_history_admin_table_custom" type="button" class="btn btn-success"><i class="fa fa-save"></i> Create</button>';
					html += '</div>';
					var i=1;
					//$(html).insertAfter( "ul[id$='_shopHistory']" );
					$("div#tab_4 div.box-body .sonata-ba-collapsed-fields em").hide();
					$("div#tab_4 div.box-success div.box-body").append(html);
					$("#loader_image").hide();
					
	},

	setLayoutForShopHistory: function(data){
		$('.popup_wrapper').find('form').remove();
		$('.popup_wrapper div').remove();
		$( "div.popup_wrapper" ).append(data);
		$( "div.popup_parent" ).show();
		$( "div.popup_wrapper" ).show();
		$( "div.popup_wrapper div.sonata-ba-form-error" ).hide();
		$(".close_btn_create").on("click",function() {
	      	$( "div.popup_parent" ).hide();
	      	$( "div.popup_wrapper" ).hide();
			$('.popup_wrapper').find('form').remove();

	    });
		
	},

	setLayoutForShopHistoryClone: function(data){
		$('.popup_wrapper_clone').find('form').remove();
		$('.popup_wrapper_clone div').remove();
		$( "div.popup_wrapper_clone" ).append(data);
		$( "div.popup_parent" ).show();
		$( "div.popup_wrapper_clone" ).show();
		$( "div.popup_wrapper_clone div.sonata-ba-form-error" ).hide();
		$(".close_btn_clone").on("click",function() {
	      	$( "div.popup_parent" ).hide();
			$( "div.popup_wrapper_clone" ).hide();
			$('.popup_wrapper_clone').find('form').remove();
	    });
		
	},
	updateTabWiseCloneLayoutContent: function(){
		var cloneObject = '';
		cloneObject = $(document).find('div.popup_wrapper_clone');
		cloneObject.find('ul.nav-tabs li').click(function () {
			var tabLIObject = $(this);
			var tabLIObjectChild = '';
			tabLIObjectChild = tabLIObject.children().attr('href');
			var targetObject = '';
			var targetObjectElement = '';
			targetObjectElement = 'div' + tabLIObjectChild;
			targetObject = cloneObject.find(targetObjectElement);
			cloneObject.find('div.tab-content .tab-pane').each(function(){
				$(this).removeClass('active in');
			})
			targetObject.addClass('active in');
		});
	},
	addShopHistoryForm: function(shopId,shopHistoryURL){
		var base_url = window.location.origin;
		URL = base_url+"/secure_area/admin/iflair/letsbonusadmin/shophistory/create";

		$.ajax({
				type: "POST",
				url: URL,
				cache: false,
				dataType: "html",
				data: {
	                shopId: shopId,
				},
				beforeSend: function() {
					$("#loader_image").show();
				},
				success: function(data)
				{	
					$("#loader_image").hide();
				
					shopHistory.setLayoutForShopHistory(data);
					$( ".popup_wrapper .bootstrap-datetimepicker-widget" ).first().addClass("first_datepicker");
					$(".first_datepicker").next(".bootstrap-datetimepicker-widget").addClass( "second_datepicker" );
					$( "div.popup_wrapper textarea[id$='_introduction']" ).ckeditor();
					$( "div.popup_wrapper textarea[id$='_description']" ).ckeditor();
					$( "div.popup_wrapper textarea[id$='_tearms']" ).ckeditor();

					/*$("button[name='btn_create']").prop("type", "button");*/
					$("button[name='btn_create']").attr('id', 'create_submit');
					$(".popup_wrapper form").attr('id', 'create_custom_form');
					//var createUrl = $(".popup_wrapper form").attr('action');
					//$("#create_submit").click(function(){
					$("#create_custom_form").one( "submit", function(e) {
						 e.preventDefault();
						$("#loader_image").show();
						shopHistory.addDataShopHistory(shopId,shopHistoryURL);
					});

					$( "div.popup_wrapper input[id$='_shop']" ).val(shopId);
					
				}
			});
	},

	addDataShopHistory: function(shopId,shopHistoryURL){

		$("#create_custom_form").submit(function(e)
			{
			    var postData = $(this).serializeArray();
			    var formURL = $(this).attr("action");
			    $.ajax(
			    {
			        url : formURL,
			        type: "POST",
			        data : postData,
			        beforeSend: function() {
					$("#loader_image").show();
					},
			        success:function(data) 
			        {
			        	$('div#tab_4 div.box-success div.box-body table').remove();
						$('div#tab_4 div.box-success div.box-body .form-actions').remove();
			        	//shopHistory.setLayoutForShops(data,shopId);
			        	admin.manageHistoryOnShop(shopId, shopHistoryURL);
			        	$( "div.popup_parent" ).hide();
						$( "div.popup_wrapper" ).hide();
						$('.popup_wrapper').find('form').remove();

			            //data: return data from server
			        },
			        error: function() 
			        {
			        	console.log("Error");
			        	$("#loader_image").hide();
			            //if fails      
			        }
			    });
			    e.preventDefault(); //STOP default action
			    //e.unbind(); //unbind. to stop multiple form submit.
			});
			 
			$("#create_custom_form").submit(); //Submit  the FORM

	},

	addCloneForm: function(history_id,shopId,administratorId,shopHistoryURL){

		var base_url = window.location.origin;
		URL = base_url+"/secure_area/admin/iflair/letsbonusadmin/shophistory/"+history_id+"/clone";

		$.ajax({
				type: "POST",
				url: URL,
				cache: false,
				dataType: "html",
				data: {
	                shopHistoryId: history_id,
				},
				beforeSend: function() {
					$("#loader_image").show();
				},
				success: function(data)
				{	
					$("#loader_image").hide();
					
					shopHistory.setLayoutForShopHistoryClone(data);
					$( ".popup_wrapper_clone .bootstrap-datetimepicker-widget" ).first().addClass("first_datepicker");
					$(".first_datepicker").next(".bootstrap-datetimepicker-widget").addClass( "second_datepicker" );
					shopHistory.updateTabWiseCloneLayoutContent();
					$( "div.popup_wrapper_clone textarea[id$='_introduction']" ).ckeditor();
					$( "div.popup_wrapper_clone textarea[id$='_description']" ).ckeditor();
					$( "div.popup_wrapper_clone textarea[id$='_tearms']" ).ckeditor();

					$('select[id$="_administrator"] option[value='+administratorId+']').attr('selected','selected');

						/*$("button[name='btn_create']").prop("type", "button");*/
					$("button[name='btn_create']").attr('id', 'update_submit');
					$(".popup_wrapper_clone form").attr('id', 'update_custom_form');
					//var createUrl = $(".popup_wrapper form").attr('action');
					//$("#update_submit").click(function(){
					
					$("#update_custom_form").one( "submit", function(e) {
						 e.preventDefault();
						$("#loader_image").show();
						shopHistory.cloneDataShopHistory(shopId,shopHistoryURL);
					});


					$( "div.popup_wrapper_clone input[id$='_shop']" ).val(shopId);
				}
			});
	},

	cloneDataShopHistory: function(shopId,shopHistoryURL){

		$("#update_custom_form").submit(function(e)
			{
			    var postData = $(this).serializeArray();
			    var formURL = $(this).attr("action");
			    $.ajax(
			    {
			        url : formURL,
			        type: "POST",
			        data : postData,
			        beforeSend: function() {
					$("#loader_image").show();
					},
			        success:function(data) 
			        {
			        	$('div#tab_4 div.box-success div.box-body table').remove();
						$('div#tab_4 div.box-success div.box-body .form-actions').remove();
			        	//shopHistory.setLayoutForShops(data,shopId);
			        	admin.manageHistoryOnShop(shopId, shopHistoryURL);
			        	$( "div.popup_parent" ).hide();
						$( "div.popup_wrapper_clone" ).hide();
						$('.popup_wrapper_clone').find('form').remove();
			            //data: return data from server
			        },
			        error: function() 
			        {
			        	console.log("Error");
			        	$("#loader_image").hide();
			            //if fails      
			        }
			    });
			    e.preventDefault(); //STOP default action
			    //e.unbind(); //unbind. to stop multiple form submit.
			});
			 
			$("#update_custom_form").submit(); //Submit  the FORM

	},

}
var layout = {
	setLoader: function(loader){
		var img = '<img id="shop_voucher_loader" style="display:none;" src='+loader+' />'
		$(document).ready(function(){
			$('select.shop_voucher').parent().parent().append(img);
		})
	},
	editModeSelectedVouchersAppend:	function(data){
		if(data.selected_vouchers!=0){
			var showSelectedVouchers = '';
			$.each(data.selected_vouchers, function(key, value){
				if(key!='status' && key!='message' && key!='count'){
					showSelectedVouchers += '<li class="select2-search-choice">';
					showSelectedVouchers += '<div>'+value.title+'</div>';
					showSelectedVouchers += '<a voucher-id="'+value.id+'" tabindex="-1" class="select2-search-choice-close" href="#"></a>';
					showSelectedVouchers += '</li>';
				}
			})
			$('.select2-choices').html(showSelectedVouchers);
		}

		var setSelectedVoucherBackgroundAsSelected = '';
		$.each(data.selected_vouchers, function(key, value){
			if(key!='status' && key!='message' && key!='count'){
				setSelectedVoucherBackgroundAsSelected += ('<option selected="selected" value="' + value.id + '">' + value.title + "</option>");
			}
		});
		$('select.shop_voucher').find("option").remove();
		$("select.shop_voucher").append(setSelectedVoucherBackgroundAsSelected);
		if(data.non_selected_vouchers!=0){
			var setNonSelectedVoucherBackground = '';
			$.each(data.non_selected_vouchers, function(key, value){
				if(key!='status' && key!='message' && key!='count'){
					setNonSelectedVoucherBackground += ('<option value="' + value.id + '">' + value.title + "</option>");
				}
			});
			$("select.shop_voucher").append(setNonSelectedVoucherBackground);
		}

		$(".select2-search-choice-close").bind("click", function(){
			var stxt = $(this).prev().text();
			var voucherId = $(this).attr('voucher-id');
			$(this).parent().remove();
			//$('.shop_voucher option:contains('+stxt+')').removeAttr("selected");
			$('.shop_voucher').find('option[value='+voucherId+']').removeAttr("selected");
		});
		$(document).on('change',".shop_voucher",function(){
			var marr = $(this).val();
			var hmt='';
			for(var i=0; i<marr.length; i++){
				$('.shop_voucher').find('option[value='+marr[i]+']').attr("selected","selected");
				$.each(data.selected_vouchers, function(key, value){
					if(value.id == marr[i]){
						hmt += '<li class="select2-search-choice">';
						hmt += '<div>'+value.title+'</div>';
						hmt += '<a voucher-id="'+value.id+'" tabindex="-1" class="select2-search-choice-close" href="#"></a>';
						hmt += '</li>';
					}
				});
				$.each(data.non_selected_vouchers, function(key, value){
					if(value.id == marr[i]){
						hmt += '<li class="select2-search-choice">';
						hmt += '<div>'+value.title+'</div>';
						hmt += '<a voucher-id="'+value.id+'" tabindex="-1" class="select2-search-choice-close" href="#"></a>';
						hmt += '</li>';
					}
				});
			}
			$(".select2-search-choice").each(function() {
			    $(this).remove();
			});
			$('ul.select2-choices').prepend(hmt);
			$(".select2-search-choice-close").bind("click", function(){
				var stxt = $(this).prev().text();
				$(this).parent().remove();
				var voucherId = $(this).attr('voucher-id');
				//$('.shop_voucher option:contains('+stxt+')').removeAttr("selected");
				$('.shop_voucher').find('option[value='+voucherId+']').removeAttr("selected");
			});
		});
	},
	editModeChangeVouchersByVoucherProgramm: function(data){
		var html = '';
		$.each(data, function(key, value){
			if(key!='status' && key!='message' && key!='count'){
				html += ('<option value="' + key + '">' + value + "</option>");
			}
		});
		$('select.shop_voucher').find("option").remove();
		$("select.shop_voucher").append(html);
	},
	editModeremoveDefaultSelectedVouchers: function(){
		$(".select2-search-choice").each(function() {
		    $(this).remove();
		});
	},
	editModesetInitialLayoutTextBox: function(){
		var html = '';
		html += '<li class="select2-search-field">';
		html += '<label class="select2-offscreen" for="s2id_autogen10">';
		html += 'Voucher';
		html += '</label>';
		html += '<input type="text" class="select2-input" spellcheck="false" autocapitalize="off" autocorrect="off" autocomplete="off" id="s2id_autogen10" style="width: 20px;" placeholder="" aria-activedescendant="select2-result-label-373">';
		html += '</li>';
		$('ul.select2-choices').html(html);
	},
	editModeloadVoucheronSelect: function(data){
		$(document).on('change',".shop_voucher",function(){
			var marr = $(this).val();
			var hmt='';
			for(var i=0; i<$(this).val().length; i++){
				$.each(data, function(key, value){
					if(key==marr[i]){
						hmt += '<li class="select2-search-choice">';
						hmt += '<div>'+value+'</div>';    
						hmt += '<a voucher-id="'+value.id+'" tabindex="-1" class="select2-search-choice-close" href="#"></a>';
						hmt += '</li>';
					}
				});
			}			
			$(".select2-search-choice").each(function() {
			    $(this).remove();
			});
			$('ul.select2-choices').prepend(hmt);
			$(".select2-search-choice-close").bind("click", function(){
				var stxt = $(this).prev().text();
				$(this).parent().remove();
				var voucherId = $(this).attr('voucher-id');
				//$('.shop_voucher option:contains('+stxt+')').removeAttr("selected");
				$('.shop_voucher').find('option[value='+voucherId+']').removeAttr("selected");
			});
		});
	},
	createModeloadVoucheronSelectVoucherProgramms: function(html){
		var htm = '';
		$.each(html, function(key, value){
			if(key!='status' && key!='message' && key!='count'){
				htm += ('<option value="' + key + '">' + value + "</option>");
			}
		});
		$(".select2-search-choice").each(function() {
		    $(this).remove();
		});
		$('select.shop_voucher').find("option").remove();
		$("select.shop_voucher").append(htm);
	}
};