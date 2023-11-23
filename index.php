<?php
    echo 'НАХУЙ ТАРКОВ';
    header('Content-Type: text/html; charset=utf-8');

    $site_dir = dirname(dirname(__FILE__)).'/'; // корень сайта
    $bot_token = '6672266037:AAFqUUN8fl4A1hBxr0vmgLkOUt_gjEAkQ1U'; // токен вашего бота
    $data = file_get_contents('php://input'); // весь ввод перенаправляем в $data
    $data = json_decode($data, true); // декодируем json-закодированные-текстовые данные в PHP-массив
    file_put_contents('file.txt', '$data: '.print_r($data, 1)."\n", FILE_APPEND);
    //https://api.telegram.org/bot6672266037:AAFqUUN8fl4A1hBxr0vmgLkOUt_gjEAkQ1U/setwebhook?url=https://tristarrit.ru/index.php

    if (!empty($data['message']['text'])) {
        $chat_id = $data['message']['from']['id'];
        $user_name = $data['message']['from']['username'];
        $first_name = $data['message']['from']['first_name'];
        $last_name = $data['message']['from']['last_name'];
        $text = trim($data['message']['text']);
        $text_array = explode(" ", $text);
        
        switch($text){
            case('/help'):
                $text_return = "Вот команды, что я понимаю: 
                /help - список команд
                /about - о нас
                ";
                message_to_telegram($bot_token, $chat_id, $text_return);
                break;
            case('/start'):
                $text_return = "Здравствуй, $first_name $last_name!
                Я - Толик, бот, который поможет вам с домашней работой по моделированию зубов!
                - Наши предложения
                ";
                message_to_telegram($bot_token, $chat_id, $text_return);
                break;
            case('/about'):
                $text_return = "Я пока ничего не умею)
                ";
                message_to_telegram($bot_token, $chat_id, $text_return);
                break;
        }
    }
        /*if ($text == '/help') {
            $text_return = "Привет, $first_name $last_name, вот команды, что я понимаю: 
    /help - список команд
    /about - о нас
    ";
            message_to_telegram($bot_token, $chat_id, $text_return);
        }
        elseif ($text == '/about') {
            $text_return = "
    Ебать, ахуеть оно работает блять, вот прикол
    ";
            message_to_telegram($bot_token, $chat_id, $text_return);
        }
    
    }*/
    
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