<script>
function GetRankingLoadedOptions()
{
	var type = "{type}";
	var param1 = "{param1}";
	var param2 = "{param2}";
	var param3 = "{param3}";
	
	if(type == "")
		type = "resets";
		
	$("#RankingOptions").load("/{SiteFolder}?c=Rankings/"+type, { } , function () { 
		$("#type").val(type);
		$("#param1").val(param1);
		$("#param2").val(param2);
		$("#param3").val(param3);
	});
}

function GetRankingOptions()
{
	$("#RankingOptions").empty();
	$("#RankingTable").empty();
	var type = $("#type").val();
	$("#RankingOptions").load("/{SiteFolder}?c=Rankings/"+type);
}

function GetRanking()
{
	var type = $("#type").val();
	
	var param1; $("#param1").length ? param1 = $("#param1").val() : param1 = "";
	var param2; $("#param2").length ? param2 = $("#param2").val() : param2 = "";
	var param3; $("#param3").length ? param3 = $("#param3").val() : param3 = "";
	
	LoadRanking("/{SiteFolder}?c=Rankings/"+type+"/"+param1+"/"+param2+"/"+param3);
}
$(function() {
	GetRankingLoadedOptions();
});
</script>
<blockquote><div class="nf_title"><h1 class="custom">Rankings</h1></div>
<p>
	<div style="float:left; width: 90px; text-align:right; vertical-align:middle; margin-right: 5px;">Tipo:</div>
	<div>
	<select id="type" name="type" onChange="GetRankingOptions()">
		<option value="resets">Top Resets</option>
		<option value="level">Level</option>
		<option value="guilds">Top Guilds</option>
		<option value="pk">PK - Hero</option>
		<option value="online">Tempo Online</option>
	</select>
	</div>
	<div id="RankingOptions"></div>
</p>
<hr />
<div id="RankingTable">{ranking}</div>
<hr />
<div align="left">Processado em {ProcessTime} segundo(s)</div>
<p id="ListarTodas" align="center" style="font-weight:bold;"><a href="/{SiteFolder}">[VOLTAR AO INÍCIO]</a></p></blockquote>