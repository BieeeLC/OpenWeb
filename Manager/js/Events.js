function Events(action,title)
{
	if($.window.getWindow('Events'+action))
	{
		var opened = $.window.getWindow('Events'+action);
		if(opened.isMinimized) opened.restore();
		opened.select();
		opened.refreshWindow();
	}
	else
	{
		var HDWindow = OpenWindow(title, "./Controllers/Events.php?action="+action+"", 750, 550);
		HDWindow.setWindowId('Events'+action);
	}
}

function EventNewEvent()
{
	var thisWindow = $.window.getSelectedWindow();
	
	var title = $("#eventTitle").val();
	var description = $("#eventDescription").val();
	var type = $("#eventType").val();
	var winQuantity = $("#eventWinQuantity").val();
	
	var cash1; ($("#eventPrize1").length && $("#eventPrize1").val() != "") ? cash1 = $("#eventPrize1").val() : cash1 = 0;
	var cash2; ($("#eventPrize2").length && $("#eventPrize2").val() != "") ? cash2 = $("#eventPrize2").val() : cash2 = 0;
	var cash3; ($("#eventPrize3").length && $("#eventPrize3").val() != "") ? cash3 = $("#eventPrize3").val() : cash3 = 0;
	var cash4; ($("#eventPrize4").length && $("#eventPrize4").val() != "") ? cash4 = $("#eventPrize4").val() : cash4 = 0;
	var cash5; ($("#eventPrize5").length && $("#eventPrize5").val() != "") ? cash5 = $("#eventPrize5").val() : cash5 = 0;
		
	$.post("./Controllers/Events.php?action=saveNewEvent", { title:title,description:description, type:type, cash1:cash1, cash2:cash2, cash3:cash3, cash4:cash4 ,cash5:cash5, winQuantity:winQuantity }, function(data) {
		$.Growl.show(data);
		thisWindow.close();
	});	
}

function EventEdit(idx)
{
	Events("editEvent&idx="+idx,"Edit event #"+idx)
}

function EventSaveEvent(idx)
{
	var thisWindow = $.window.getSelectedWindow();
	var thatWindow = $.window.getWindow('EventsmanageEvents');
	
	var title = $("#eventTitle").val();
	var description = $("#eventDescription").val();
	var type = $("#eventType").val();
	var winQuantity = $("#eventWinQuantity").val();
	
	var cash1; ($("#eventPrize1").length && $("#eventPrize1").val() != "") ? cash1 = $("#eventPrize1").val() : cash1 = 0;
	var cash2; ($("#eventPrize2").length && $("#eventPrize2").val() != "") ? cash2 = $("#eventPrize2").val() : cash2 = 0;
	var cash3; ($("#eventPrize3").length && $("#eventPrize3").val() != "") ? cash3 = $("#eventPrize3").val() : cash3 = 0;
	var cash4; ($("#eventPrize4").length && $("#eventPrize4").val() != "") ? cash4 = $("#eventPrize4").val() : cash4 = 0;
	var cash5; ($("#eventPrize5").length && $("#eventPrize5").val() != "") ? cash5 = $("#eventPrize5").val() : cash5 = 0;
		
	$.post("./Controllers/Events.php?action=saveEvent", { idx:idx,title:title,description:description, type:type, cash1:cash1, cash2:cash2, cash3:cash3, cash4:cash4 ,cash5:cash5, winQuantity:winQuantity }, function(data) {
		$.Growl.show(data);
		thatWindow.refreshWindow();
		thisWindow.close();
	});	
}

function ScheduleEvent(idx)
{
	Events("scheduleEvent&idx="+idx,"Schedule event #"+idx)
}

function DeleteEvent(idx)
{
	var thisWindow = $.window.getSelectedWindow();
	if(confirm("Do you confirm?"))
	{
		$.post("./Controllers/Events.php?action=deleteEvent", { idx:idx }, function(data) {
			thisWindow.refreshWindow();
		});
	}
}

function EventNewSchedule()
{
	var thisWindow = $.window.getSelectedWindow();
	
	var eventId = $("#event").val();
	var schedType = $("#schedType").val();
	var schedDate = $("#schedDate").val();
	var schedAmount = $("#schedAmount").val();
	var eventPlace = $("#eventPlace").val();
	
	var cash1; ($("#eventPrize1").length && $("#eventPrize1").val() != "") ? cash1 = $("#eventPrize1").val() : cash1 = 0;
	var cash2; ($("#eventPrize2").length && $("#eventPrize2").val() != "") ? cash2 = $("#eventPrize2").val() : cash2 = 0;
	var cash3; ($("#eventPrize3").length && $("#eventPrize3").val() != "") ? cash3 = $("#eventPrize3").val() : cash3 = 0;
	var cash4; ($("#eventPrize4").length && $("#eventPrize4").val() != "") ? cash4 = $("#eventPrize4").val() : cash4 = 0;
	var cash5; ($("#eventPrize5").length && $("#eventPrize5").val() != "") ? cash5 = $("#eventPrize5").val() : cash5 = 0;
	
	$.post("./Controllers/Events.php?action=saveShedule", { eventId:eventId,schedType:schedType,schedDate:schedDate,schedAmount:schedAmount, cash1:cash1, cash2:cash2, cash3:cash3, cash4:cash4 ,cash5:cash5, eventPlace:eventPlace }, function(data) {
		$.Growl.show(data);
		thisWindow.close();
	});	
}

function EventPrize(idx)
{
	var thisWindow = $.window.getSelectedWindow();
	
	var Name = $("#winner_"+idx).val();
	
	$.post("./Controllers/Events.php?action=prizeWinner", { idx:idx,Name:Name }, function(data) {
		$.Growl.show(data);
		thisWindow.refreshWindow();
	});	
}

function CancelScheduled(idx)
{
	var thisWindow = $.window.getSelectedWindow();
	if(confirm("Do you confirm?"))
	{
		$.post("./Controllers/Events.php?action=cancelSchedule", { idx:idx }, function(data) {
			$.Growl.show(data);
			thisWindow.refreshWindow();
		});
	}
}

