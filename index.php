<?php
    header('Content-Type: text/html; charset=utf-8');

    $bot_token = '6672266037:AAFqUUN8fl4A1hBxr0vmgLkOUt_gjEAkQ1U'; // токен вашего бота
    $data = json_decode(file_get_contents('php://input'), true); // декодируем json-закодированные-текстовые данные в PHP-массив
    file_put_contents('file.txt', '$data: '.print_r($data, 1)."\n", FILE_APPEND);
    https://api.telegram.org/bot6672266037:AAFqUUN8fl4A1hBxr0vmgLkOUt_gjEAkQ1U/setwebhook?url=http://tristarrit.42web.io/

    /*$data = $data['callback_query'] ? $data['callback_query'] : $data['message'];
    define('TOKEN', '6672266037:AAFqUUN8fl4A1hBxr0vmgLkOUt_gjEAkQ1U');
    $message = mb_strtolower(($data['text'] ? $data['text']: $data['data']), 'utf-8');

    switch ($message)
    {
        case '/help':
            $method = 'sendMessage';
            $send_data = [
                'text' => 'Я пока ничего не могу :)'
            ];
            break;
    }

    $send_data['$chat_id'] = $data['chat']['id'];
    $res = sendTelegram($method, $send_data);
    function sendTelegram($method, $data, $headers = []){
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'https://api.telegram.org/bot'. TOKEN . '/'. $method,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array_merge(array('Content-Type: application/json'), $headers),
        ] );
        $result = curl_exec($curl);
        curl_close($curl);
        return (json_decode($result, true)) ? json_decode($result, true) : $result;
    }*/
?>