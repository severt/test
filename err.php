echo -n | openssl s_client -connect kp-gp-r72-prd.codm.gazprom.loc:443 -servername kp-gp-r72-prd.codm.gazprom.loc | openssl x509 > /etc/pki/tls/certs/r7-office-ca.crt

Узнайте, где лежит системный бандл: /etc/pki/tls/certs/ca-bundle.crt.
Создайте свой комбинированный файл:
cat /etc/pki/tls/certs/ca-bundle.crt /etc/pki/tls/certs/r7-office-ca.crt > /etc/pki/tls/certs/php-ca-bundle.crt


Для CentOS/RHEL (как у вас):

Узнайте, где лежит системный бандл: /etc/pki/tls/certs/ca-bundle.crt.

Создайте свой комбинированный файл:

bash
cat /etc/pki/tls/certs/ca-bundle.crt /etc/pki/tls/certs/r7-office-ca.crt > /etc/pki/tls/certs/php-ca-bundle.crt
(Теперь в php-ca-bundle.crt есть и все мировые CA, и ваш Р7).

Для Ubuntu/Debian:
Системный путь обычно /etc/ssl/certs/ca-certificates.crt. Логика та же:
cat /etc/ssl/certs/ca-certificates.crt /путь/к/r7.crt > /etc/ssl/certs/php-custom.crt

3. Настройка php.ini
Найдите активный php.ini (в консоли php --ini или php -i | grep 'Loaded Configuration File'). Часто для веб-сервера (PHP-FPM) он лежит отдельно, например /etc/php/7.4/fpm/php.ini.

Откройте его на редактирование.

Найдите директиву openssl.cafile.

Раскомментируйте и укажите путь к вашему новому файлу:

text
openssl.cafile = /etc/pki/tls/certs/php-ca-bundle.crt
Если используете cURL напрямую, можно также задать curl.cainfo:

text
curl.cainfo = /etc/pki/tls/certs/php-ca-bundle.crt
4. Перезапуск PHP
Чтобы изменения вступили в силу, перезапустите службу PHP-FPM (или Apache):

bash
# Для CentOS/RHEL
systemctl restart php-fpm
# или
systemctl restart httpd
