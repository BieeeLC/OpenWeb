function DupeFinder(action,title,w,h,l,t)
{
	if($.window.getWindow('DupeFinder'+action))
	{
		var opened = $.window.getWindow('DupeFinder'+action);
		if(opened.isMinimized) opened.restore();
		opened.select();
		opened.refreshWindow();
	}
	else
	{
		var HDWindow = OpenWindow(title, "./Controllers/DupeFinder.php?action="+action+"", w, h, l, t);
		HDWindow.setWindowId('DupeFinder'+action);
	}
}

function DupeFinderStep1()
{
	var myWindow;
	if(myWindow = $.window.getWindow('ItemDupeFinderStep2'))
		myWindow.close();
		
	if(confirm("The process will start NOW!\nDo you confirm?"))
	{
		DupeFinder("DupeFinderStep1","Step 1",400,600,470);
	}
}

function DupeFinderStep2()
{
	var myWindow;
	if(myWindow = $.window.getWindow('ItemDupeFinderStep1'))
		myWindow.close();
	
	DupeFinder("DupeFinderStep2","Step 2",400,600,470);
}

function DupeFinderStep3()
{
	var myWindow;
	if(myWindow = $.window.getWindow('ItemDupeFinderStep1'))
		myWindow.close();
	if(myWindow = $.window.getWindow('ItemDupeFinderStep2'))
		myWindow.close();
		
	var DeleteDup; ($("input[type=checkbox][id=DeleteDup]:checked").length) ? DeleteDup = "1" : DeleteDup = "0";
	var DeleteAll; ($("input[type=checkbox][id=DeleteAll]:checked").length) ? DeleteAll = "1" : DeleteAll = "0";
	var BlockAccs; ($("input[type=checkbox][id=BlockAccs]:checked").length) ? BlockAccs = "1" : BlockAccs = "0";
	
	DupeFinder("DupeFinderStep3&DeleteDup="+ DeleteDup +"&DeleteAll="+ DeleteAll +"&BlockAccs="+ BlockAccs,"Step 3",400,600,470);
}