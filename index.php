<?php
    header('Content-Type: text/html; charset=utf-8');

    $site_dir = dirname(dirname(__FILE__)).'/'; // корень сайта
    $bot_token = '6672266037:AAFqUUN8fl4A1hBxr0vmgLkOUt_gjEAkQ1U'; // токен вашего бота
    $data = file_get_contents('php://input'); // весь ввод перенаправляем в $data
    $data = json_decode($data, true); // декодируем json-закодированные-текстовые данные в PHP-массив
    file_put_contents('file.txt', '$data: '.print_r($data, 1)."\n", FILE_APPEND);
    https://api.telegram.org/bot6672266037:AAFqUUN8fl4A1hBxr0vmgLkOUt_gjEAkQ1U/setwebhook?url=https://dashing-puppy-182f71.netlify.app/index.php
?>