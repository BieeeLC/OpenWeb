function HelpDesk(action,title)
{
	if($.window.getWindow('HelpDeskList'+action))
	{
		var opened = $.window.getWindow('HelpDeskList'+action);
		if(opened.isMinimized) opened.restore();
		opened.select();
		opened.refreshWindow();
	}
	else
	{
		var HDWindow = OpenWindow(title + " - HelpDesk", "./Controllers/HelpDesk.php?action="+action+"", 700, 500);
		HDWindow.setWindowId('HelpDeskList'+action);
	}
}

function OpenTicket(id)
{
	OpenWindow("Ticket #"+id+" - HelpDesk", "./Controllers/HelpDesk.php?action=viewTicket&id="+id+"", 600, 500);
}

function HelpDeskButtonClick(button)
{
	$("#HelpDeskMessageBox").val($("#HelpDeskMessageBox").val() + $(button).val());
}

function HelpDeskBlockUser(memb___id, msg)
{
	$.post("./Controllers/HelpDesk.php?action=userBlock", {memb___id: memb___id, action: 1}, function(){
		$.Growl.show(msg);
		var myWindow = $.window.getSelectedWindow()
		myWindow.setUrl(myWindow.getUrl());
	});
}

function HelpDeskUnBlockUser(memb___id, msg)
{
	var myWindow = $.window.getSelectedWindow();
	$.post("./Controllers/HelpDesk.php?action=userBlock", {memb___id: memb___id, action: 0}, function(){
		$.Growl.show(msg);
		myWindow.refreshWindow();
	});
	
}

function HelpDeskAddMessage(Close)
{
	var ticketId = $("#ticketId").val();
	var Admin = $("#Admin").val();
	var TicketStatus = $("#TicketStatus").val();
	var HelpDeskMessageBox = $("#HelpDeskMessageBox").val();
	var memb___id = $("#memb___id").val();
	
	var thisWindow = $.window.getSelectedWindow();
	var myWindow;
	
	$.post("./Controllers/HelpDesk.php?action=addMessage",{ ticketId:ticketId, Admin:Admin, TicketStatus:TicketStatus, HelpDeskMessageBox:HelpDeskMessageBox, memb___id:memb___id }, function(data) {
		
		if($.window.getWindow('HelpDeskListwaiting'))
		{
			myWindow = $.window.getWindow('HelpDeskListwaiting');
			myWindow.refreshWindow();
		}
		else if($.window.getWindow('HelpDeskListfind'))
		{
			Find();
		}
		else if($.window.getWindow('HelpDeskListanswered')) 
		{
			myWindow = $.window.getWindow('HelpDeskListanswered');
			myWindow.refreshWindow();
		}
		
		if(Close == false)
			thisWindow.refreshWindow();
		else
			thisWindow.close();		
	});
}

function DeletePost(idx,message)
{
	var thisWindow = $.window.getSelectedWindow();
	if(confirm("Do you confirm?"))
	{
		$.post("./Controllers/HelpDesk.php?action=delMessage", { idx:idx }, function(data) {
			$.Growl.show(message);			
			thisWindow.refreshWindow();
		});
	}
}

function Find()
{
	var TicketId = $("#TicketId").val();
	var memb___id = $("#memb___id").val();
	var starting_date = $("#starting_date").val();
	var ending_date = $("#ending_date").val();
	var ip = $("#ip").val();
		
	$.post("./Controllers/HelpDesk.php?action=results", { TicketId:TicketId, memb___id:memb___id, starting_date:starting_date, ending_date:ending_date, ip:ip }, function(data) {
		$("#SearchResults").html(data);
	});
}

function AddHelpDeskButton()
{
	var NewButtonTitle = $("#NewButtonTitle").val();
	var NewButtonText = $("#NewButtonText").val();
	var NewButtonOwner = $("#NewButtonOwner").val();
	
	var thisWindow = $.window.getSelectedWindow();

	$.post("./Controllers/HelpDesk.php?action=addNewButton", { NewButtonTitle:NewButtonTitle, NewButtonText:NewButtonText, NewButtonOwner:NewButtonOwner }, function(data) {
		$.Growl.show(data);
		thisWindow.refreshWindow();
	});
}

function SaveHelpDeskButton(buttonId)
{
	var Title = $("#Title"+buttonId).val();
	var Text = $("#Text"+buttonId).val();

	var thisWindow = $.window.getSelectedWindow();

	$.post("./Controllers/HelpDesk.php?action=editButton", { Title:Title, Text:Text, buttonId:buttonId }, function(data) {
		$.Growl.show(data);
		thisWindow.refreshWindow();
	});
}

function DeleteHelpDeskButton(buttonId)
{
	var thisWindow = $.window.getSelectedWindow();
	$.post("./Controllers/HelpDesk.php?action=deleteButton", { buttonId:buttonId }, function(data) {
		$.Growl.show(data);
		thisWindow.refreshWindow();
	});
}