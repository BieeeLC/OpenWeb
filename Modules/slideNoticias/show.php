<?php
if (!@require("Config/Main.php"))
	die();

include "config.php";

//------------------------------------------------//
//------------------------------------------------//
//------------------------------------------------//
// --  NÃO  TOQUE  NADA  DAQUI  PRA  BAIXO!!!  -- //
//------------------------------------------------//
//------------------------------------------------//
//------------------------------------------------//

$count = 0;
$files = scandir($_SERVER['DOCUMENT_ROOT'] . $PathToImg . "/", 0);
foreach ($files as $key => $value) {
	if (!is_dir($value) && $value != $ActiveImageIcon && $value != $InactiveImageIcon) {
		$parts = explode(".", $value);
		if ($parts[1] != "jpg") {
			unset($files[$key]);
		}
	} else {
		unset($files[$key]);
	}
}
?>
<table>
	<tr><td class="theLinks" align="right">
			<div style="float:right">
				<?php
				$myFiles = "";
				foreach ($files as $key => $value) {
					echo "<img id=\"" . str_replace(".jpg", "", $value) . "\" src=\"$PathToImg/$InactiveImageIcon\" style=\"float:left; cursor:pointer;\" onClick=\"GoTo('$value')\" />";
					$myFiles .= "\"$value\",";
				}
				$myFiles = substr($myFiles, 0, strlen($myFiles) - 1);
				?>
			</div>
		</td></tr>
	<tr><td><div id="image" style="width:250px; height:150px"></div></td></tr>
</table>
<script>
	var interval;
	var current = 0;
	var myFiles = new Array(<?php echo $myFiles; ?>);

	$(function ()
	{
		Cicle();
		interval = setInterval("Cicle()",<?php echo $TimeShift * 1000; ?>);

		$("#image").hover(function () {
			clearInterval(interval);
		},
				function () {
					interval = setInterval("Cicle()",<?php echo $TimeShift * 1000; ?>);
				}
		);
	});

	function Cicle()
	{
		$(".theLinks img").each(function () {
			$(this).attr("src", "<?php echo $PathToImg . "/" . $InactiveImageIcon; ?>");
		});

		var theRightId = myFiles[current].replace(".jpg", "");

		$("#image").animate({opacity: 0.01}, 500, function ()
		{
			$("#image").html("<img src='<?php echo $PathToImg ?>/" + myFiles[current] + "' />");
			$("#" + theRightId).attr("src", "<?php echo $PathToImg . "/" . $ActiveImageIcon; ?>");
			$("#image").animate({opacity: 1.0}, 500);
			if (myFiles.length > (current + 1))
				current++;
			else
				current = 0;
		});
	}

	function GoTo(value)
	{
		for (key in myFiles)
		{
			if (myFiles[key] == value)
			{
				current = key;
				clearInterval(interval);
				Cicle();
				interval = setInterval("Cicle()",<?php echo $TimeShift * 1000; ?>);
			}
		}
	}
</script>