curl -v 'https://kp-gp-app1-prd.codm.gazprom.loc/local/components/r7/viewwer/ajax.php?type=download&ID=58'
*   Trying 10.56.144.150:443...
* Connected to kp-gp-app1-prd.codm.gazprom.loc (10.56.144.150) port 443 (#0)
* ALPN: offers h2
* ALPN: offers http/1.1
*  CAfile: /etc/pki/tls/certs/ca-bundle.crt
*  CApath: none
* TLSv1.3 (OUT), TLS handshake, Client hello (1):
* TLSv1.3 (IN), TLS handshake, Server hello (2):
* TLSv1.3 (IN), TLS handshake, Encrypted Extensions (8):
* TLSv1.3 (IN), TLS handshake, Certificate (11):
* TLSv1.3 (OUT), TLS alert, unknown CA (560):
* SSL certificate problem: self signed certificate in certificate chain
* Closing connection 0
curl: (60) SSL certificate problem: self signed certificate in certificate chain
More details here: https://curl.se/docs/sslcerts.html

curl failed to verify the legitimacy of the server and therefore could not
establish a secure connection to it. To learn more about this situation and
how to fix it, please visit the web page mentioned above.
[root@kp-gp-arr1-prd sites-enabled]#
