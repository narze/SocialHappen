$(function(){
	Date.createFromMysql = function(mysql_string){ 
	   if(typeof mysql_string === 'string')
	   {
		  var t = mysql_string.split(/[- :]/);
		  return new Date(t[0], t[1] - 1, t[2], t[3] || 0, t[4] || 0, t[5] || 0);          
	   }
	   return null;   
	}
});