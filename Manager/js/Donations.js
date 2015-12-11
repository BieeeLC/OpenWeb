function Donations(action,title)
{
	if($.window.getWindow('Donations'+action))
	{
		var opened = $.window.getWindow('Donations'+action);
		if(opened.isMinimized) opened.restore();
		opened.select();
		opened.refreshWindow();
	}
	else
	{
		var HDWindow = OpenWindow(title, "./Controllers/Donations.php?action="+action+"", 750, 550);
		HDWindow.setWindowId('Donations'+action);
	}
}

function OpenConfirmation(idx)
{
	OpenWindow("Confirmation #"+idx+" - Donations", "./Controllers/Donations.php?action=viewConfirmation&idx="+idx+"", 600, 500);
}

function ConfirmDonation(idx)
{
	var thisWindow = $.window.getSelectedWindow();
	var myWindow = $.window.getWindow('Donationsconfirmations');
		
	$.post("./Controllers/Donations.php?action=confirmDonation", { idx:idx }, function(data)
	{
		$.Growl.show(data);
		myWindow.refreshWindow();
		thisWindow.close();
	});
}

function InvalidDonation(idx)
{
	var message = $("#returnMessage").val();	
	var thisWindow = $.window.getSelectedWindow();
	var myWindow = $.window.getWindow('Donationsconfirmations');
	$.post("./Controllers/Donations.php?action=cancelConfirmation", { idx:idx, message:message }, function(data)
	{
		$.Growl.show(data);
		myWindow.refreshWindow();
		thisWindow.close();
	});
}

function TypeInvalidMessage(message)
{
	$("#returnMessage").val(message);
}

function AddNewBank()
{
	var bank_name = $("#bank_name").val();
	var thisWindow = $.window.getSelectedWindow();
	$.post("./Controllers/Donations.php?action=addBank", {bank_name:bank_name }, function(data) {
		thisWindow.refreshWindow();
		$.Growl.show(data);
	});
}

function GetDepositWays()
{
	var bank = $("#banks").val();
	
	if(bank == 0)
		$("#depositWays").text("");
	else
		$("#depositWays").load("./Controllers/Donations.php?action=depositWays&bank="+bank);
}

function AddNewDepositWay()
{
	var way = $("#way").val();
	var bank = $("#banks").val();
	$.post("./Controllers/Donations.php?action=addNewWay", { way:way, bank:bank }, function(data) {
		$.Growl.show(data);
		GetDepositWays();
	});
}

function ShowWayData()
{
	var way = $("#WaysList").val();
	$("#WayData").load("./Controllers/Donations.php?action=getWayData&way="+way);	
}

function DeleteDepositWay(way)
{
	if(confirm("Do you confirm?"))
	{
		$.post("./Controllers/Donations.php?action=deleteWay", { way:way }, function(data) {
			$.Growl.show(data);
			GetDepositWays();
		});
	}
}

function AddNewData()
{
	var way = $("#WaysList").val();
	var dataName = $("#dataName").val();
	var dataFormat = $("#dataFormat").val();
	
	$.post("./Controllers/Donations.php?action=addWayData", { way:way, dataName:dataName, dataFormat:dataFormat } , function(data) {
		$.Growl.show(data);
		ShowWayData();
	});
}

function ShowDeleteData(message)
{
	var wayData = $("#dataList").val();
	if(confirm(message))
	{
		$.post("./Controllers/Donations.php?action=deleteWayData", { wayData:wayData }, function(data) {
			$.Growl.show(data);
			ShowWayData();
		});
	}
}

function DeleteBank(bank,message)
{
	var thisWindow = $.window.getSelectedWindow();
	if(confirm(message))
	{
		$.post("./Controllers/Donations.php?action=deleteBank", { bank:bank }, function(data) {
			$.Growl.show(data);
			thisWindow.refreshWindow();
		});
	}
}