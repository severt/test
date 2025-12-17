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
* TLSv1.3 (IN), TLS handshake, CERT verify (15):
* TLSv1.3 (IN), TLS handshake, Finished (20):
* TLSv1.3 (OUT), TLS change cipher, Change cipher spec (1):
* TLSv1.3 (OUT), TLS handshake, Finished (20):
* SSL connection using TLSv1.3 / TLS_AES_256_GCM_SHA384
* ALPN: server accepted http/1.1
* Server certificate:
*  subject: C=RU; ST=Moscow; L=Moscow; O=GazpromInform; OU=IT; CN=kp-iod-app-prd.codm.gazprom.loc; emailAddress=d.fursa@inform.gazprom.ru
*  start date: Dec 18 11:37:06 2024 GMT
*  expire date: Dec 18 11:37:06 2026 GMT
*  subjectAltName: host "kp-iod-app-prd.codm.gazprom.loc" matched cert's "kp-iod-app-prd.codm.gazprom.loc"
*  issuer: C=RU; DC=LOC; DC=GAZPROM; L=Moscow; O=JSC GAZPROM; CN=Issuing COD CA SHA2
*  SSL certificate verify ok.
> GET /local/components/r7/viewer/ajax.php?type=download&ID=345 HTTP/1.1
> Host: kp-iod-app-prd.codm.gazprom.loc
> User-Agent: curl/7.85.0
> Accept: */*
>
* TLSv1.3 (IN), TLS handshake, Newsession Ticket (4):
* TLSv1.3 (IN), TLS handshake, Newsession Ticket (4):
* old SSL session ID is stale, removing
* Mark bundle as not supporting multiuse
< HTTP/1.1 200 OK
< Server: Angie/1.6.0
< Date: Wed, 17 Dec 2025 12:49:44 GMT
< Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet
< Transfer-Encoding: chunked
< Connection: keep-alive
< X-Powered-By: PHP/8.1.30
< P3P: policyref="/bitrix/p3p.xml", CP="NON DSP COR CUR ADM DEV PSA PSD OUR UNR BUS UNI COM NAV INT DEM STA"
< X-Powered-CMS: Bitrix Site Manager (fa95ca1cb02af541d02cf4645394a917)
< X-DevSrv-CMS: Bitrix
< Expires: Thu, 19 Nov 1981 08:52:00 GMT
< Cache-Control: no-store, no-cache, must-revalidate
< Pragma: no-cache
< Content-Disposition: attachment; filename*=UTF-8''27yzpwxbxv9ps62hfmw3rs5tpfcuk5qi
< Set-Cookie: PHPSESSID=wdKkTI15sknmyPp3ZDiZvAFphlDkwOCP; path=/; domain=kp-iod-app-prd.codm.gazprom.loc; HttpOnly
< Strict-Transport-Security: max-age=31536000; includeSubDomains; preload
<
Warning: Binary output can mess up your terminal. Use "--output -" to tell
Warning: curl to output it to your terminal anyway, or consider "--output
Warning: <FILE>" to save to a file.
* Failure writing output to destination
* Failed reading the chunked-encoded stream
* Closing connection 0
* TLSv1.3 (OUT), TLS alert, close notify (256):
