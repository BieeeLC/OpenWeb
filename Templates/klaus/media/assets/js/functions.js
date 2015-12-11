// JavaScript Document
function tabRank(new_tab, new_content) {
	
	document.getElementById('content_1').style.display = 'block';
	document.getElementById('content_2').style.display = 'none';
	document.getElementById('content_3').style.display = 'none';			
	document.getElementById('content_4').style.display = 'none';	
	

	document.getElementById('tab_1').className = 'active';
	document.getElementById('tab_2').className = '';
	document.getElementById('tab_3').className = '';			
	document.getElementById('tab_4').className = '';		

}

function tabRank_2(active, number, tab_prefix, content_prefix) {
	
	for (var i=1; i < number+2; i++) {
	  document.getElementById(content_prefix+i).style.display = 'none';
	  document.getElementById(tab_prefix+i).className = '';
	}
	document.getElementById(content_prefix+active).style.display = 'block';
	document.getElementById(tab_prefix+active).className = 'active';	
	
}