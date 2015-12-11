// JavaScript Document
function tabNews(new_tb, new_news) {
	
	document.getElementById('news_1').style.display = 'block';
	document.getElementById('news_2').style.display = 'none';
	document.getElementById('news_3').style.display = 'none';			
	document.getElementById('news_4').style.display = 'none';	
	

	document.getElementById('tb_1').className = 'active';
	document.getElementById('tb_2').className = '';
	document.getElementById('tb_3').className = '';			
	document.getElementById('tb_4').className = '';		

}

function tabNews_2(active, number, tb_prefix, news_prefix) {
	
	for (var i=1; i < number+2; i++) {
	  document.getElementById(news_prefix+i).style.display = 'none';
	  document.getElementById(tb_prefix+i).className = '';
	}
	document.getElementById(news_prefix+active).style.display = 'block';
	document.getElementById(tb_prefix+active).className = 'active';	
	
}