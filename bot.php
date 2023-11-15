<?php
    header('Content-Type: text/html; charset=utf-8');

    $site_dir = dirname(dirname(__FILE__)).'/'; // корень сайта
    $bot_token = '6672266037:AAFqUUN8fl4A1hBxr0vmgLkOUt_gjEAkQ1U'; // токен вашего бота
    $data = file_get_contents('php://input'); // весь ввод перенаправляем в $data
    $data = json_decode($data, true); // декодируем json-закодированные-текстовые данные в PHP-массив
    file_put_contents(__DIR__ . '/message.txt', print_r($data, true));
    if (!empty($data['message']['text'])) {
        $chat_id = $data['message']['from']['id'];
        $user_name = $data['message']['from']['username'];
        $first_name = $data['message']['from']['first_name'];
        $last_name = $data['message']['from']['last_name'];
        $text = trim($data['message']['text']);
        $text_array = explode(" ", $text);
        
        if ($text == '/help') {
            $text_return = "Привет, $first_name $last_name, вот команды, что я понимаю: 
    /help - список команд
    /about - о нас
    ";
            message_to_telegram($bot_token, $chat_id, $text_return);
        }
        elseif ($text == '/about') {
            $text_return = "verysimple_bot:
    Я пример самого простого бота для телеграм, написанного на простом PHP.
    ";
            message_to_telegram($bot_token, $chat_id, $text_return);
        }
    
    }
    
    // функция отправки сообщени в от бота в диалог с юзером
    function message_to_telegram($bot_token, $chat_id, $text, $reply_markup = '')
    {
        $ch = curl_init();
        $ch_post = [
            CURLOPT_URL => 'https://api.telegram.org/bot' . $bot_token . '/sendMessage',
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_POSTFIELDS => [
                'chat_id' => $chat_id,
                'parse_mode' => 'HTML',
                'text' => $text,
                'reply_markup' => $reply_markup,
            ]
        ];
    
        curl_setopt_array($ch, $ch_post);
        curl_exec($ch);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
</body>
</html>