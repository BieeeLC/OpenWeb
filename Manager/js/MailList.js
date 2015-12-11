function MailList(action,title)
{
	if($.window.getWindow('MailList'+action))
	{
		var opened = $.window.getWindow('MailList'+action);
		if(opened.isMinimized) opened.restore();
		opened.select();
		opened.refreshWindow();
	}
	else
	{
		var HDWindow = OpenWindow(title, "./Controllers/MailList.php?action="+action+"", 750, 550);
		HDWindow.setWindowId('MailList'+action);
	}
}

function AddMessage()
{
	var MessageText  = $("#MessageText").val();
	var MessageTitle = $("#MessageTitle").val();
	
	$.post("./Controllers/MailList.php?action=AddMessage", { MessageText:MessageText, MessageTitle:MessageTitle }, function(data)
	{
		$.Growl.show(data);
		MailList('manage','Mail List')
	});
}

function EditMessage(idx,title)
{
	OpenWindow(title, "./Controllers/MailList.php?action=EditMessage&idx="+idx, 800, 550);
}

function SaveMessage(idx)
{
	var Text  = $("#Text").val();
	var Title = $("#Title").val();
	
	var myWindow2 = $.window.getWindow('MailListmanage');
	var thisWindow = $.window.getSelectedWindow();
	
	$.post("./Controllers/MailList.php?action=SaveMessage", { Text:Text, Title:Title, idx:idx }, function(data)
	{
		$.Growl.show(data);
		if(myWindow2) myWindow2.refreshWindow();
		thisWindow.close();
	});
}

function DeleteMessage(idx)
{
	var myWindow2 = $.window.getWindow('MailListmanage');
	
	if(confirm("Do you confirm?"))
	{
		$.post("./Controllers/MailList.php?action=DeleteMessage", { idx:idx }, function(data)
		{
			if(myWindow2) myWindow2.refreshWindow();
		});
	}
}

function StartList(idx)
{
	if(confirm("This will reset the mail list. Do you confirm?"))
	{
		$.post("./Controllers/MailList.php?action=StartList", { idx:idx }, function(data)
		{
			$.Growl.show(data);
		});
	}
}

