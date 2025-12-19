<?php
// probe.php (app1)
header('Content-Type: text/plain; charset=utf-8');

$url = 'https://app2.example.local/nettest/ping.txt'; // поменяйте на ваш app2 URL

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_TIMEOUT => 10,

    // ВАЖНО: лучше оставить проверку сертификата включенной.
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,

    // Если у app2 сертификат от внутреннего CA — добавьте CURLOPT_CAINFO.
    // CURLOPT_CAINFO => '/etc/pki/ca-trust/source/anchors/internal-ca.pem',
]);

$body = curl_exec($ch);
$err  = curl_error($ch);
$code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
curl_close($ch);

if ($body === false) {
    http_response_code(502);
    echo "curl error: $err\n";
    exit;
}
if ($code < 200 || $code >= 300) {
    http_response_code(502);
    echo "upstream http code: $code\n";
    echo $body;
    exit;
}

echo $body;


--------------
 <!doctype html>
<meta charset="utf-8" />
<button id="btn">Проверить app1→app2 по 443</button>
<pre id="out">Нажмите кнопку…</pre>

<script>
const out = document.getElementById('out');

document.getElementById('btn').addEventListener('click', async () => {
  out.textContent = 'Запрос...';
  try {
    const r = await fetch('/probe.php', { cache: 'no-store' });
    const t = await r.text(); // чтение тела ответа как текста [web:1]
    out.textContent = `HTTP ${r.status}\n\n` + t;
  } catch (e) {
    out.textContent = 'Ошибка сети/доступа: ' + e;
  }
});
</script>
----------------

 
