/**
 * copy this file to config.js
 */
var config = {
	keyPath: '/etc/apache2/ssl/socialhappen.com.key',
	certPath: '/etc/apache2/ssl/socialhappen.com.crt',
	caPath: '/etc/apache2/ssl/PositiveSSLCA.crt',
	http_port: 8080,
	https_port: 8081
}

exports.config = config;