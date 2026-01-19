SAVE_URL='https://kp-gp-r72-prd.codm.gazprom.loc/cache/files/data/.../output.docx?...'
curl -L -v -o /tmp/r7_test.docx -w '\nhttp=%{http_code} size=%{size_download}\n' "$SAVE_URL"
ls -l /tmp/r7_test.docx
file /tmp/r7_test.docx


с сервера kp-gp-app1-prd

curl -I -v "https://kp-gp-r72-prd.codm.gazprom.loc/healthcheck"
*   Trying 10.56.144.149:443...
* Connected to kp-gp-r72-prd.codm.gazprom.loc (10.56.144.149) port 443 (#0)
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
*  subject: C=RU; ST=Moscow; L=Moscow; O=OOO GAZPROM INFORM; OU=USAD; CN=kp-gp-arr1-prd.codm.gazprom.loc; emailAddress=K.Lipatov@inform.gazprom.ru
*  start date: Jul 15 09:38:51 2025 GMT
*  expire date: Jul 15 09:38:51 2027 GMT
*  subjectAltName: host "kp-gp-r72-prd.codm.gazprom.loc" matched cert's "kp-gp-r72-prd.codm.gazprom.loc"
*  issuer: C=RU; DC=LOC; DC=GAZPROM; L=Moscow; O=JSC GAZPROM; CN=Issuing COD CA SHA2
*  SSL certificate verify ok.
> HEAD /healthcheck HTTP/1.1
> Host: kp-gp-r72-prd.codm.gazprom.loc
> User-Agent: curl/7.86.0
> Accept: */*
>
* TLSv1.3 (IN), TLS handshake, Newsession Ticket (4):
* TLSv1.3 (IN), TLS handshake, Newsession Ticket (4):
* old SSL session ID is stale, removing
* Mark bundle as not supporting multiuse
< HTTP/1.1 200 OK
HTTP/1.1 200 OK
< Server: nginx/1.25.4
Server: nginx/1.25.4
< Date: Mon, 19 Jan 2026 09:30:32 GMT
Date: Mon, 19 Jan 2026 09:30:32 GMT
< Content-Type: text/plain; charset=utf-8
Content-Type: text/plain; charset=utf-8
< Content-Length: 4
Content-Length: 4
< Connection: keep-alive
Connection: keep-alive
< ETag: W/"4-X/5TO4MPCKAyY0ipFgr6/IraRNs"
ETag: W/"4-X/5TO4MPCKAyY0ipFgr6/IraRNs"
< Strict-Transport-Security: max-age=31536000
Strict-Transport-Security: max-age=31536000
< X-Content-Type-Options: nosniff
X-Content-Type-Options: nosniff
< Front-End-Https: on
Front-End-Https: on

<
* Connection #0 to host kp-gp-r72-prd.codm.gazprom.loc left intact
######################################################
[root@kp-gp-app2-prd converter]# cat out.log
[2026-01-19T12:38:26.946] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - Start Task
[2026-01-19T12:38:26.972] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - downloadFileFromStorage list 9437037ba1e915e2703f5392f958a4f2/Editor.bin
[2026-01-19T12:38:26.979] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - downloadFileFromStorage complete
[2026-01-19T12:38:26.980] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - downloadFileFromStorage list
[2026-01-19T12:38:27.017] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - processChanges end
[2026-01-19T12:38:28.140] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - stdout:
[2026-01-19T12:38:28.140] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - stderr:
[2026-01-19T12:38:28.140] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - ExitCode (code=0;signal=null;error:0)
[2026-01-19T12:38:28.215] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - processUploadToStorage complete
[2026-01-19T12:38:28.215] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - output (data={"ctx":{"logger":{"category":"nodeJS","context":{"TENANT":"localhost","DOCID":"9437037ba1e915e2703f5392f958a4f2","USERID":"55183"},"callStackSkipIndex":0},"tenant":"localhost","docId":"9437037ba1e915e2703f5392f958a4f2","userId":"55183"},"cmd":{"wopiParams":null,"c":"sfc","id":"9437037ba1e915e2703f5392f958a4f2","userid":"5518","userindex":3,"data":null,"title":"output.docx","outputformat":65,"outputpath":"output.docx","status_info":0,"savekey":"9437037ba1e915e2703f5392f958a4f2_7254","jsonparams":"{\"documentLayout\":{\"openedAt\":1768825917439}}","useractionid":"5518","useractionindex":3,"nobase64":true,"status_info_in":2301934}})
[2026-01-19T12:38:28.216] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - postProcess
[2026-01-19T12:38:28.218] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - deleteFolderRecursive
[2026-01-19T12:38:28.218] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - End Task
[2026-01-19T12:38:28.222] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - ackTask addResponse
[2026-01-19T12:38:28.222] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - ackTask ack
#####################################################################
[root@kp-gp-app2-prd docservice]# cat out.log
[2026-01-19T12:38:10.899] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [userId] nodeJS - io.use start
[2026-01-19T12:38:10.899] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [userId] nodeJS - io.use end
[2026-01-19T12:38:10.899] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [userId] nodeJS - _checkLicense start
[2026-01-19T12:38:10.900] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [userId] nodeJS - sendData: type = license
[2026-01-19T12:38:10.900] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [userId] nodeJS - _checkLicense end
[2026-01-19T12:38:11.043] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [userId] nodeJS - data.type = auth
[2026-01-19T12:38:11.043] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [userId] nodeJS - auth time: 896
[2026-01-19T12:38:11.064] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [5518] nodeJS - sendData: type = auth
[2026-01-19T12:38:11.065] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [5518] nodeJS - Start command: {"withAuthorization":true,"c":"open","id":"9437037ba1e915e2703f5392f958a4f2","userid":"5518","format":"docx","url":"https://kp-gp-app1-prd.codm.gazprom.loc/local/components/r7/viewer/ajax.php?type=download&ID=5890777","title":"2020.11.06 Установка Chrome.docx","lcid":9,"nobase64":true,"convertToOrigin":".pdf.xps.oxps.djvu"}
[2026-01-19T12:38:11.067] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [5518] nodeJS - Response command: {"type":"open","status":"ok","data":{"Editor.bin":"https://kp-gp-r72-prd.codm.gazprom.loc/cache/files/data/9437037ba1e915e2703f5392f958a4f2/Editor.bin/Editor.bin?md5=ASr1RdgwG9kgUEZ50MRSKg&expires=1771430878&filename=Editor.bin"},"openedAt":1768825917439}
[2026-01-19T12:38:11.067] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [5518] nodeJS - sendData: type = documentOpen
[2026-01-19T12:38:11.067] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [5518] nodeJS - End command
[2026-01-19T12:38:11.173] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55182] nodeJS - data.type = clientLog
[2026-01-19T12:38:11.173] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55182] nodeJS - clientLog: onDownloadFile time:14
[2026-01-19T12:38:11.667] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55182] nodeJS - data.type = clientLog
[2026-01-19T12:38:11.668] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55182] nodeJS - clientLog: onOpenDocument time:227
[2026-01-19T12:38:11.762] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55182] nodeJS - data.type = clientLog
[2026-01-19T12:38:11.762] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55182] nodeJS - clientLog: onLoadFonts time:96
[2026-01-19T12:38:12.200] [INFO] [localhost] [docId] [userId] nodeJS - themes.json start
[2026-01-19T12:38:12.201] [DEBUG] [localhost] [docId] [userId] nodeJS - themes.json dir:/var/www/r7-office/documentserver/web-apps/apps/common/main/resources/themes
[2026-01-19T12:38:12.202] [DEBUG] [localhost] [docId] [userId] nodeJS - themes.json themesList:["/var/www/r7-office/documentserver/web-apps/apps/common/main/resources/themes/themes.json"]
[2026-01-19T12:38:12.203] [INFO] [localhost] [docId] [userId] nodeJS - themes.json end
[2026-01-19T12:38:12.352] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55182] nodeJS - data.type = cursor
[2026-01-19T12:38:12.353] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55182] nodeJS - send cursor: {
  cursor: '',
  time: 1768815492353,
  user: '55182',
  useridoriginal: '5518'
}
[2026-01-19T12:38:12.353] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55182] nodeJS - data.type = clientLog
[2026-01-19T12:38:12.353] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55182] nodeJS - clientLog: onDocumentContentReady time:2050 memory:{"totalJSHeapSize":184385300,"usedJSHeapSize":164657768,"jsHeapSizeLimit":4294705152}
[2026-01-19T12:38:13.231] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55182] nodeJS - Connection closed or timed out: reason = transport close
[2026-01-19T12:38:14.078] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [userId] nodeJS - io.use start
[2026-01-19T12:38:14.079] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [userId] nodeJS - io.use end
[2026-01-19T12:38:14.079] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [userId] nodeJS - _checkLicense start
[2026-01-19T12:38:14.079] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [userId] nodeJS - sendData: type = license
[2026-01-19T12:38:14.079] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [userId] nodeJS - _checkLicense end
[2026-01-19T12:38:14.500] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [userId] nodeJS - data.type = auth
[2026-01-19T12:38:14.500] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [userId] nodeJS - auth time: 1283
[2026-01-19T12:38:14.531] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [5518] nodeJS - getCallbackByUserIndex: userIndex = 3 callbacks = {"userIndex":3,"callback":"https://kp-gp-app1-prd.codm.gazprom.loc/local/components/r7/viewer/ajax.php?type=track&ID=5890777&user_id=5518"}
[2026-01-19T12:38:14.533] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [5518] nodeJS - postData request: url = https://kp-gp-app1-prd.codm.gazprom.loc/local/components/r7/viewer/ajax.php?type=track&ID=5890777&user_id=5518;data = {"key":"9437037ba1e915e2703f5392f958a4f2","status":1,"users":["5518"],"actions":[{"type":1,"userid":"5518"}]}
[2026-01-19T12:38:14.650] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [5518] nodeJS - postData response: data = {"error":0,"status":"success"}
[2026-01-19T12:38:14.659] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [5518] nodeJS - sendData: type = auth
[2026-01-19T12:38:14.660] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [5518] nodeJS - Start command: {"withAuthorization":true,"wopiParams":null,"c":"open","id":"9437037ba1e915e2703f5392f958a4f2","userid":"5518","format":"docx","url":"https://kp-gp-app1-prd.codm.gazprom.loc/local/components/r7/viewer/ajax.php?type=download&ID=5890777","title":"2020.11.06 Установка Chrome.docx","lcid":9,"nobase64":true,"convertToOrigin":".pdf.xps.oxps.djvu"}
[2026-01-19T12:38:14.662] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [5518] nodeJS - Response command: {"type":"open","status":"ok","data":{"Editor.bin":"https://kp-gp-r72-prd.codm.gazprom.loc/cache/files/data/9437037ba1e915e2703f5392f958a4f2/Editor.bin/Editor.bin?md5=ASr1RdgwG9kgUEZ50MRSKg&expires=1771430878&filename=Editor.bin"},"openedAt":1768825917439}
[2026-01-19T12:38:14.662] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [5518] nodeJS - sendData: type = documentOpen
[2026-01-19T12:38:14.662] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [5518] nodeJS - End command
[2026-01-19T12:38:14.682] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - data.type = clientLog
[2026-01-19T12:38:14.682] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - clientLog: onDownloadFile time:7
[2026-01-19T12:38:14.839] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - data.type = clientLog
[2026-01-19T12:38:14.839] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - clientLog: onOpenDocument time:153
[2026-01-19T12:38:14.922] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - data.type = clientLog
[2026-01-19T12:38:14.923] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - clientLog: onLoadFonts time:88
[2026-01-19T12:38:15.456] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - data.type = clientLog
[2026-01-19T12:38:15.456] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - clientLog: onDocumentContentReady time:2090 memory:{"totalJSHeapSize":257336927,"usedJSHeapSize":220805303,"jsHeapSizeLimit":4294705152}
[2026-01-19T12:38:17.330] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - data.type = getLock
[2026-01-19T12:38:17.331] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - getLock
[2026-01-19T12:38:17.331] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - getLock id: 622
[2026-01-19T12:38:17.332] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - sendData: type = getLock
[2026-01-19T12:38:17.577] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - data.type = getLock
[2026-01-19T12:38:17.578] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - getLock
[2026-01-19T12:38:17.578] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - getLock id: 3_633
[2026-01-19T12:38:17.579] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - sendData: type = getLock
[2026-01-19T12:38:17.691] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - data.type = getLock
[2026-01-19T12:38:17.691] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - getLock
[2026-01-19T12:38:17.692] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - getLock id: 3_637
[2026-01-19T12:38:17.692] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - sendData: type = getLock
[2026-01-19T12:38:17.833] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - data.type = getLock
[2026-01-19T12:38:17.833] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - getLock
[2026-01-19T12:38:17.833] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - getLock id: 3_641
[2026-01-19T12:38:17.834] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - sendData: type = getLock
[2026-01-19T12:38:17.953] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - data.type = getLock
[2026-01-19T12:38:17.953] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - getLock
[2026-01-19T12:38:17.953] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - getLock id: 3_645
[2026-01-19T12:38:17.954] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - sendData: type = getLock
[2026-01-19T12:38:19.101] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - data.type = isSaveLock
[2026-01-19T12:38:19.102] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - isSaveLock lockRes: true
[2026-01-19T12:38:19.102] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - sendData: type = saveLock
[2026-01-19T12:38:19.112] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - data.type = saveChanges
[2026-01-19T12:38:19.112] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - Start saveChanges: reSave: undefined
[2026-01-19T12:38:19.116] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - saveChanges: deleteIndex: -1 ; startIndex: 0 ; length: 138
[2026-01-19T12:38:19.124] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - sendData: type = unSaveLock
[2026-01-19T12:38:21.928] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - Connection closed or timed out: reason = transport close
[2026-01-19T12:38:22.453] [INFO] [localhost] [docId] [userId] nodeJS - checkDocumentExpire start
[2026-01-19T12:38:22.454] [DEBUG] [localhost] [docId] [userId] nodeJS - expireDoc connections.length = 1
[2026-01-19T12:38:22.454] [INFO] [localhost] [docId] [userId] nodeJS - checkDocumentExpire end: startSaveCount = 0, removedCount = 0
[2026-01-19T12:38:26.940] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - Start saveFromChanges
[2026-01-19T12:38:26.941] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - getCallbackByUserIndex: userIndex = undefined callbacks = {"userIndex":3,"callback":"https://kp-gp-app1-prd.codm.gazprom.loc/local/components/r7/viewer/ajax.php?type=track&ID=5890777&user_id=5518"}
[2026-01-19T12:38:26.947] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - AddTask saveFromChanges
[2026-01-19T12:38:28.221] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - receiveTask start: {"ctx":{"logger":{"category":"nodeJS","context":{"TENANT":"localhost","DOCID":"9437037ba1e915e2703f5392f958a4f2","USERID":"55183"},"callStackSkipIndex":0},"tenant":"localhost","docId":"9437037ba1e915e2703f5392f958a4f2","userId":"55183"},"cmd":{"wopiParams":null,"c":"sfc","id":"9437037ba1e915e2703f5392f958a4f2","userid":"5518","userindex":3,"data":null,"title":"output.docx","outputformat":65,"outputpath":"output.docx","status_info":0,"savekey":"9437037ba1e915e2703f5392f958a4f2_7254","jsonparams":"{\"documentLayout\":{\"openedAt\":1768825917439}}","useractionid":"5518","useractionindex":3,"nobase64":true,"status_info_in":2301934}}
[2026-01-19T12:38:28.222] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - Start commandSfcCallback
[2026-01-19T12:38:28.223] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - getCallbackByUserIndex: userIndex = 3 callbacks = {"userIndex":3,"callback":"https://kp-gp-app1-prd.codm.gazprom.loc/local/components/r7/viewer/ajax.php?type=track&ID=5890777&user_id=5518"}
[2026-01-19T12:38:28.224] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - Callback commandSfcCallback: callback = https://kp-gp-app1-prd.codm.gazprom.loc/local/components/r7/viewer/ajax.php?type=track&ID=5890777&user_id=5518
[2026-01-19T12:38:28.230] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - commandSfcCallback presence: count = 0
[2026-01-19T12:38:28.232] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - postData request: url = https://kp-gp-app1-prd.codm.gazprom.loc/local/components/r7/viewer/ajax.php?type=track&ID=5890777&user_id=5518;data = {"key":"9437037ba1e915e2703f5392f958a4f2","status":2,"url":"https://kp-gp-r72-prd.codm.gazprom.loc/cache/files/data/9437037ba1e915e2703f5392f958a4f2_7254/output.docx/output.docx?md5=QLgKlLomrtBZT9jT03ytgw&expires=1768816409&filename=output.docx","changesurl":"https://kp-gp-r72-prd.codm.gazprom.loc/cache/files/data/9437037ba1e915e2703f5392f958a4f2_7254/changes.zip/changes.zip?md5=cGHWcbFWixtJ4IXx6mSplg&expires=1768816409&filename=changes.zip","history":{"serverVersion":"2024.3.2","changes":[{"created":"2026-01-19 09:38:19","user":{"id":"5518","name":"Сиваков С. В."}}]},"users":["5518"],"actions":[{"type":0,"userid":"5518"}],"lastsave":"2026-01-19T09:38:19.000Z","notmodified":false,"filetype":"docx"}
[2026-01-19T12:38:28.583] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - postData response: data = {"error":0,"status":"success"}
[2026-01-19T12:38:28.596] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - getCallbackByUserIndex: userIndex = 3 callbacks = {"userIndex":3,"callback":"https://kp-gp-app1-prd.codm.gazprom.loc/local/components/r7/viewer/ajax.php?type=track&ID=5890777&user_id=5518"}
[2026-01-19T12:38:28.596] [DEBUG] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - End commandSfcCallback
[2026-01-19T12:38:28.596] [INFO] [localhost] [9437037ba1e915e2703f5392f958a4f2] [55183] nodeJS - receiveTask end
[2026-01-19T12:38:42.523] [INFO] [localhost] [e2a5a0c0fa6d11f2d3e95158f9c07f91] [415651] nodeJS - Connection closed or timed out: reason = transport close
#####################################################################
