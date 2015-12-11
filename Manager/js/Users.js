function UserInfo(memb___id)
{
	OpenWindow("User info: "+memb___id+"", "./Controllers/Users.php?action=userInfo&memb___id="+memb___id+"", 600, 500);
}

function CharInfo(char)
{
	OpenWindow("Char info: "+char+"", "./Controllers/Users.php?action=charInfo&char="+char+"", 600, 700);
}

function Users(action,title)
{
	if($.window.getWindow('Users'+action))
	{
		var opened = $.window.getWindow('News'+action);
		if(opened.isMinimized) opened.restore();
		opened.select();
		opened.refreshWindow();
	}
	else
	{
		var HDWindow = OpenWindow(title, "./Controllers/Users.php?action="+action+"", 700, 500);
		HDWindow.setWindowId('Users'+action);
	}
}

function SaveUserData(memb___id)
{
	var memb__pwd;
	($("#memb__pwd")) ? memb__pwd = $("#memb__pwd").val() : memb__pwd = "";
	var fpas_ques = $("#fpas_ques").val();
	var mail_addr = $("#mail_addr").val();
	var fpas_answ = $("#fpas_answ").val();
	var bloc_code = $("#bloc_code").val();
	var sno__numb = $("#sno__numb").val();
	var mail_chek = $("#mail_chek").val();
	var credits = $("#credits").val();
	
	$.post("./Controllers/Users.php?action=SaveUser", { memb___id:memb___id, memb__pwd:memb__pwd, fpas_ques:fpas_ques, mail_addr:mail_addr, fpas_answ:fpas_answ, bloc_code:bloc_code, sno__numb:sno__numb, mail_chek:mail_chek, credits:credits },function(data){
		$.Growl.show(data);
	});
}

function SaveServerData(memb___id)
{
	var VipId = $("#VipLevel").val();
	var DueDay = $("#DueDay").val();
	var DueMonth = $("#DueMonth").val();
	var DueYear = $("#DueYear").val();
	
	var VipItem = $("#VipItem").val();
	var ItemDueDay = $("#ItemDueDay").val();
	var ItemDueMonth = $("#ItemDueMonth").val();
	var ItemDueYear = $("#ItemDueYear").val();
	
	var Credit1; ($("#Credit_1").length) ? Credit1 = $("#Credit_1").val() : Credit1 = "";
	var Credit2; ($("#Credit_2").length) ? Credit2 = $("#Credit_2").val() : Credit2 = "";
	var Credit3; ($("#Credit_3").length) ? Credit3 = $("#Credit_3").val() : Credit3 = "";
	var Credit4; ($("#Credit_4").length) ? Credit4 = $("#Credit_4").val() : Credit4 = "";
	var Credit5; ($("#Credit_5").length) ? Credit5 = $("#Credit_5").val() : Credit5 = "";
	
	var GameCredit1; ($("#GameCredit_1").length) ? GameCredit1 = $("#GameCredit_1").val() : GameCredit1 = "";
	var GameCredit2; ($("#GameCredit_2").length) ? GameCredit2 = $("#GameCredit_2").val() : GameCredit2 = "";
	var GameCredit3; ($("#GameCredit_3").length) ? GameCredit3 = $("#GameCredit_3").val() : GameCredit3 = "";
	var GameCredit4; ($("#GameCredit_4").length) ? GameCredit4 = $("#GameCredit_4").val() : GameCredit4 = "";
	var GameCredit5; ($("#GameCredit_5").length) ? GameCredit5 = $("#GameCredit_5").val() : GameCredit5 = "";	
	
	$.post("./Controllers/Users.php?action=SaveServerData", { memb___id:memb___id, VipId:VipId, DueDay:DueDay, DueMonth:DueMonth, DueYear:DueYear, VipItem:VipItem, ItemDueDay:ItemDueDay, ItemDueMonth:ItemDueMonth, ItemDueYear:ItemDueYear, Credit1:Credit1, Credit2:Credit2, Credit3:Credit3, Credit4:Credit4, Credit5:Credit5, GameCredit1:GameCredit1, GameCredit2:GameCredit2, GameCredit3:GameCredit3, GameCredit4:GameCredit4, GameCredit5:GameCredit5  }, function(data) {
		$.Growl.show(data);
	});
}

function RenameCharacter(memb___id,oldName)
{
	var thisWindow = $.window.getSelectedWindow();
	
	var newName = $("#Name").val();
	
	if(oldName == newName)
	{
		$.Growl.show('No changes');
		return false;
	}
	
	$.post("./Controllers/Users.php?action=RenameCharacter", { memb___id:memb___id, oldName:oldName, newName:newName }, function(data) {
		$.Growl.show(data);
		if(data.indexOf('#SCCD') > -1)
		{
			thisWindow.close();
		}
	});
}

function SaveCharData(acc,char)
{
	var Class = $("#Class").val();
	var cLevel = $("#cLevel").val();
	var Experience = $("#Experience").val();
	var Resets = $("#Resets").val();
	var MasterResets = $("#MasterResets").val();
	var LevelUpPoint = $("#LevelUpPoint").val();
	var Strength = $("#Strength").val();
	var Dexterity = $("#Dexterity").val();
	var Vitality = $("#Vitality").val();
	var Energy = $("#Energy").val();
	var Leadership = $("#Leadership").val();
	
	var CtlCode = $("#CtlCode").val();
	var Money = $("#Money").val();
	var PkLevel = $("#PkLevel").val();
	var PkCount = $("#PkCount").val();
	var PkTime = $("#PkTime").val();
	
	var MasterLevel;	    ($("#MasterLevel").length) ? MasterLevel = $("#MasterLevel").val() : MasterLevel = "";
	var MasterPoint;		($("#MasterPoint").length) ? MasterPoint = $("#MasterPoint").val() : MasterPoint = "";
	var MasterExperience;	($("#MasterExperience").length) ? MasterExperience = $("#MasterExperience").val() : MasterExperience = "";
	//var ML_NEXTEXP;		($("#ML_NEXTEXP").length) ? ML_NEXTEXP = $("#ML_NEXTEXP").val() : ML_NEXTEXP = "";
	var ExpandedInventory;  ($("#ExpandedInventory").length) ? ExpandedInventory = $("#ExpandedInventory").val() : ExpandedInventory = "x";
	
	$.post("./Controllers/Users.php?action=SaveChar", { memb___id:acc, Name:char, Class:Class, cLevel:cLevel, Experience:Experience, Resets:Resets, MasterResets:MasterResets, LevelUpPoint:LevelUpPoint, Strength:Strength, Dexterity:Dexterity, Vitality:Vitality, Energy:Energy,	Leadership:Leadership, CtlCode:CtlCode, Money:Money, PkLevel:PkLevel, PkCount:PkCount, PkTime:PkTime, MasterLevel:MasterLevel, MasterPoint:MasterPoint, MasterExperience:MasterExperience, ExpandedInventory:ExpandedInventory },function(data){
		$.Growl.show(data);
	});
}

function SaveManagers()
{
	var username = new Array();
	var realname = new Array();
	var password = new Array();
	var userlevel = new Array();
	
	$.each($("input:text"), function() {
		if($(this).attr('id') == "realname")
			realname.push($(this).val());
		if($(this).attr('id') == "username")
			username.push($(this).val());
		if($(this).attr('id') == "password")
			password.push($(this).val());
	});
	
	$.each($("select"), function() {
		userlevel.push($(this).val());
	});
	
	var thisWindow = $.window.getSelectedWindow();
	
	$.post("./Controllers/Users.php?action=saveManagers", { realname:realname,username:username,password:password,userlevel:userlevel }, function(data) {
		$.Growl.show(data);		
		thisWindow.refreshWindow();
	});
	
}

function AddManager()
{
	var appendHTML = "<tr><td><input type=\"text\" name=\"username\" id=\"username\" maxlength=\"20\" value=\"\" /></td><td><input type=\"text\" name=\"password\" id=\"password\" maxlength=\"20\" value=\"\" /></td><td><input type=\"text\" name=\"realname\" id=\"realname\" maxlength=\"50\" value=\"\" /></td><td><select name=\"userlevel\" id=\"userlevel\">";
	for(var i=0; i < 10; i++)
	{
		appendHTML = appendHTML + "<option value=\"" + i + "\">" + i + "</option>";
	}
	appendHTML = appendHTML + "</select></td></tr>";
	
	$("#UsersManagersListTable").append(appendHTML);
	setTimeout(Layout, 100);
}

function UsersOpenMessageForm(memb___id)
{
	var HDWindow = OpenWindow('Message to '+memb___id, "./Controllers/Users.php?action=MessageForm&memb___id="+memb___id, 400, 400);
	HDWindow.setWindowId('UsersMessageForm');
}

function UsersSendMessage(memb___id)
{
	var thisWindow = $.window.getSelectedWindow();
	var title = $("#UsersMessageSubject").val();
	var text  = $("#UsersMessageText").val();
	var type = $("#UsersSendMessageType").val();
	
	$.post("./Controllers/Users.php?action=SendMessage", { memb___id:memb___id, title:title, text:text, type:type  }, function(data) {
		$.Growl.show(data);		
		thisWindow.close();
	});
}

function DisconnectFromGame(memb___id)
{
	var thisWindow = $.window.getSelectedWindow();
	$.post("./Controllers/Users.php?action=DisconnectFromGame", { memb___id:memb___id  }, function(data) {
		window.setTimeout(function () { thisWindow.refreshWindow() },800);
	});
	
}

function UsersOpenDeleteForm(memb___id)
{
	var HDWindow = OpenWindow('Delete account: '+memb___id, "./Controllers/Users.php?action=DeleteAccForm&memb___id="+memb___id, 600, 600);
	HDWindow.setWindowId('UsersDeleteAccForm');
}

function UserDelete(memb___id)
{
	var thisWindow = $.window.getSelectedWindow();
	
	var deleteOptions = new Array();
	
	$.each($("input[type=checkbox]:checked"), function() {
		deleteOptions.push($(this).attr('id'));
	});	
	
	$.post("./Controllers/Users.php?action=DeleteAccount", { memb___id:memb___id, data:deleteOptions  }, function(data) {
		alert(data);
		thisWindow.close();
	});	
}

function RenameCharLog(memb___id)
{
	OpenWindow("Rename Log: "+memb___id+"", "./Controllers/Users.php?action=RenameLog&memb___id="+memb___id+"", 300, 400);
}