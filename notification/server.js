var http = require('http');
var https = require('https');
var HTTP_PORT = 8080;
var HTTPS_PORT = 8081;
var KEY = 'WOW';
var crypto = require('crypto');
var fs = require("fs");

var privateKey = fs.readFileSync('./ssl/key/vhost1.key').toString();
var certificate = fs.readFileSync('./ssl/crt/vhost1.crt').toString();

var options = {
	key : privateKey,
	cert : certificate
};

var httpServer = http.createServer(function (req, res) {
	var path = require('url').parse(req.url, true);
	switch(path.pathname) {
		case '/publish':
			var query = path.query;
			if(query.key && query.key === KEY && query.user_id 
				&& query.notification_amount){
				
					io.sockets.in('user_id_' + query.user_id)
						.emit('newNotificationAmount', query.notification_amount);
						
					if(query.notification_message){
						var notification_message;
						try{
							notification_message = JSON.parse(query.notification_message);
						}catch(e){
							res.writeHead(200, {'Content-Type': 'text/plain'});
					  	res.end(JSON.stringify({
					  		result: 'FAIL',
					  		message: 'cannot parse "notification_message"'
					  	}));
					  	return;
						}
						console.log('send notification to user_id: ' + query.user_id);
						console.log('notification_message: ' + query.notification_message);
						io.sockets.in('user_id_' + query.user_id)
							.emit('newNotificationMessage', notification_message);
					}
					
					
					res.writeHead(200, {'Content-Type': 'text/plain'});
			  	res.end(JSON.stringify({
			  		result: 'OK'
			  	}));
			}
			res.writeHead(200, {'Content-Type': 'text/plain'});
	  	res.end(JSON.stringify({
	  		result: 'FAIL',
	  		message: 'invalid args'
	  	}));
			break;
		default:
	    res.writeHead(200, {'Content-Type': 'text/plain'});
	  	res.end('Y U COME ?\n');
	}
});

var httpsServer = https.createServer(options, function (req, res) {
  res.writeHead(200);
  res.end("hello world\n");
});
httpsServer.listen(HTTPS_PORT);

console.log('Server running at port: ' + HTTP_PORT);

var io = require('socket.io').listen(httpsServer);
io.configure(function () {
  io.set('transports', ['xhr-polling']);
  io.enable('browser client minification');
});

io.sockets.on('connection', function (socket) {
  socket.on('subscribe', function (user_id, session) {
  	console.log(user_id + ' try to subscribe with session: ' + session);
		// verify user's session
		verifyUserSession(user_id, session, function(err, data){
			if(err){
				socket.emit('subscribeResult', { message: 'fail' });
			}else{
				socket.join('user_id_' + user_id);
				socket.emit('subscribeResult', { message: 'success' });
			}
		})
  });
});

function verifyUserSession(user_id, session, callback){
	if(user_id && session){
		// @TODO: hook asking API here
		
		callback(null, true);
	}else{
		callback(null, false);
	}
}
