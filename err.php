Вот текст ошибки  
 sudo -u ds curl -v https://kp-iod-app-prd.codm.gazprom.loc/local/components/r7/viewer/ajax.php?type=download\&ID=345
*   Trying 10.55.144.251:443...
* Connected to kp-iod-app-prd.codm.gazprom.loc (10.55.144.251) port 443 (#0)
* ALPN: offers h2
* ALPN: offers http/1.1
*  CAfile: /etc/pki/tls/certs/ca-bundle.crt
*  CApath: none
* TLSv1.3 (OUT), TLS handshake, Client hello (1):
* TLSv1.3 (IN), TLS handshake, Server hello (2):
* TLSv1.3 (IN), TLS handshake, Encrypted Extensions (8):
* TLSv1.3 (IN), TLS handshake, Certificate (11):
* TLSv1.3 (OUT), TLS alert, unknown CA (560):
* SSL certificate problem: unable to get local issuer certificate
* Closing connection 0
curl: (60) SSL certificate problem: unable to get local issuer certificate
More details here: https://curl.se/docs/sslcerts.html
curl failed to verify the legitimacy of the server and therefore could not
establish a secure connection to it. To learn more about this situation and
how to fix it, please visit the web page mentioned above.

nodeJS - error downloadFile:url=https://kp-iod-app-prd.codm.gazprom.loc/local/components/r7/viewer/ajax.php?type=download&ID=345;attempt=1;code:UNABLE_TO_VERIFY_LEAF_SIGNATURE;connect:undefined Error: unable to verify the first certificate
