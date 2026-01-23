[root@bus-gp-ap1a-prd postfix]# postconf relayhost
relayhost = [c1p1-exch.gazprom.loc]:465, [c1p2-exch.gazprom.loc]:465
[root@bus-gp-ap1a-prd postfix]# postconf smtp_tls_security_level
smtp_tls_security_level = encrypt
[root@bus-gp-ap1a-prd postfix]# postconf smtp_tls_wrappermode
smtp_tls_wrappermode = yes
[root@bus-gp-ap1a-prd postfix]# openssl s_client -connect c1p1-exch.gazprom.loc:465 -showcerts 2>&1 | head -30
139919820928832:error:1408F10B:SSL routines:ssl3_get_record:wrong version number:ssl/record/ssl3_record.c:331:
CONNECTED(00000003)
---
no peer certificate available
---
No client certificate CA names sent
---
SSL handshake has read 5 bytes and written 323 bytes
Verification: OK
---
New, (NONE), Cipher is (NONE)
Secure Renegotiation IS NOT supported
Compression: NONE
Expansion: NONE
No ALPN negotiated
Early data was not sent
Verify return code: 0 (ok)
