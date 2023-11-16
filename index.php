<?php
//65fc0986
if (!isset($_REQUEST)) {
    return "65fc0986";
}
$confirmationToken = '65fc0986';

$token = 'vk1.a.0gxV9bsjsuQ11oW-ogbt_cAV7lhWsEieVFdzO1CTwMb8EdFCJCbaOM752YjkKq2rQy8ngwiEMvbBRQjyba1x8ioFioXzpieRLQeRnkGDpoJg5nT3mifjRyAAUMcfuNOX-uaO1AaolX4zKRCzpa-pD2--4Orf68PwMkdY1ND9qf7h6igNTqLJwOpKes_aetYYlmzRcqdpN61XHaCQSGkRJw';
$data = json_decode(file_get_contents('php://input'));
$secretKey = "MaaYAol";
echo 'data:'.$data;

if(strcmp($data->secret, $secretKey) !== 0 && strcmp($data->type, 'confirmation') !== 0){
    return;
}

    switch ($data->type) {
    //Если это уведомление для подтверждения адреса сервера...
    case 'confirmation':
        //...отправляем строку для подтверждения адреса
        echo $confirmationToken;
        break;

    //Если это уведомление о новом сообщении...
    case 'message_new':
        //...получаем id его автора
        $userId = $data->object->user_id;
        //затем с помощью users.get получаем данные об авторе
        $userInfo = json_decode(file_get_contents("https://api.vk.com/method/users.get?user_ids={$userId}&v=5.0"));

        //и извлекаем из ответа его имя
        $user_name = $userInfo->response[0]->first_name;

        //С помощью messages.send и токена сообщества отправляем ответное сообщение
        $request_params = array(
            'message' => "{$user_name}, ваше сообщение зарегистрировано!<br>".
                            "Мы постараемся ответить в ближайшее время.",
            'user_id' => $userId,
            'access_token' => $token,
            'v' => '5.0'
        );

        $get_params = http_build_query($request_params);

        file_get_contents('https://api.vk.com/method/messages.send?' . $get_params);

        //Возвращаем "ok" серверу Callback API
        echo('ok');

        break;
    }
?>