function WebService(param)
{
	$.post("./Controllers/WebService.php", { param:param }, function() { alert("Command sent to WebService successfully."); } );
}