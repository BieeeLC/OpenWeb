function Poll(action,title)
{
	if($.window.getWindow('Poll'+action))
	{
		var opened = $.window.getWindow('Poll'+action);
		if(opened.isMinimized) opened.restore();
		opened.select();
		opened.refreshWindow();
	}
	else
	{
		var HDWindow = OpenWindow(title, "./Controllers/Poll.php?action="+action+"", 750, 550);
		HDWindow.setWindowId('Poll'+action);
	}
}

function PollSaveNew()
{
	var thisWindow = $.window.getSelectedWindow();
	
	var question = $("#pollQuestion").val();
	var expiration = $("#pollExpYear").val() + "-" + $("#pollExpMonth").val() + "-" + $("#pollExpDay").val() + " " + $("#pollExpHour").val() + ":" + $("#pollExpMin").val();
	var accountLevel = $("#pollMinAL").val();
		
	$.post("./Controllers/Poll.php?action=saveNewPoll", { question:question, expiration:expiration, accountLevel:accountLevel  }, function(data)
	{
		$.Growl.show(data);
		thisWindow.close();
	});
}

function PollSaveEdited(id)
{
	var thisWindow = $.window.getSelectedWindow();
	var thatWindow = $.window.getWindow('PollmanagePolls');
	
	var question = $("#pollQuestion").val();
	var expiration = $("#pollExpYear").val() + "-" + $("#pollExpMonth").val() + "-" + $("#pollExpDay").val() + " " + $("#pollExpHour").val() + ":" + $("#pollExpMin").val();
	var accountLevel = $("#pollMinAL").val();
		
	$.post("./Controllers/Poll.php?action=saveEditedPoll", { id:id, question:question, expiration:expiration, accountLevel:accountLevel  }, function(data)
	{
		$.Growl.show(data);
		thisWindow.close();
		thatWindow.refreshWindow();
	});
}

function PollSaveNewAnswer(id)
{
	var thisWindow = $.window.getSelectedWindow();
	
	var answer = $("#answer").val();
	
	$.post("./Controllers/Poll.php?action=saveNewAnswer", { id:id, answer:answer }, function(data)
	{
		$.Growl.show(data);
		thisWindow.refreshWindow();
	});
}

function PollDeleteAnswer(idx)
{
	if(confirm('Do you confirm?'))
	{
		var thisWindow = $.window.getSelectedWindow();
		$.post("./Controllers/Poll.php?action=deleteAnswer", { idx:idx }, function(data)
		{
			$.Growl.show(data);
			thisWindow.refreshWindow();
		});
	}
}

function PollDelete(id)
{
	if(confirm('Do you confirm?'))
	{
		var thisWindow = $.window.getSelectedWindow();
		$.post("./Controllers/Poll.php?action=deletePoll", { id:id }, function(data)
		{
			$.Growl.show(data);
			thisWindow.refreshWindow();
		});
	}
}