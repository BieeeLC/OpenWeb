function GuideDB(action,title,w,h)
{
	if($.window.getWindow('GuideDB'+action))
	{
		var opened = $.window.getWindow('GuideDB'+action);
		if(opened.isMinimized) opened.restore();
		opened.select();
		opened.refreshWindow();
	}
	else
	{
		var HDWindow = OpenWindow(title, "./Controllers/GuideDB.php?action="+action+"", w, h);
		HDWindow.setWindowId('GuideDB'+action);
	}
}

function GuideDBAddCategory()
{
	var category = $("#newCategory").val();
	var mainCategory = $("#mainCategory").val();
	
	var thisWindow = $.window.getSelectedWindow();
	
	$.post("./Controllers/GuideDB.php?action=addCategory", { category:category, mainCategory:mainCategory }, function(data) {
		thisWindow.refreshWindow();
	});
}

function GuideDBMoveCategory(id, dir)
{
	var thisWindow = $.window.getSelectedWindow();
	$.post("./Controllers/GuideDB.php?action=moveCategory", { id:id, dir:dir }, function()
	{
		thisWindow.refreshWindow();
	});
}

function GuideDBDeleteCategory(idx)
{
	var thisWindow = $.window.getSelectedWindow();
	if(confirm("Do you confirm?"))
	{
		$.post("./Controllers/GuideDB.php?action=deleteCategory", { idx:idx }, function()
		{
			thisWindow.refreshWindow();
		});
	}
}

function GuideDBSaveCategory(idx)
{
	var thisWindow = $.window.getSelectedWindow();
	
	var category = $("#category"+idx).val();
	var mainCategory = $("#mainCategory"+idx).val();
	
	$.post("./Controllers/GuideDB.php?action=saveCategory", { category:category, mainCategory:mainCategory, idx:idx }, function(data) {
		$.Growl.show(data);
		thisWindow.refreshWindow();
	});
}

function GuideDBAddNew()
{
	var thisWindow = $.window.getSelectedWindow();
	
	var category = $("#guideCategory").val();
	var title = $("#guideTitle").val();
	var text = $("#guideText").val();
	
	$.post("./Controllers/GuideDB.php?action=saveNewGuide", { category:category, title:title, text:text }, function(data) {
		$.Growl.show(data);
		thisWindow.close();
	});
}

function GuideEdit(idx)
{
	OpenWindow("Edit Tutorial", "./Controllers/GuideDB.php?action=edit&idx="+idx+"", 777, 600);
}

function GuideDBSave(idx)
{
	var thisWindow = $.window.getSelectedWindow();
	
	var category = $("#guideCategory").val();
	var title = $("#guideTitle").val();
	var text = $("#guideText").val();
	
	$.post("./Controllers/GuideDB.php?action=saveGuide", { category:category, title:title, text:text, idx:idx }, function(data) {
		$.Growl.show(data);
		thisWindow.close();
	});
}

function GuideDelete(idx)
{
	var thisWindow = $.window.getSelectedWindow();
	
	if(confirm("Do you confirm?"))
	{
		$.post("./Controllers/GuideDB.php?action=deleteGuide", { idx:idx }, function(data)
		{
			$.Growl.show(data);
			thisWindow.refreshWindow();
		});
	}
}