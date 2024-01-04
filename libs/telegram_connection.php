<?php
    require_once __DIR__ . "/db.php";
    
    function message_to_telegram($bot_token, $chat_id, $text, $reply_markup){
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

    function delete_message($bot_token, $chat_id, $message_id){
        $ch = curl_init();
        $ch_post = [
            CURLOPT_URL => 'https://api.telegram.org/bot' . $bot_token . '/deleteMessage',
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_POSTFIELDS => [
                'chat_id' => $chat_id,
                'parse_mode' => 'HTML',
                'message_id' => $message_id,
            ]
        ];
    
        curl_setopt_array($ch, $ch_post);
        curl_exec($ch);
    }

    function photo_to_telegram($bot_token, $postContent, $text, $reply_markup){
        $ch = curl_init();
        $ch_post = [
            CURLOPT_URL => 'https://api.telegram.org/bot' . $bot_token . '/sendMediaGroup',
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HEADER => FALSE,
            CURLOPT_POSTFIELDS => $postContent,
        ];
    
        curl_setopt_array($ch, $ch_post);
        curl_exec($ch);
    }

    function getInlineKeyBoard($data)
    {
        $inlineKeyboard = array(
            "inline_keyboard" => $data,
        );
        return json_encode($inlineKeyboard);
    }
    
    function getKeyBoard($data)
    {
        $keyboard = [
            "keyboard" => $data,
            "one_time_keyboard" => false,
            "resize_keyboard" => true
            ];
        return json_encode($keyboard);
    }
?>