function Reseller(action,title,w,h)
{
	if($.window.getWindow('Reseller'+action))
	{
		var opened = $.window.getWindow('Reseller'+action);
		if(opened.isMinimized) opened.restore();
		opened.select();
		opened.refreshWindow();
	}
	else
	{
		var HDWindow = OpenWindow(title, "./Controllers/Reseller.php?action="+action+"", w, h);
		HDWindow.setWindowId('Reseller'+action);
	}
}

function SaveNewReseller()
{
	var thisWindow = $.window.getSelectedWindow();
	
	var memb___id = $("#Reseller_memb___id").val();
	var name = $("#Reseller_name").val();
	var description = $("#Reseller_description").val();
	var commission = $("#Reseller_commission").val();
	
	$.post("./Controllers/Reseller.php?action=saveNewReseller", { memb___id:memb___id, name:name, description:description, commission:commission }, function(data)
	{
		$.Growl.show(data);
	});
}

function DeleteReseller(idx)
{
	var thisWindow = $.window.getSelectedWindow();
	if(confirm("Do you confirm?"))
	{
		$.post("./Controllers/Reseller.php?action=deleteReseller", { idx:idx }, function() { thisWindow.refreshWindow(); });
	}	
}

function SaveReseller(idx)
{
	var thisWindow = $.window.getSelectedWindow();
	
	var memb___id = $("#Reseller_memb___id"+idx).val();
	var name = $("#Reseller_name"+idx).val();
	var description = $("#Reseller_description"+idx).val();
	var commission = $("#Reseller_commission"+idx).val();
	
	
	$.post("./Controllers/Reseller.php?action=saveReseller", { idx:idx, memb___id:memb___id, name:name, description:description, commission:commission }, function(data) {
		$.Growl.show(data);
		thisWindow.refreshWindow();
	});	
}