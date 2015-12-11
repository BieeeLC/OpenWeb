function News(action,title)
{
	if($.window.getWindow('News'+action))
	{
		var opened = $.window.getWindow('News'+action);
		if(opened.isMinimized) opened.restore();
		opened.select();
		opened.refreshWindow();
	}
	else
	{
		var HDWindow = OpenWindow(title, "./Controllers/News.php?action="+action+"", 800, 550);
		HDWindow.setWindowId('News'+action);
	}
}

function AddNew()
{
	var NewText  = $("#NewText").val();
	var NewTitle = $("#NewTitle").val();
	var NewLink  = $("#NewLink").val();
	var Stick = $("input[type=checkbox][name=Stick]:checked").val();
	var thisWindow = $.window.getSelectedWindow();
	$.post("./Controllers/News.php?action=AddNew", { NewText:NewText, NewTitle:NewTitle, NewLink:NewLink, Stick:Stick }, function(data)
	{
		$.Growl.show(data);		
		thisWindow.close();
	});
}

function EditNew(id,title)
{
	OpenWindow(title, "./Controllers/News.php?action=EditNew&id="+id+"", 800, 550);	
}

function SaveNew(id)
{
	var Text  = $("#Text").val();
	var Title = $("#Title").val();
	var Link = $("#Link").val();
	var Stick = $("input[type=checkbox][name=Stick]:checked").val();
	
	var myWindow1 = $.window.getWindow('Newsarchive');
	var myWindow2 = $.window.getWindow('Newsmanage');
	var thisWindow = $.window.getSelectedWindow();
	
	$.post("./Controllers/News.php?action=SaveNew", { Text:Text, Title:Title, Link:Link, Stick:Stick, id:id }, function(data)
	{
		$.Growl.show(data);
		if(myWindow1) myWindow1.refreshWindow();
		if(myWindow2) myWindow2.refreshWindow();		
		thisWindow.close();
	});
}

function ArchiveNew(id)
{
	var myWindow1 = $.window.getWindow('Newsarchive');
	var myWindow2 = $.window.getWindow('Newsmanage');
	
	if(confirm("Do you confirm?"))
	{
		$.post("./Controllers/News.php?action=ArchiveNew", { id:id }, function()
		{
			if(myWindow1) myWindow1.refreshWindow();
			if(myWindow2) myWindow2.refreshWindow();
		});
	}
}

function MoveNew(id, dir)
{
	var thisWindow = $.window.getSelectedWindow();
	$.post("./Controllers/News.php?action=MoveNew", { id:id, dir:dir }, function()
	{
		thisWindow.refreshWindow();
	});
}

function PublishNew(id)
{
	var myWindow1 = $.window.getWindow('Newsarchive');
	var myWindow2 = $.window.getWindow('Newsmanage');
	
	if(confirm("Do you confirm?"))
	{
		$.post("./Controllers/News.php?action=Publish", { id:id }, function()
		{
			if(myWindow1) myWindow1.refreshWindow();
			if(myWindow2) myWindow2.refreshWindow();
		});
	}
}

function DeleteNew(id)
{
	var myWindow1 = $.window.getWindow('Newsarchive');
		
	if(confirm("Do you confirm?"))
	{
		$.post("./Controllers/News.php?action=Delete", { id:id }, function()
		{
			if(myWindow1) myWindow1.refreshWindow();
		});
	}
}