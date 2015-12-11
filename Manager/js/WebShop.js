function WebShop(action,title,w,h)
{
	if($.window.getWindow('WebShop'+action))
	{
		var opened = $.window.getWindow('WebShop'+action);
		if(opened.isMinimized) opened.restore();
		opened.select();
		opened.refreshWindow();
	}
	else
	{
		var HDWindow = OpenWindow(title, "./Controllers/WebShop.php?action="+action+"", w, h);
		HDWindow.setWindowId('WebShop'+action);
	}
}

function LogList()
{
	var memb___id = $("#memb___id").val();
	var currency = $("#currency").val();	
	var starting_date = $("#starting_date").val();
	var ending_date = $("#ending_date").val();
	var canceled = $("input[type=checkbox][name=canceled]:checked").val();
	var Search = $("input[type=checkbox][name=search]:checked").val();
	
	$("#SearchResults").html("Loading... please wait.");
	
	$.post("./Controllers/WebShop.php?action=results", { memb___id:memb___id, currency:currency, starting_date:starting_date, ending_date:ending_date, canceled:canceled, Search:Search }, function(data) {
		$("#SearchResults").html(data);
	});
}

function CancelPurchase()
{
	var purchases = new Array();
	
	$.each($("input[type=checkbox][name=cancel]:checked"), function() {
		purchases.push($(this).val());
	});
	
	$.post("./Controllers/WebShop.php?action=cancelPurchases", { purchases:purchases }, function(data) {
		$.Growl.show(data);
		LogList();
	});
}

function SearchItem(idx,table)
{
	$("#ItemLocation_"+idx).html("<img src='img/wait.gif' width='25px' height='25px'>");
	$.post("./Controllers/WebShop.php?action=searchItem", { idx:idx, table:table }, function(data) {
		$("#ItemLocation_"+idx).html(data);
	});
}

function ToggleInsurance(idx)
{
	$("#insurance_"+idx).html("<img src='img/wait.gif' width='25px' height='25px'>");
	$.post("./Controllers/WebShop.php?action=toggleInsurance", { idx:idx }, function(data) {
		$("#insurance_"+idx).html(data);
	});
}

function WebShopAddCategory()
{
	var category = $("#newCategory").val();
	var mainCategory = $("#mainCategory").val();
	var pack; ($("input[type=checkbox][name=newCatPack]:checked").length) ? pack = $("input[type=checkbox][name=newCatPack]:checked").val() : pack = 0;
	
	var thisWindow = $.window.getSelectedWindow();
	
	$.post("./Controllers/WebShop.php?action=addCategory", { category:category, mainCategory:mainCategory, pack:pack }, function(data) {
		thisWindow.refreshWindow();
	});
}

function MoveCategory(id, dir)
{
	var thisWindow = $.window.getSelectedWindow();
	$.post("./Controllers/WebShop.php?action=moveCategory", { id:id, dir:dir }, function()
	{
		thisWindow.refreshWindow();
	});
}

function DeleteCategory(idx)
{
	var thisWindow = $.window.getSelectedWindow();
	if(confirm("Do you confirm?"))
	{
		$.post("./Controllers/WebShop.php?action=deleteCategory", { idx:idx }, function()
		{
			thisWindow.refreshWindow();
		});
	}
}

function SaveCategory(idx)
{
	var thisWindow = $.window.getSelectedWindow();
	
	var category = $("#category"+idx).val();
	var mainCategory = $("#mainCategory"+idx).val();
	var pack; ($("input[type=checkbox][name=isPack"+idx+"]:checked").length) ? pack = $("input[type=checkbox][name=isPack"+idx+"]:checked").val() : pack = 0;
	
	$.post("./Controllers/WebShop.php?action=saveCategory", { category:category, mainCategory:mainCategory, pack:pack, idx:idx }, function(data) {
		$.Growl.show(data);
		thisWindow.refreshWindow();
	});
}

function LoadItemsByType()
{
	var type = $("#itemType").val();
	
	$.post("./Controllers/WebShop.php?action=loadItemsByType", { type:type }, function(data) {
		$("#itemIndex").html(data);
		ActivateCreationButton();
	});
}

function ActivateCreationButton()
{
	if($("#itemType").val() != null && $("#itemIndex").val() != null && $("#itemCategory").val() != null)
	{
		$("#createItem").removeClass('ui-button-disabled ui-state-disabled');
	}
	else
	{
		$("#createItem").addClass('ui-button-disabled ui-state-disabled');
	}
}

function CreateItemForm()
{
	if($("#itemType").val() == null || $("#itemIndex").val() == null || $("#itemCategory").val() == null)
		return false;
		
	var itemType = $("#itemType").val();
	var itemIndex = $("#itemIndex").val();
	var itemCategory = $("#itemCategory").val();
	
	var thisWindow = $.window.getSelectedWindow();
	thisWindow.close();
	
	if($.window.getWindow('WebShopItemConfig'))
	{
		var opened = $.window.getWindow('WebShopItemConfig');
		if(opened.isMinimized) opened.restore();
		opened.select();
	}
	else
	{
		var HDWindow = OpenWindow("WebShop Item Config", "./Controllers/WebShop.php?action=itemForm&itemType="+itemType+"&itemIndex="+itemIndex+"&itemCategory="+itemCategory, 450, 600);
		HDWindow.setWindowId('WebShopItemConfig');
	}	
}

function SaveItem(category, type, index)
{
	var thisWindow = $.window.getSelectedWindow();
	
	var insurance = $("#insurance").val();
	var limit = $("#limit").val();
	var max_amount = $("#max_amount").val();
	var max_exc_opts = $("#max_exc_opts").slider("value");
	var min_level = $("#level").slider("values",0);
	var max_level = $("#level").slider("values",1);
	var addopt = $("#addopt").slider("value");	
	var skill = $("#skill").val();
	var luck = $("#luck").val();
	var ancient = $("#ancient").val();
	var harmony = $("#harmony").val();
	var opt380 = $("#opt380").val();	
	var max_socket = $("#max_socket").slider("value");
	var socket_level = $("#socket_level").slider("value");
	var socket_empty = $("#socket_empty").val();
	var currency = $("#currency").val();
	var base_price = $("#base_price").val();
	var status = $("#status").val();
	var cancellable = $("#cancellable").val();
	var vip_item = $("#vip_item").val();
	
	$.post("./Controllers/WebShop.php?action=saveItem", { category:category, type:type, index:index, insurance:insurance, limit:limit, max_amount:max_amount, max_exc_opts:max_exc_opts, min_level:min_level, max_level:max_level, addopt:addopt, skill:skill, luck:luck, ancient:ancient, harmony:harmony, opt380:opt380, max_socket:max_socket, socket_level:socket_level, socket_empty:socket_empty, currency:currency, base_price:base_price, status:status, cancellable:cancellable, vip_item:vip_item }, function(data) {
		$.Growl.show(data);
		thisWindow.close();
	});	
}

function SaveEditedItem(idx)
{
	var thisWindow = $.window.getSelectedWindow();
	
	var itemCategory = $("#itemCategory").val();
	var insurance = $("#insurance").val();
	var limit = $("#limit").val();
	var max_amount = $("#max_amount").val();
	var max_exc_opts = $("#max_exc_opts").slider("value");
	var min_level = $("#level").slider("values",0);
	var max_level = $("#level").slider("values",1);
	var addopt = $("#addopt").slider("value");	
	var skill = $("#skill").val();
	var luck = $("#luck").val();
	var ancient = $("#ancient").val();
	var harmony = $("#harmony").val();
	var opt380 = $("#opt380").val();	
	var max_socket = $("#max_socket").slider("value");
	var socket_level = $("#socket_level").slider("value");
	var socket_empty = $("#socket_empty").val();
	var currency = $("#currency").val();
	var base_price = $("#base_price").val();
	var status = $("#status").val();
	var cancellable = $("#cancellable").val();
	var vip_item = $("#vip_item").val();
	
	$.post("./Controllers/WebShop.php?action=saveEditedItem", { idx:idx, itemCategory:itemCategory, insurance:insurance, limit:limit, max_amount:max_amount, max_exc_opts:max_exc_opts, min_level:min_level, max_level:max_level, addopt:addopt, skill:skill, luck:luck, ancient:ancient, harmony:harmony, opt380:opt380, max_socket:max_socket, socket_level:socket_level, socket_empty:socket_empty, currency:currency, base_price:base_price, status:status, cancellable:cancellable, vip_item:vip_item }, function(data) {
		$.Growl.show(data);
		thisWindow.close();
		LoadItemsList(itemCategory);
	});	
}

function LoadItemsList(category)
{
	$("#itemList").empty();
	$("#disabledItemList").empty();
	$("#itemList").load("./Controllers/WebShop.php?action=listItems&category="+category+"&status=1");
	$("#disabledItemList").load("./Controllers/WebShop.php?action=listItems&category="+category+"&status=0");
}

function EditWebshopItem(idx)
{
	if($.window.getWindow('WebShopItemConfig'))
	{
		var opened = $.window.getWindow('WebShopItemConfig');
		if(opened.isMinimized) opened.restore();
		opened.select();
	}
	else
	{
		var HDWindow = OpenWindow("WebShop Item Config", "./Controllers/WebShop.php?action=itemEditForm&idx="+idx, 450, 600);
		HDWindow.setWindowId('WebShopItemConfig');
	}
}

function DisableWebshopItem(idx, category)
{
	$.post("./Controllers/WebShop.php?action=disableItem", { idx:idx }, function(data) {
		LoadItemsList(category);		
	});
}

function EnableWebshopItem(idx, category)
{
	$.post("./Controllers/WebShop.php?action=enableItem", { idx:idx }, function(data) {
		LoadItemsList(category);		
	});
}

function DeleteWebshopItem(idx, category)
{
	if(confirm("Do you confirm?"))
	{
		$.post("./Controllers/WebShop.php?action=deleteItem", { idx:idx }, function(data) {
			LoadItemsList(category);		
		});
	}
}

function SavePack()
{
	var thisWindow = $.window.getSelectedWindow();
	
	var insurance = $("#insurance").val();
	var cancellable = $("#cancellable").val();
	var vip_item = $("#vip_item").val();
	var limit = $("#limit").val();
	var currency = $("#currency").val();
	var base_price = $("#base_price").val();
	var pack_name = $("#pack_name").val();
	var category_idx = $("#packCategory").val();
	
	$.post("./Controllers/WebShop.php?action=savePack", { category_idx:category_idx, insurance:insurance, limit:limit, currency:currency, base_price:base_price, pack_name:pack_name, cancellable:cancellable, vip_item:vip_item  }, function(data) {
		$.Growl.show(data);
		thisWindow.close();
		WebShop('managePacks','Packs',950,500)
	});	
}

function LoadPacksList(category)
{
	$("#packList").empty();
	$("#disabledPackList").empty();
	$("#packList").load("./Controllers/WebShop.php?action=listPacks&category="+category+"&status=1");
	$("#disabledPackList").load("./Controllers/WebShop.php?action=listPacks&category="+category+"&status=0");
}

function EditWebshopPack(idx)
{
	if($.window.getWindow('WebShopPackConfig'))
	{
		var opened = $.window.getWindow('WebShopPackConfig');
		opened.setUrl("./Controllers/WebShop.php?action=packEditForm&idx="+idx);
		if(opened.isMinimized) opened.restore();
		opened.select();
	}
	else
	{
		var HDWindow = OpenWindow("WebShop Pack Config", "./Controllers/WebShop.php?action=packEditForm&idx="+idx, 400, 400);
		HDWindow.setWindowId('WebShopPackConfig');
	}
}

function DisableWebshopPack(idx, category)
{
	$.post("./Controllers/WebShop.php?action=disablePack", { idx:idx }, function(data) {
		LoadPacksList(category);		
	});
}

function EnableWebshopPack(idx, category)
{
	$.post("./Controllers/WebShop.php?action=enablePack", { idx:idx }, function(data) {
		LoadPacksList(category);		
	});
}

function DeleteWebshopPack(idx, category)
{
	if(confirm("Do you confirm?"))
	{
		$.post("./Controllers/WebShop.php?action=deletePack", { idx:idx }, function(data) {
			LoadPacksList(category);		
		});
	}
}

function SaveEditedPack(idx)
{
	var thisWindow = $.window.getSelectedWindow();
	
	var cancellable = $("#cancellable").val();
	var vip_item = $("#vip_item").val();
	var insurance = $("#insurance").val();
	var limit = $("#limit").val();
	var currency = $("#currency").val();
	var base_price = $("#base_price").val();
	var pack_name = $("#pack_name").val();
	var category_idx = $("#packCategory").val();
	
	$.post("./Controllers/WebShop.php?action=saveEditedPack", { idx:idx, category_idx:category_idx, insurance:insurance, limit:limit, currency:currency, base_price:base_price, pack_name:pack_name, cancellable:cancellable, vip_item:vip_item  }, function(data) {
		$.Growl.show(data);
		thisWindow.close();
		LoadPacksList(category_idx);
	});	
}

function EditPackItemsForm(idx)
{
	WebShop('manageItemsPack&idx='+idx,'Pack Items',800,630);	
}

function PackLoadItems()
{
	var type = $("#itemType").val();
	
	$.post("./Controllers/WebShop.php?action=loadItemsByType", { type:type }, function(data) {
		$("#itemIndex").html(data);
		ProcessPackItemForm();
	});
}

function ProcessPackItemForm()
{
	var type = $("#itemType").val();
	var id   = $("#itemIndex").val();
	
	$("#excellent_opt").html("");
	$("#ancient").html("");
	$("#harmony_opt").html("");
	
	$.post("./Controllers/WebShop.php?action=getExcellentOptions", { type:type,id:id }, function(data) {
		$("#excellent_opt").html(data);
	});
	
	$.post("./Controllers/WebShop.php?action=getAncientName", { type:type,id:id }, function(data) {
		$("#ancient").html('<option value="0">-</option>'+data);
	});
	
	$.post("./Controllers/WebShop.php?action=getHarmonyOpts", { type:type }, function(data) {
		$("#harmony_opt").html('<option value="0">-</option>'+data);
		GetHarmonyLevels();
	});
	
	$.post("./Controllers/WebShop.php?action=getSocketOpts", { type:type }, function(data) {
		$("#socket1").html('<option value="255">No socket</option><option value="254">Empty socket</option>'+data);
		$("#socket2").html('<option value="255">No socket</option><option value="254">Empty socket</option>'+data);
		$("#socket3").html('<option value="255">No socket</option><option value="254">Empty socket</option>'+data);
		$("#socket4").html('<option value="255">No socket</option><option value="254">Empty socket</option>'+data);
		$("#socket5").html('<option value="255">No socket</option><option value="254">Empty socket</option>'+data);
	});
}

function GetHarmonyLevels()
{
	var harmony = $("#harmony_opt").val();
	var type = $("#itemType").val();
	
	if(harmony == 0)
	{
		$("#harmony_lvl").html('<option value="0">-</option>');
		return false;
	}
	
	$.post("./Controllers/WebShop.php?action=getHarmonyLevel", { harmony:harmony,type:type }, function(data) {
		$("#harmony_lvl").html(data);
	});
}

function SaveItemToPack()
{
	var thisWindow = $.window.getSelectedWindow();
	
	var idx = $("#idx").val();
	var itemType = $("#itemType").val();
	var itemIndex = $("#itemIndex").val();
	var level = $("#level").val();
	var addopt = $("#addopt").val();
	var skill = $("#skill").val();
	var luck = $("#luck").val();
	var ancient = $("#ancient").val();
	var harmony_opt = $("#harmony_opt").val();
	var harmony_lvl = $("#harmony_lvl").val();
	var opt380 = $("#opt380").val();
	var socket1 = $("#socket1").val();
	var socket2 = $("#socket2").val();
	var socket3 = $("#socket3").val();
	var socket4 = $("#socket4").val();
	var socket5 = $("#socket5").val();	
	var excopt = new Array();	
	$.each($("input[type=checkbox][name=excopt]:checked"), function() {
		excopt.push($(this).val());
	});
	
	$.post("./Controllers/WebShop.php?action=saveItemToPack", { pack_idx:idx, type:itemType, id:itemIndex, exc_opts:excopt, level:level, addopt:addopt, skill:skill, luck:luck, ancient:ancient, harmony_opt:harmony_opt, harmony_lvl:harmony_lvl, opt380:opt380, socket1:socket1, socket2:socket2, socket3:socket3, socket4:socket4, socket5:socket5 }, function(data) {
		$.Growl.show(data);
		thisWindow.refreshWindow();
	});
}

function DeleteItemPack(idx)
{
	var thisWindow = $.window.getSelectedWindow();
	if(confirm("Do you confirm?"))
	{
		$.post("./Controllers/WebShop.php?action=deleteItemPack", { idx:idx }, function(data) {
			thisWindow.refreshWindow();
		});
	}
}

function SaveNewDiscountCode()
{
	var thisWindow = $.window.getSelectedWindow();
	
	var code = $("#code").val();
	var type = $("#type").val();
	var value = $("#value").val();
	var expireDate = $("#expireDate").val();
	var count = $("#count").val();
	
	$.post("./Controllers/WebShop.php?action=saveNewDiscCode", { code:code, type:type, value:value, expireDate:expireDate, count:count }, function(data) {
		$.Growl.show(data);
		thisWindow.close();
	});
}

function SaveDiscCode(idx)
{
	var code = $("#code"+idx).val();
	var type = $("#type"+idx).val();
	var value = $("#value"+idx).val();
	var expireDate = $("#expireDate"+idx).val();
	var count = $("#count"+idx).val();
	
	$.post("./Controllers/WebShop.php?action=saveDiscCode", { idx:idx, code:code, type:type, value:value, expireDate:expireDate, count:count }, function(data) {
		$.Growl.show(data);
	});
}

function DeleteDiscCode(idx)
{
	var thisWindow = $.window.getSelectedWindow();
	if(confirm("Do you confirm?"))
	{
		$.post("./Controllers/WebShop.php?action=deleteDiscCode", { idx:idx }, function(data) {
			thisWindow.refreshWindow();
		});
	}
}


