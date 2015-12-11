// JavaScript Document

//required Item Functions
function ItemNameClick(the_item)
{
	$("#" + the_item + "_desc").slideToggle('slow');
	return false;
}
function ItemNameMouseOut(the_item)
{
	//$("#" + the_item + "_desc").slideUp('fast');
	return false;
}
function ItemNameMouseOver(the_item)
{
	//$("#" + the_item + "_desc").slideDown('slow');
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


//required Credit Shop function
function PackageBuyClick(nome_do_pacote,valor_do_pacote)
{
	var texto = 'Por favor, confirme a compra:\nPacote: '+nome_do_pacote+'\nValor: R$'+valor_do_pacote+',00';
	if(confirm(texto))
		return true;
	else
		return false;
}