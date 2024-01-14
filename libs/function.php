<?php
    require_once __DIR__ . "/db.php";
    require_once __DIR__ ."/db_user.php";
    require_once __DIR__ ."/telegram_connection.php";
    require_once  __DIR__ ."/db_ord.php";
    
    function getTokenFromDb(){
        return R::getAll('SELECT token FROM bot WHERE id = 0;')[0]['token'];
    }

    function pocket($chat_id, $bot_token){
        $key = [];
        $price = 0;
        $teethcol = 0;
        $teethprice = 0;
        $pocket = R::getAll('SELECT id, product, name FROM prodinord WHERE ord='.findUserActiveOrd($chat_id).';');
                if(!empty($pocket)){
                    for ($i=0; $i<count($pocket); $i++){
                        if (empty($pocket[$i]['name'])){
                            $key[$i] = [['text' => R::getAll('SELECT description FROM product WHERE name="'.$pocket[$i]['product'].'";')[0]['description'], 'callback_data' => 'del'.$i]];
                        }else{
                            $seg1 = substr($pocket[$i]['name'],0,1);
                            $seg2 = substr($pocket[$i]['name'],-1);
                            if (substr($pocket[$i]['product'],-2)==00){
                                $key[$i] = [['text' => R::getAll('SELECT description FROM product WHERE name="'.$pocket[$i]['product'].'";')[0]['description'] .' ('.$seg1.''.$seg2.')', 'callback_data' => 'del'.$i]];
                            }else{$key[$i] = [['text' => R::getAll('SELECT description FROM product WHERE name="'.$pocket[$i]['product'].'";')[0]['description'] .'('.$seg1.' и '.$seg2.' сегмент)', 'callback_data' => 'del'.$i]];}
                        }
                        if (!empty(R::getAll('SELECT teethcol FROM product WHERE name="'.$pocket[$i]['product'].'";')[0]['teethcol'])){
                            $teethcol = $teethcol +  R::getAll('SELECT teethcol FROM product WHERE name="'.$pocket[$i]['product'].'";')[0]['teethcol'];
                            $teethprice = $teethprice +  R::getAll('SELECT price FROM product WHERE name ="'.$pocket[$i]['product'].'";')[0]['price'];
                        }else{$price = $price + R::getAll('SELECT price FROM product WHERE name ="'.$pocket[$i]['product'].'";')[0]['price'];}
                    }
                    $price1 = $price + $teethprice;
                    if ($teethcol>=7){
                        $teethprice = $teethprice * 0.9;
                        $price = $teethprice + $price;
                        $c = substr($price, -2);
                        if($c>=50){
                            $price = $price +(100-$c);
                        }else{
                            $price = $price - $c;
                        }
                        $text_return = "Сейчас в вашей корзине товаров на ".$price.' руб. (без скидки: '.$price1.' руб.)';
                    }else {
                        $price = $teethprice + $price;
                        $text_return = "Сейчас в вашей корзине товаров на ".$price.' руб.';
                    }
                    array_push($key, [['text' => "Продолжить", 'callback_data' => 'cont']]);
                }else $text_return = "Пока что в вашей корзине ничего нет";
                setPrice($chat_id, $price);
                $keyboard = getInlineKeyBoard($key);
                $reply_markup = $keyboard;
                message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
    }

    function sendNotice($user_id){
        $bot_token = getTokenFromDb();
        $admin_id = R::getAll('SELECT admin_id FROM bot WHERE id = 0;')[0]['admin_id'];
        $user = R::getAll('SELECT * FROM user WHERE chat_id = '.$user_id.";");

        $ord = R::load('ord', findUserActiveOrd($user_id));

        $text_return = "&#8252; Поступил новый заказ от ".$user[0]['first_name']." на сумму ".$ord->price.":\n
    https://web.telegram.org/k/#".$user_id."\n
    Срок: ".$ord->term."
    Цвет зубов: ".$ord->color."
    Контакты: ".$ord->contacts."
    Комментарий: ".$ord->comments."\n
        ";

        $pocket = R::getAll('SELECT id, product, name FROM prodinord WHERE ord='.findUserActiveOrd($user_id).';');
        $text_return .= "Содержание заказа:
";
        for ($i=0; $i<count($pocket); $i++){
            if (empty($pocket[$i]['name'])){
                $text_return .= R::getAll('SELECT description FROM product WHERE name="'.$pocket[$i]['product'].'";')[0]['description']."
";
            }else{
                $seg1 = substr($pocket[$i]['name'],0,1);
                $seg2 = substr($pocket[$i]['name'],-1);
                if (substr($pocket[$i]['product'],-2)==00){
                    $text_return .= R::getAll('SELECT description FROM product WHERE name="'.$pocket[$i]['product'].'";')[0]['description'] .' ('.$seg1.''.$seg2.')
';
                }else{$text_return .= R::getAll('SELECT description FROM product WHERE name="'.$pocket[$i]['product'].'";')[0]['description'] .'('.$seg1.' и '.$seg2.' сегмент)
';
                }
            }
        }

        $reply_markup = "";
        message_to_telegram($bot_token, $admin_id, $text_return, $reply_markup);
    }

?>