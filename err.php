[2025-12-19T10:21:50.683] [INFO] [localhost] [f3eae6f4a023241929e0c28bca6f2ffa] [5518] nodeJS - Start Task
[2025-12-19T10:21:50.711] [ERROR] [null] [null] [null] nodeJS - (node:648706) Warning: Setting the NODE_TLS_REJECT_UNAUTHORIZED environment variable to '0' makes TLS connections and HTTPS requests
 insecure by disabling certificate verification.
(Use `converter --trace-warnings ...` to show where the warning was created)
[2025-12-19T10:21:50.714] [ERROR] [localhost] [f3eae6f4a023241929e0c28bca6f2ffa] [5518] nodeJS - error downloadFile:url=https://kp-iod-app-prd.codm.gazprom.loc/local/components/r7/viewer/ajax.php?
type=download&ID=334;attempt=1;code:UNABLE_TO_VERIFY_LEAF_SIGNATURE;connect:undefined Error: unable to verify the first certificate
    at TLSSocket.onConnectSecure (_tls_wrap.js:1515:34)
    at TLSSocket.emit (events.js:400:28)
    at TLSSocket._finishInit (_tls_wrap.js:937:8)
    at TLSWrap.ssl.onhandshakedone (_tls_wrap.js:709:12)
[2025-12-19T10:21:51.721] [ERROR] [localhost] [f3eae6f4a023241929e0c28bca6f2ffa] [5518] nodeJS - error downloadFile:url=https://kp-iod-app-prd.codm.gazprom.loc/local/components/r7/viewer/ajax.php?
type=download&ID=334;attempt=2;code:UNABLE_TO_VERIFY_LEAF_SIGNATURE;connect:undefined Error: unable to verify the first certificate
    at TLSSocket.onConnectSecure (_tls_wrap.js:1515:34)
    at TLSSocket.emit (events.js:400:28)
    at TLSSocket._finishInit (_tls_wrap.js:937:8)
    at TLSWrap.ssl.onhandshakedone (_tls_wrap.js:709:12)
[2025-12-19T10:21:52.726] [ERROR] [localhost] [f3eae6f4a023241929e0c28bca6f2ffa] [5518] nodeJS - error downloadFile:url=https://kp-iod-app-prd.codm.gazprom.loc/local/components/r7/viewer/ajax.php?
type=download&ID=334;attempt=3;code:UNABLE_TO_VERIFY_LEAF_SIGNATURE;connect:undefined Error: unable to verify the first certificate
    at TLSSocket.onConnectSecure (_tls_wrap.js:1515:34)
    at TLSSocket.emit (events.js:400:28)
    at TLSSocket._finishInit (_tls_wrap.js:937:8)
    at TLSWrap.ssl.onhandshakedone (_tls_wrap.js:709:12)
[2025-12-19T10:21:53.728] [DEBUG] [localhost] [f3eae6f4a023241929e0c28bca6f2ffa] [5518] nodeJS - ExitCode (code=0;signal=null;error:-81)
[2025-12-19T10:21:53.729] [DEBUG] [localhost] [f3eae6f4a023241929e0c28bca6f2ffa] [5518] nodeJS - output (data={"ctx":{"logger":{"category":"nodeJS","context":{"TENANT":"localhost","DOCID":"f3eae6f
4a023241929e0c28bca6f2ffa","USERID":"5518"},"callStackSkipIndex":0},"tenant":"localhost","docId":"f3eae6f4a023241929e0c28bca6f2ffa","userId":"5518"},"cmd":{"withAuthorization":true,"c":"open","id"
:"f3eae6f4a023241929e0c28bca6f2ffa","userid":"5518","format":"docx","url":"https://kp-iod-app-prd.codm.gazprom.loc/local/components/r7/viewer/ajax.php?type=download&ID=334","title":"TEST77.docx","
outputformat":8192,"outputpath":"Editor.bin","embeddedfonts":false,"status_info":-81,"lcid":25,"nobase64":true,"convertToOrigin":".pdf.xps.oxps.djvu"}})
[2025-12-19T10:21:53.729] [DEBUG] [localhost] [f3eae6f4a023241929e0c28bca6f2ffa] [5518] nodeJS - postProcess
[2025-12-19T10:21:53.730] [DEBUG] [localhost] [f3eae6f4a023241929e0c28bca6f2ffa] [5518] nodeJS - deleteFolderRecursive
[2025-12-19T10:21:53.730] [INFO] [localhost] [f3eae6f4a023241929e0c28bca6f2ffa] [5518] nodeJS - End Task
[2025-12-19T10:21:53.732] [INFO] [localhost] [f3eae6f4a023241929e0c28bca6f2ffa] [5518] nodeJS - ackTask addResponse
[2025-12-19T10:21:53.733] [INFO] [localhost] [f3eae6f4a023241929e0c28bca6f2ffa] [5518] nodeJS - ackTask ack
