[root@kp-iod-app-prd ds-docservice.service.d]# systemctl show ds-docservice -p Environment
Environment=NODE_ENV=production-linux NODE_CONFIG_DIR=/etc/r7-office/documentserver NODE_DISABLE_COLORS=1 NODE_EXTRA_CA_CERTS=/etc/angie/ssl/ca.cer
[root@kp-iod-app-prd ds-docservice.service.d]# systemctl show ds-converter -p Environment
Environment=NODE_ENV=production-linux NODE_CONFIG_DIR=/etc/r7-office/documentserver NODE_DISABLE_COLORS=1 APPLICATION_NAME=r7-office X2T_IS_SERVER=true NODE_EXTRA_CA_CERTS=/etc/angie/ssl/ca.cer
[root@kp-iod-app-prd ds-docservice.service.d]# sudo -u ds curl -v -L -o /tmp/test.docx "https://kp-iod-app-prd.codm.gazprom.loc/local/components/r7/viewer/ajax.php?type=download&ID=334"
  % Total    % Received % Xferd  Average Speed   Time    Time     Time  Current
                                 Dload  Upload   Total   Spent    Left  Speed
  0     0    0     0    0     0      0      0 --:--:-- --:--:-- --:--:--     0*   Trying 10.55.144.251:443...
* Connected to kp-iod-app-prd.codm.gazprom.loc (10.55.144.251) port 443 (#0)
* ALPN: offers h2
* ALPN: offers http/1.1
*  CAfile: /etc/pki/tls/certs/ca-bundle.crt
*  CApath: none
} [5 bytes data]
* TLSv1.3 (OUT), TLS handshake, Client hello (1):
} [512 bytes data]
* TLSv1.3 (IN), TLS handshake, Server hello (2):
{ [122 bytes data]
* TLSv1.3 (IN), TLS handshake, Encrypted Extensions (8):
{ [25 bytes data]
* TLSv1.3 (IN), TLS handshake, Certificate (11):
{ [1818 bytes data]
* TLSv1.3 (IN), TLS handshake, CERT verify (15):
{ [264 bytes data]
* TLSv1.3 (IN), TLS handshake, Finished (20):
{ [52 bytes data]
* TLSv1.3 (OUT), TLS change cipher, Change cipher spec (1):
} [1 bytes data]
* TLSv1.3 (OUT), TLS handshake, Finished (20):
} [52 bytes data]
* SSL connection using TLSv1.3 / TLS_AES_256_GCM_SHA384
* ALPN: server accepted http/1.1
* Server certificate:
*  subject: C=RU; ST=Moscow; L=Moscow; O=GazpromInform; OU=IT; CN=kp-iod-app-prd.codm.gazprom.loc; emailAddress=d.fursa@inform.gazprom.ru
*  start date: Dec 18 11:37:06 2024 GMT
*  expire date: Dec 18 11:37:06 2026 GMT
*  subjectAltName: host "kp-iod-app-prd.codm.gazprom.loc" matched cert's "kp-iod-app-prd.codm.gazprom.loc"
*  issuer: C=RU; DC=LOC; DC=GAZPROM; L=Moscow; O=JSC GAZPROM; CN=Issuing COD CA SHA2
*  SSL certificate verify ok.
} [5 bytes data]
> GET /local/components/r7/viewer/ajax.php?type=download&ID=334 HTTP/1.1
> Host: kp-iod-app-prd.codm.gazprom.loc
> User-Agent: curl/7.85.0
> Accept: */*
>
{ [5 bytes data]
* TLSv1.3 (IN), TLS handshake, Newsession Ticket (4):
{ [297 bytes data]
* TLSv1.3 (IN), TLS handshake, Newsession Ticket (4):
{ [281 bytes data]
* old SSL session ID is stale, removing
{ [5 bytes data]
* Mark bundle as not supporting multiuse
< HTTP/1.1 200 OK
< Server: Angie/1.6.0
< Date: Fri, 19 Dec 2025 06:54:58 GMT
< Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document
< Transfer-Encoding: chunked
< Connection: keep-alive
< X-Powered-By: PHP/8.1.30
< P3P: policyref="/bitrix/p3p.xml", CP="NON DSP COR CUR ADM DEV PSA PSD OUR UNR BUS UNI COM NAV INT DEM STA"
< X-Powered-CMS: Bitrix Site Manager (fa95ca1cb02af541d02cf4645394a917)
< X-DevSrv-CMS: Bitrix
< Expires: Thu, 19 Nov 1981 08:52:00 GMT
< Cache-Control: no-store, no-cache, must-revalidate
< Pragma: no-cache
< Content-Disposition: attachment; filename*=UTF-8''zg94aqiy7qipoiszo7vrgwi3coyeis28
< Set-Cookie: PHPSESSID=Lp4c0dcAbuddXpOj8Y4nIb9xGR9ogZ7g; path=/; domain=kp-iod-app-prd.codm.gazprom.loc; HttpOnly
< Strict-Transport-Security: max-age=31536000; includeSubDomains; preload
<
{ [11514 bytes data]
100 11501    0 11501    0     0   115k      0 --:--:-- --:--:-- --:--:--  116k
* Connection #0 to host kp-iod-app-prd.codm.gazprom.loc left intact
[root@kp-iod-app-prd ds-docservice.service.d]#
[root@kp-iod-app-prd converter]# systemctl show ds-converter -p Environment
Environment=NODE_ENV=production-linux NODE_CONFIG_DIR=/etc/r7-office/documentserver NODE_DISABLE_COLORS=1 APPLICATION_NAME=r7-office X2T_IS_SERVER=true NODE_EXTRA_CA_CERTS=/etc/angie/ssl/ca.cer
[root@kp-iod-app-prd converter]# systemctl show ds-docservice -p Environment
Environment=NODE_ENV=production-linux NODE_CONFIG_DIR=/etc/r7-office/documentserver NODE_DISABLE_COLORS=1 NODE_EXTRA_CA_CERTS=/etc/angie/ssl/ca.cer
[root@kp-iod-app-prd converter]# openssl s_client -connect kp-iod-app-prd.codm.gazprom.loc:443 -servername kp-iod-app-prd.codm.gazprom.loc -showcerts
CONNECTED(00000003)
depth=2 C = RU, L = Moscow, O = PJSC GAZPROM, CN = ROOT_CA_SHA256
verify return:1
depth=1 C = RU, DC = LOC, DC = GAZPROM, L = Moscow, O = JSC GAZPROM, CN = Issuing COD CA SHA2
verify return:1
depth=0 C = RU, ST = Moscow, L = Moscow, O = GazpromInform, OU = IT, CN = kp-iod-app-prd.codm.gazprom.loc, emailAddress = d.fursa@inform.gazprom.ru
verify return:1
---
