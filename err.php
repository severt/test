<?php
$to = 's.sivakov@inform.gazprom.ru';
$subject = 'PHP Test Exchange Relay';
$message = 'Тест отправки через Exchange с правильным отправителем.';
$headers = 'From: ius.loc' . "\r\n" .
           'Reply-To: ius.loc' . "\r\n";

if (mail($to, $subject, $message, $headers)) {
    echo "✓ PHP Mail отправлено успешно!\n";
} else {
    echo "✗ PHP Mail ошибка!\n";
}
?>
