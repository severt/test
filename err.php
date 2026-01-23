[root@bus-gp-ap1a-prd postfix]# postmap sender_canonical
[root@bus-gp-ap1a-prd postfix]# service postfix restart
Redirecting to /bin/systemctl restart postfix.service
[root@bus-gp-ap1a-prd postfix]# echo "test" | sudo mail -s "Subj" -r "s.sivakov@inform.gazprom.ru" s.sivakov.inform.gazprom.ru
[root@bus-gp-ap1a-prd postfix]# tail -f /var/log/maillog
Jan 23 14:33:30 bus-gp-ap1a-prd postfix/smtp[3669628]: warning: c1p1-exch.gazprom.loc[10.56.80.67]:465 offered no supported AUTH mechanisms: 'GSSAPI NTLM'
Jan 23 14:33:30 bus-gp-ap1a-prd postfix/smtp[3669628]: 08F8CC0184: to=<s.sivakov.inform.gazprom.ru@gazprom.ru>, orig_to=<s.sivakov.inform.gazprom.ru>, relay=c1p1-exch.gazprom.loc[10.56.80.67]:465, delay=0.14, delays=0.06/0.02/0.06/0, dsn=5.7.1, status=bounced (host c1p1-exch.gazprom.loc[10.56.80.67] said: 550 5.7.1 Client does not have permissions to send as this sender (in reply to end of DATA command))
Jan 23 14:33:30 bus-gp-ap1a-prd postfix/cleanup[3669626]: 2B706C064A: message-id=<20260123113330.2B706C064A@bus-gp-ap1a-prd.codm.gazprom.loc>
Jan 23 14:33:30 bus-gp-ap1a-prd postfix/bounce[3669630]: 08F8CC0184: sender non-delivery notification: 2B706C064A
Jan 23 14:33:30 bus-gp-ap1a-prd postfix/qmgr[3669616]: 2B706C064A: from=<>, size=2827, nrcpt=1 (queue active)
Jan 23 14:33:30 bus-gp-ap1a-prd postfix/qmgr[3669616]: 08F8CC0184: removed
Jan 23 14:33:30 bus-gp-ap1a-prd postfix/relay/smtp[3669631]: SMTPS wrappermode (TCP port 465) requires setting "smtp_tls_wrappermode = yes", and "smtp_tls_security_level = encrypt" (or stronger)
Jan 23 14:33:30 bus-gp-ap1a-prd postfix/relay/smtp[3669631]: warning: c1p1-exch.gazprom.loc[10.56.80.67]:465 offered no supported AUTH mechanisms: 'GSSAPI NTLM'
Jan 23 14:33:30 bus-gp-ap1a-prd postfix/relay/smtp[3669631]: 2B706C064A: to=<s.sivakov@inform.gazprom.ru>, relay=c1p1-exch.gazprom.loc[10.56.80.67]:465, delay=0.09, delays=0.02/0.02/0.04/0, dsn=5.7.1, status=bounced (host c1p1-exch.gazprom.loc[10.56.80.67] said: 550 5.7.1 Client does not have permissions to send as this sender (in reply to end of DATA command))
Jan 23 14:33:30 bus-gp-ap1a-prd postfix/qmgr[3669616]: 2B706C064A: removed
