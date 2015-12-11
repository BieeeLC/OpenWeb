$(function()
{
    //Ajax Loading Engine
	$(document).ajaxStart(ShowLoading).ajaxStop(HideLoading);
	
	//Função para fechar no ESC
	$(document).keyup(function(e) {
		if(e.keyCode == 27) {
	  		$.window.getSelectedWindow().close();
		}
	});
	
	//Carregar layout
	Layout();

});

function ShowLoading()
{
	$.blockUI({ 
		message: "<img src='img/wait.gif'>", 
		fadeIn: 500, 
		fadeOut: 500, 
		showOverlay: false, 
		centerY: false,
		css: { 
			width: '64px', 
			top: '25px', 
			left: '', 
			right: '5px', 
			border: 'none', 
			padding: '1px', 
			backgroundColor: 'none', 
			'-webkit-border-radius': '10px', 
			'-moz-border-radius': '10px', 
			//opacity: .9, 
			color: '#fff' 
		} 
	}); 
}

function Layout()
{
	var altura = $(window).height() - 65;
	var largura = $(window).width() - 4;
	
	$("#container").css("height", altura+"px");
	$("#container").css("width", largura+"px");
	
	$( "input:text, input:password, textarea" ).addClass("text ui-widget-content ui-corner-all");
	$( "input:text, input:password, textarea" ).focusin(function() { $(this).toggleClass("ui-state-focus"); });
	$( "input:text, input:password, textarea" ).focusout(function() { $(this).toggleClass("ui-state-focus"); });
	$( "fieldset" ).addClass("ui-widget-content");
	$( "legend" ).addClass("ui-widget-header ui-corner-all");
	$( "input:checkbox" ).addClass("");

	if($( "input:button" ))	$( "input:button" ).button();
	
	$('div .ui-state-default').hover(
		function() { $(this).addClass('ui-state-hover'); }, function() { $(this).removeClass('ui-state-hover'); }
	);
}

function HideLoading()
{
	$.unblockUI();
	setTimeout(Layout, 100);
}


//Itens
function ItemNameClick(the_item)
{
	$("#" + the_item + "_desc").slideToggle('slow');
	return false;
}
function ItemNameMouseOut(the_item)
{
	return false;
}
function ItemNameMouseOver(the_item)
{
	return false;
}
function ItemDescriptionClick(the_item)
{
	$("#" + the_item + "_desc").slideToggle('slow');
	return false;
}
function ItemDescriptionMouseOut(the_item)
{
	return false;
}
function ItemDescriptionMouseOver(the_item)
{
	return false;
}

function OperateInput(inputId,actionType,incDec,maxMin)
{
	if(actionType == 'min' || actionType == 'max')
	{
		$("#" + inputId).val(maxMin);
	}
	
	if(actionType == 'down')
	{
		var newValue = parseInt( parseInt($("#" + inputId).val()) - parseInt(incDec) );
		if(newValue < maxMin)
			newValue = maxMin;
		$("#" + inputId).val(newValue);
	}
	
	if(actionType == 'up')
	{
		var newValue = parseInt( parseInt($("#" + inputId).val()) + parseInt(incDec) );
		if(newValue > maxMin)
			newValue = maxMin;
		$("#" + inputId).val(newValue);
	}
}




