function CreditShop(action,title,w,h)
{
	if($.window.getWindow('CreditShop'+action))
	{
		var opened = $.window.getWindow('CreditShop'+action);
		if(opened.isMinimized) opened.restore();
		opened.select();
		opened.refreshWindow();
	}
	else
	{
		var HDWindow = OpenWindow(title, "./Controllers/CreditShop.php?action="+action+"", w, h);
		HDWindow.setWindowId('CreditShop'+action);
	}
}

function SaveNewPack()
{
	var name = $("#name").val();
	var description = $("#description").val();
	var price = $("#price").val();
	
	var thisWindow = $.window.getSelectedWindow();
	
	$.post("./Controllers/CreditShop.php?action=saveNew", { name:name, description:description, price:price }, function(data) {
		$.Growl.show(data);		
		thisWindow.close();
	});	
}

function MovePack(id, dir)
{
	var thisWindow = $.window.getSelectedWindow();
	$.post("./Controllers/CreditShop.php?action=movePack", { id:id, dir:dir }, function()
	{
		thisWindow.refreshWindow();
	});
}

function DisablePack(idx)
{
	var thisWindow = $.window.getSelectedWindow();
	$.post("./Controllers/CreditShop.php?action=disablePack", { idx:idx }, function()
	{
		thisWindow.refreshWindow();
	});
}

function ActivatePack(idx)
{
	var thisWindow = $.window.getSelectedWindow();
	$.post("./Controllers/CreditShop.php?action=activatePack", { idx:idx }, function()
	{
		thisWindow.refreshWindow();
	});
}

function EditPack(title,idx)
{
	var Opened = $.window.getWindow('CreditShopPackEdit');
	if(Opened)
		Opened.close();
		
	var HDWindow = OpenWindow(title + idx, "./Controllers/CreditShop.php?action=editPack&idx="+idx,500,650,630,10);
	HDWindow.setWindowId('CreditShopPackEdit');
}

function SaveCrediShopPack(idx)
{
	var name = $("#name").val();
	var description = $("#description").val();
	var price = $("#price").val();
	var multiply = $("#multiply").val();
	
	var theWindow = $.window.getWindow('CreditShopmanage')
	
	$.post("./Controllers/CreditShop.php?action=savePack", { idx:idx, name:name, description:description, price:price, multiply:multiply }, function(data) {
		$.Growl.show(data);
		theWindow.refreshWindow();
	});	
}

function AddItemToPack(idx)
{
	var vip;  ($("#vip").length) ? vip = $("#vip").val() : vip = "";
	var days; ($("#days").length) ? days = $("#days").val() : days = "";
	
	var cash1; ($("#cash_1").length) ? cash1 = $("#cash_1").val() : cash1 = "";
	var cash2; ($("#cash_2").length) ? cash2 = $("#cash_2").val() : cash2 = "";
	var cash3; ($("#cash_3").length) ? cash3 = $("#cash_3").val() : cash3 = "";
	var cash4; ($("#cash_4").length) ? cash4 = $("#cash_4").val() : cash4 = "";
	var cash5; ($("#cash_5").length) ? cash5 = $("#cash_5").val() : cash5 = "";
	
	var gameNames  = new Array();
	var gameValues = new Array();
	
	$.each($("input:text"), function()
	{
		if($(this).attr('id').indexOf('game_') > -1)
		{
			gameNames.push($(this).attr('id'));
			gameValues.push($(this).val());
		}
	});
	
	var thisWindow = $.window.getSelectedWindow();
	
	$.post("./Controllers/CreditShop.php?action=addItem", { idx:idx, vip:vip, days:days, cash1:cash1, cash2:cash2, cash3:cash3, cash4:cash4, cash5:cash5, gameNames:gameNames, gameValues:gameValues }, function(data) {
		$.Growl.show(data);		
		thisWindow.refreshWindow();		
	});
}

function DeleteItem(idx)
{
	var thisWindow = $.window.getSelectedWindow();
	$.post("./Controllers/CreditShop.php?action=delItem", { idx:idx }, function(data) {
		$.Growl.show(data);
		thisWindow.refreshWindow();	
	});
}

function SaveCurrencies()
{
	var cash1 = $("#cash_1").val();
	var cash2 = $("#cash_2").val();
	var cash3 = $("#cash_3").val();
	var cash4 = $("#cash_4").val();
	var cash5 = $("#cash_5").val();
	
	var thisWindow = $.window.getSelectedWindow();
	
	$.post("./Controllers/CreditShop.php?action=saveCurrencies", { cash1:cash1,cash2:cash2,cash3:cash3,cash4:cash4,cash5:cash5 }, function(data) {
		$.Growl.show(data);		
		thisWindow.close();
	});
}

function SaveNewGameCurrency()
{
	var name = $("#currency_name").val();
	var database = $("#currency_db").val();
	var table = $("#currency_table").val();
	var col = $("#currency_col").val();
	var acc = $("#currency_acc").val();
	var guid = $("#currency_guid").val();
	var onlyoff; ($("input[type=checkbox][id=currency_off]:checked").length) ? onlyoff = "1" : onlyoff = "0";
	
	var thisWindow = $.window.getSelectedWindow();
	
	$.post("./Controllers/CreditShop.php?action=saveNewGameCurrency", { name:name,database:database,table:table,col:col,acc:acc,guid:guid,onlyoff:onlyoff }, function(data) {
		$.Growl.show(data);		
		thisWindow.refreshWindow();
	});
}

function DeleteGameCurrency(idx)
{
	var thisWindow = $.window.getSelectedWindow();
	$.post("./Controllers/CreditShop.php?action=delGameCurrency", { idx:idx }, function(data) {
		$.Growl.show(data);
		thisWindow.refreshWindow();	
	});
}

function CreditShopLogList()
{
	var memb___id = $("#memb___id").val();
	var starting_date = $("#starting_date").val();
	var ending_date = $("#ending_date").val();
	
	$("#SearchResults").html("Loading... please wait.");
	
	$.post("./Controllers/CreditShop.php?action=results", { memb___id:memb___id, starting_date:starting_date, ending_date:ending_date }, function(data) {
		$("#SearchResults").html(data);
	});
}


	