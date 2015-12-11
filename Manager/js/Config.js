function Config(action,title,w,h)
{
	if($.window.getWindow('Config'+action))
	{
		var opened = $.window.getWindow('Config'+action);
		if(opened.isMinimized) opened.restore();
		opened.select();
		opened.refreshWindow();
	}
	else
	{
		var HDWindow = OpenWindow(title, "./Controllers/Config.php?action="+action+"", w, h);
		HDWindow.setWindowId('Config'+action);
	}
}

function ConfigSaveConfig(config)
{
	var configName = new Array();
	var configValue= new Array();
	
	$.each($("input:text, select"), function()
	{
		if($(this).attr('id').indexOf(config) > -1)
		{
			configName.push($(this).attr('id'));
			configValue.push($(this).val());
		}
	});
	
	$.post("./Controllers/Config.php?action=saveConfig", { config:config, names:configName, values:configValue } , function(data) {
		$.Growl.show(data);		
	});	
}













