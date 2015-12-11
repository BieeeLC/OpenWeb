function ItemNameClick(the_item){$("#" + the_item + "_desc").slideToggle('slow');return false;}
function ItemNameMouseOut(the_item){return false;}
function ItemNameMouseOver(the_item){return false;}
function ItemDescriptionClick(the_item){$("#" + the_item + "_desc").slideToggle('slow');return false;}
function ItemDescriptionMouseOut(the_item){return false;}
function ItemDescriptionMouseOver(the_item){return false;}
function PackageBuyClick(nome_do_pacote,valor_do_pacote,creditos,id_do_pacote,SiteFolder){if(parseInt(creditos) < parseInt(valor_do_pacote)){alert('Você não tem créditos para comprar este pacote.');return false;}var texto = 'Por favor, confirme a compra:\nPacote: '+nome_do_pacote+'\nValor: R$'+valor_do_pacote+',00';if(confirm(texto)){LoadContent('/'+SiteFolder+'?c=CreditShop/'+id_do_pacote);}}

function LoadContent(theLink)
{
	$("#FerrareziMUWebContentDiv").animate({opacity: 0.1},500,function() {
		$("#FerrareziMUWebContentDiv").load(theLink, { ajaxed:'true' }, function() {
			$("#FerrareziMUWebContentDiv").animate({opacity: 1.0},200);
			GetCufonWorking();
		});
	});	
}

$(function()
{
	$("a, area").live("click",function(event)
	{
		var theLink = $(this).attr("href");
		var theTarget = $(this).attr("target");
		
		if(theLink != "/" && theLink != "" && theLink != "/index.php?logout" && theLink != "javascript:;" && theLink.indexOf("https:///") == -1 && theLink.indexOf("http://") == -1 && theLink.indexOf("ftp:///") == -1 && theLink.indexOf("#") == -1 && theLink.indexOf("mailto:") == -1 && theLink.indexOf("News/index.html") == -1 && theTarget != "_blank" )
		{
			event.preventDefault();
			LoadContent(theLink);
		}
	});
	
	$("form").live("submit", function(event) {
		
		if($(this).attr("name") == "login_pn")
			return;
			
		if($(this).attr("action").indexOf("http") != -1)
			return;
		
		event.preventDefault();
		
		$("#FerrareziMUWebContentDiv").animate({opacity: 0.1},500);
		$(this).ajaxSubmit(
		{
			data: { ajaxed:'true' },
			target: '#FerrareziMUWebContentDiv',
			resetForm: false,
			success: function() { 
				$("#FerrareziMUWebContentDiv").animate({opacity: 1.0},200);
				GetCufonWorking();
			}
		});
	});
});

function LoadRanking(theLink){LoadContent(theLink);}

function GetCufonWorking()
{
	Cufon.replace('.SideBoxTop', {fontFamily: 'Diavlo',textShadow:'1px 1px #000000',color: '-linear-gradient(#FBCE8C, #FF9900)',fontSize: '18px',fontWeight: '900'});
	Cufon.replace('.MenuRapidoButton', {fontFamily: 'Trajan',textShadow:'1px 1px #000000',color: '-linear-gradient(#FFFFFF, #EEEEEE)',fontSize: '18px',fontWeight: 'bold'});
	Cufon.replace('.osMelhoresButton', {fontFamily: 'Trajan',textShadow:'1px 1px #000000',color: '-linear-gradient(#FFFFFF, #EEEEEE)',fontSize: '15px',fontWeight: 'bold'});	
	Cufon.replace('.TopDescription', {fontFamily: 'Diavlo',textShadow:'1px 1px #000000',color: '-linear-gradient(#FBCE8C, #FF9900)',fontSize: '12px',fontWeight: 'bold'});
	Cufon.replace('#CastleNextDate,#CastleNextTime', {fontFamily: 'Trajan',textShadow:'1px 1px #000000',color: '-linear-gradient(#CCFF99, #CCCC66)',fontSize: '13px',fontWeight: 'bold'});
	Cufon.replace('h1', {fontFamily: 'Diavlo',textShadow:'1px 1px #000000',color: '-linear-gradient(#FBCE8C, #FF9900)',fontSize: '28px',fontWeight: 'bold'});
	Cufon.replace('h3', {fontFamily: 'Diavlo',textShadow:'0 0 0.1em #FF6136,0 0 0.1em #FF6136,0 0 0.1em #FF6136',color: '-linear-gradient(#FFFFFF, #EEEEEE)',fontSize: '28px',fontWeight: 'bold'});
}