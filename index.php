<?php
    require_once __DIR__ . "/libs/db.php";
    require_once __DIR__ . "/libs/telegram_connection.php";
    require_once __DIR__ . "/libs/db_user.php";
    require_once __DIR__ . "/libs/db_ord.php";
    require_once __DIR__ . "/libs/function.php";

    
    
    //require_once __DIR__ . "/classes/order.php";
    /*if( !R::testConnection()){
        echo 'Нет подключения к дб';
    }
    else echo 'С подключением к дб всё в порядке';*/
    $site_dir = dirname(dirname(__FILE__)).'/'; // корень сайта
    $bot_token = getTokenFromDb(); // токен бота
    $data = file_get_contents('php://input'); // весь ввод перенаправляем в $data
    $data = json_decode($data, true); // декодируем json-закодированные-текстовые данные в PHP-массив
    file_put_contents('file.txt', '$data: '.print_r($data, 1)."\n", FILE_APPEND);
    $reply_markup = "";

    //Обработчик вписанного текста
    if (!empty($data['message']['text'])) {
        $chat_id = $data['message']['from']['id'];
        $user_name = $data['message']['from']['username'];
        $first_name = $data['message']['from']['first_name'];
        $text = trim($data['message']['text']);
        $message_id = $data['message']['message_id'];
        $message_id++;
        $text_array = explode(" ", $text);
        if (findLastMessage($chat_id)=='dm'){
           switch($text){
            case('/start'):
                if(checkUserInBase($chat_id)){
                    setUser($chat_id, $user_name, $first_name);
                }
                if(checkOrdInBase($chat_id)){
                    setOrd($chat_id);
                }
                $text_return = "Здравствуй, $first_name!\n
                Я - Толик, бот, который поможет вам с домашней работой по моделированию зубов!
                Все мои основные функции присутствуют на клавиатуре рядом со строкой ввода
                ";
                $keyboard1 = getKeyBoard([[
                    ['text'=>"Услуги"],
                    ['text'=>'Помощь'],
                    ['text'=>'О нас']
                    ],
                    [['text'=>'Контакты'],
                    ['text'=>'Примеры работ']],
                    [['text'=>'Корзина']]
                    ]);
                $reply_markup = $keyboard1;
                message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                break;
            case ('Корзина'):
                if (empty(findLastMessageId($chat_id))){
                    setLastMessageId($chat_id, $message_id);
                }else {
                    delete_message($bot_token, $chat_id, findLastMessageId($chat_id));
                    setLastMessageId($chat_id, $message_id);
                }
                pocket($chat_id, $bot_token);
                break;
            case('Помощь'):
                $text_return = "Вот команды, что я понимаю: 
                Услуги - список оказываемых услуг
                Помощь - список команд
                О нас - основная информация о нас
                Контакты - контакты с продавцом
                Примеры работ - фото примеров работ
                ";
                message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                break;
            case ('Отсосёшь'):
                $text_return="Иди нахуй";
                message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                break;
            case('Услуги'):
                $text_return = "Вот список оказываемых услуг:";
                $keyboard = getInlineKeyBoard([
                    [['text' => 'Зубы, бережно слепленные, из пластилина ', 'callback_data' => '1p_z']],
                    [['text' => 'Зубы, аккуратно вырезанные из мыла', 'callback_data' => 'm_z']],
                    [['text' => 'Альбом с контурами зубов', 'callback_data' => 'a']],
                    [['text' => 'Индивидуальные занятия по моделированию зубов', 'callback_data' => 'z']],
                ]);
                $reply_markup = $keyboard;
                message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                break;
            case('О нас'):
                $text_return = "Помогаем с работами по моделированию уже 4 года!!!💥🎉✨
                
У нас вы сможете заказать модели зубов 🦷 : 
🌈Необходимого вам цвета 
🌟Нужной степени аккуратности 
⏱В короткие сроки 
💸По приятной цене 
 
Модели зубов делаем индивидуально на заказ 🤝. Изготавливаем в соответствии с контурными тетрадями МГМСУ 📘 и приложением «Dental lite» 📱. Все зубы имеют анатомически верное строение 🦷, высоко оценены преподавателями МГМСУ👍 
Также имеется многолетний опыт работы со студентами МГМУ, РНИMУ, РУДН, Синергии, Владикавказского, Краснодарского, Курского, Hoвоcибиpcкoгo, Пензенского, Ростовского, Санкт-Петербургских, Самарского, Смоленского медицинских университетов и ординаторами ❤️ 
 
Действует гибкая система скидок за количество зубов и программа лояльности! 👋 Начните оформлять заказ, чтобы система рассчитала вашу скидку 🧭 Если собираетесь делать заказ на группу студентов, свяжитесь в сообщениях по контактам ниже, подберём индивидуальное предложение! 📝 
 
📦Осуществляем доставку во все регионы России посредством предпочитаемых вами служб! 
💼По договорённости, возможна доставка по Москве в пределах МКАД, личная встреча в метро в центре или в корпусах МГМСУ (в последних двух случаях, доставка бесплатная!) ";
                message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                break;
            case ('Контакты'):
                $text_return="Telegram, WhatsApp +79229619507 
VK ya.stor100";
                message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                break;
            case ('Примеры работ'):
                $postContent = [
                    'chat_id' => $chat_id,
                    'media' => json_encode([
                        ['type' => 'photo', 'media' => 'attach://1.jpg' ],
                        ['type' => 'photo', 'media' => 'attach://2.jpg' ],
                        ['type' => 'photo', 'media' => 'attach://3.jpg' ],
                        ['type' => 'photo', 'media' => 'attach://4.jpg' ],
                        ['type' => 'photo', 'media' => 'attach://5.jpg' ],
                        ['type' => 'photo', 'media' => 'attach://6.jpg' ],
                        ['type' => 'photo', 'media' => 'attach://7.jpg' ],
                        ['type' => 'photo', 'media' => 'attach://8.jpg' ],
                        ['type' => 'photo', 'media' => 'attach://9.jpg' ],
                        ['type' => 'photo', 'media' => 'attach://10.jpg' ],
                    ]),
                    '1.jpg' => new CURLFile(__DIR__ .'/photo/1.jpg'),
                    '2.jpg' => new CURLFile(__DIR__ . '/photo/2.jpg'),
                    '3.jpg' => new CURLFile(__DIR__ .'/photo/3.jpg'),
                    '4.jpg' => new CURLFile(__DIR__ . '/photo/4.jpg'),
                    '5.jpg' => new CURLFile(__DIR__ .'/photo/5.jpg'),
                    '6.jpg' => new CURLFile(__DIR__ . '/photo/6.jpg'),
                    '7.jpg' => new CURLFile(__DIR__ .'/photo/7.jpg'),
                    '8.jpg' => new CURLFile(__DIR__ . '/photo/8.jpg'),
                    '9.jpg' => new CURLFile(__DIR__ .'/photo/9.jpg'),
                    '10.jpg' => new CURLFile(__DIR__ . '/photo/10.jpg'),
                ];
                photo_to_telegram($bot_token, $postContent, $text_return, $reply_markup);
                $postContent = [
                    'chat_id' => $chat_id,
                    'media' => json_encode([
                        ['type' => 'photo', 'media' => 'attach://11.jpg' ],
                        ['type' => 'photo', 'media' => 'attach://12.jpg' ],
                        ['type' => 'photo', 'media' => 'attach://13.jpg' ],
                        ['type' => 'photo', 'media' => 'attach://14.jpg' ],
                        ['type' => 'photo', 'media' => 'attach://15.jpg' ],
                        ['type' => 'photo', 'media' => 'attach://16.jpg' ],
                        ['type' => 'photo', 'media' => 'attach://17.jpg' ],
                        ['type' => 'photo', 'media' => 'attach://18.jpg' ],
                        ['type' => 'photo', 'media' => 'attach://19.jpg' ],
                        ['type' => 'photo', 'media' => 'attach://20.jpg' ],
                    ]),
                    '11.jpg' => new CURLFile(__DIR__ .'/photo/11.jpg'),
                    '12.jpg' => new CURLFile(__DIR__ . '/photo/12.jpg'),
                    '13.jpg' => new CURLFile(__DIR__ .'/photo/13.jpg'),
                    '14.jpg' => new CURLFile(__DIR__ . '/photo/14.jpg'),
                    '15.jpg' => new CURLFile(__DIR__ .'/photo/15.jpg'),
                    '16.jpg' => new CURLFile(__DIR__ . '/photo/16.jpg'),
                    '17.jpg' => new CURLFile(__DIR__ .'/photo/17.jpg'),
                    '18.jpg' => new CURLFile(__DIR__ . '/photo/18.jpg'),
                    '19.jpg' => new CURLFile(__DIR__ .'/photo/19.jpg'),
                    '20.jpg' => new CURLFile(__DIR__ . '/photo/20.jpg'),
                ];
                photo_to_telegram($bot_token, $postContent, $text_return, $reply_markup);
                break;
            default:
                $text_return = "Извините, я вас не понимаю, пожалуйста воспользуйтесь клавиатурой.";
                $keyboard1 = getKeyBoard([[
                    ['text'=>"Услуги"],
                    ['text'=>'Помощь'],
                    ['text'=>'О нас']
                    ],
                    [['text'=>'Контакты'],
                    ['text'=>'Примеры работ']],
                    [['text'=>'Корзина']]
                    ]);
                $reply_markup = $keyboard1;
                message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                break;
            }
        }
        if (findLastMessage($chat_id)=='time'){
            setTerm($chat_id,$text);
            setLastMessage($chat_id,'dm');
            $text_return="Учтём!
            Давайте подытожу:
            ";
            $keyboard = getInlineKeyBoard([
                [['text' => 'Оставить комментарий к заказу', 'callback_data' => 'comment']],
                [['text' => 'На этом всё', 'callback_data' => 'ok']],
            ]);
            $reply_markup = $keyboard;
            message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
        } 
        if (findLastMessage($chat_id)=='contacts'){
            setContacts($chat_id,$text);
            setLastMessage($chat_id,'contact');
            $text_return="Приняли в работу, свяжемся с вами как всё будет готово";
            message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
            setLastMessage($chat_id,'dm');
            setTime($chat_id);
            sendNotice($chat_id);
            setStatus($chat_id);
            setOrd($chat_id);
        }
        if (findLastMessage($chat_id)=='comment'){
            setComment($chat_id,$text);
            $text_return="Хорошо! Пожалуйста, напишите как можно будет с вами связаться?";
            message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
            setLastMessage($chat_id,'contacts');
        }  
    }
        
    //Обработчик вложенных в сообщение кнопок
    if (array_key_exists('callback_query', $data)) {
        $chat_id = $data['callback_query']['message']['chat']['id'];
        $message = $data['callback_query']['data'];
        if (substr($message,2,2)=="00"){
            addProductToPocket($chat_id, $message);
            $text_return='Выбранный зуб под номером : '.substr($message,-2).' успешно добавлен в корзину, чтобы сделать заказ перейдите в корзину и нажмите "Продолжить"';
            message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
        }
        if (substr($message,0, -1)=="del" || substr($message,0, -2)=="del"){
            $prod_id = R::getAll('SELECT id FROM prodinord WHERE ord ='.findUserActiveOrd($chat_id).';');
            if (substr($message,0, -1)=="del"){
                $i = -1;
            } else $i = -2;
            R::hunt('prodinord', 'id=?',[$prod_id[substr($message,$i)]['id']]);
            $message_id = findLastMessageId($chat_id)+1;
            delete_message($bot_token, $chat_id, findLastMessageId($chat_id));
            setLastMessageId($chat_id, $message_id);
            pocket($chat_id, $bot_token);
        }
        if ((substr($message, 0, 1)=="p") && (substr($message, 2,2)!=00)){
            addProductToPocket($chat_id, $message);
            $text_return='Выбранный комплект с сегментами : '.substr($message,4,1).' и '.substr($message,-1).' успешно добавлен в корзину, чтобы сделать заказ перейдите в корзину и нажмите "Продолжить"';
            message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
        }
        //SELECT * FROM prodinord WHERE product LIKE 's%' OR product LIKE 'p%';
        if ($message == 'cont'){
            if (empty(R::getAll("SELECT id FROM prodinord WHERE ord = ".findUserActiveOrd($chat_id)." AND (product LIKE 's%' OR product LIKE 'p%');")[0]['id'])){
                    message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                    $message = "noteeth";
                }else {
                    message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                    $message ="teeth";
                }
                
        }
        switch($message){
            //Услуги
            case('m_z'): //зубы из мыла
                $text_return = "Вам нужны только коронки или зубы с корнями:";
                $keyboard = getInlineKeyBoard([
                    [['text' => 'Коронки(150р/шт) ', 'callback_data' => 'nsotdel']],
                    [['text' => 'Зубы с корнями(200р/шт)', 'callback_data' => 'wsotdel']],
                ]);
                $reply_markup = $keyboard;
                message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                break;
            case('wsotdel'):
                $text_return="Выберите нужные зубы";
                $keyboard = getInlineKeyBoard([
                    [
                    ['text' => '11', 'callback_data' => 'sw0011'],
                    ['text' => '12', 'callback_data' => 'sw0012'],
                    ['text' => '13', 'callback_data' => 'sw0013'],
                    ['text' => '14', 'callback_data' => 'sw0014'],
                    ['text' => '15', 'callback_data' => 'sw0015'],
                    ['text' => '16', 'callback_data' => 'sw0016'],
                    ['text' => '17', 'callback_data' => 'sw0017'],
                    ['text' => '18', 'callback_data' => 'sw0018'],
                    ],
                    [
                    ['text' => '21', 'callback_data' => 'sw0021'],
                    ['text' => '22', 'callback_data' => 'sw0022'],
                    ['text' => '23', 'callback_data' => 'sw0023'],
                    ['text' => '24', 'callback_data' => 'sw0024'],
                    ['text' => '25', 'callback_data' => 'sw0025'],
                    ['text' => '26', 'callback_data' => 'sw0026'],
                    ['text' => '27', 'callback_data' => 'sw0027'],
                    ['text' => '28', 'callback_data' => 'sw0028'],
                    ],
                    [
                    ['text' => '31', 'callback_data' => 'sw0031'],
                    ['text' => '32', 'callback_data' => 'sw0032'],
                    ['text' => '33', 'callback_data' => 'sw0033'],
                    ['text' => '34', 'callback_data' => 'sw0034'],
                    ['text' => '35', 'callback_data' => 'sw0035'],
                    ['text' => '36', 'callback_data' => 'sw0036'],
                    ['text' => '37', 'callback_data' => 'sw0037'],
                    ['text' => '38', 'callback_data' => 'sw0038'],
                    ],
                    [
                    ['text' => '41', 'callback_data' => 'sw0041'],
                    ['text' => '42', 'callback_data' => 'sw0042'],
                    ['text' => '43', 'callback_data' => 'sw0043'],
                    ['text' => '44', 'callback_data' => 'sw0044'],
                    ['text' => '45', 'callback_data' => 'sw0045'],
                    ['text' => '46', 'callback_data' => 'sw0046'],
                    ['text' => '47', 'callback_data' => 'sw0047'],
                    ['text' => '48', 'callback_data' => 'sw0048'],
                    ],
                ]);
                $reply_markup = $keyboard;
                message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                break;
            case('nsotdel'):
                $text_return="Выберите нужные зубы";
                $keyboard = getInlineKeyBoard([
                    [
                    ['text' => '11', 'callback_data' => 'sn0011'],
                    ['text' => '12', 'callback_data' => 'sn0012'],
                    ['text' => '13', 'callback_data' => 'sn0013'],
                    ['text' => '14', 'callback_data' => 'sn0014'],
                    ['text' => '15', 'callback_data' => 'sn0015'],
                    ['text' => '16', 'callback_data' => 'sn0016'],
                    ['text' => '17', 'callback_data' => 'sn0017'],
                    ['text' => '18', 'callback_data' => 'sn0018'],
                    ],
                    [
                    ['text' => '21', 'callback_data' => 'sn0021'],
                    ['text' => '22', 'callback_data' => 'sn0022'],
                    ['text' => '23', 'callback_data' => 'sn0023'],
                    ['text' => '24', 'callback_data' => 'sn0024'],
                    ['text' => '25', 'callback_data' => 'sn0025'],
                    ['text' => '26', 'callback_data' => 'sn0026'],
                    ['text' => '27', 'callback_data' => 'sn0027'],
                    ['text' => '28', 'callback_data' => 'sn0028'],
                    ],
                    [
                    ['text' => '31', 'callback_data' => 'sn0031'],
                    ['text' => '32', 'callback_data' => 'sn0032'],
                    ['text' => '33', 'callback_data' => 'sn0033'],
                    ['text' => '34', 'callback_data' => 'sn0034'],
                    ['text' => '35', 'callback_data' => 'sn0035'],
                    ['text' => '36', 'callback_data' => 'sn0036'],
                    ['text' => '37', 'callback_data' => 'sn0037'],
                    ['text' => '38', 'callback_data' => 'sn0038'],
                    ],
                    [
                    ['text' => '41', 'callback_data' => 'sn0041'],
                    ['text' => '42', 'callback_data' => 'sn0042'],
                    ['text' => '43', 'callback_data' => 'sn0043'],
                    ['text' => '44', 'callback_data' => 'sn0044'],
                    ['text' => '45', 'callback_data' => 'sn0045'],
                    ['text' => '46', 'callback_data' => 'sn0046'],
                    ['text' => '47', 'callback_data' => 'sn0047'],
                    ['text' => '48', 'callback_data' => 'sn0048'],
                    ],
                ]);
                $reply_markup = $keyboard;
                message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                break;
            case('1p_z'): //Зубы из пластилина
                $text_return="Какой цвет пластилина выберете ?";
                $keyboard = getInlineKeyBoard([
                    [['text' => 'Белый ', 'callback_data' => 'color1']],
                    [['text' => 'Бежевый (телесный)', 'callback_data' => 'color2']],
                ]);
                $reply_markup = $keyboard;
                message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                break;
            case('color1'):
            case('color2'):
                switch($message){
                    case('color1'):
                        $color = 'Белый';
                        break;
                    case('color2'):
                        $color = 'Бежевый';
                        break;
                }
                setColor($chat_id, $color);
                $text_return = "Вам нужны только коронки или зубы с корнями:";
                $keyboard = getInlineKeyBoard([
                    [['text' => 'Коронки(150р/шт) ', 'callback_data' => 'nkor_p']],
                    [['text' => 'Зубы с корнями(200р/шт)', 'callback_data' => 'wkor_p']],
                ]);
                $reply_markup = $keyboard;
                message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                break;
            case('a'):
                $text_return="Альбом представляет собой набор изображений каждого зуба в 5 проекциях, точно повторяющих изображения в тетради МГМСУ «Схематичное изображение контуров зубов». Предоставляем возможность заказать альбом в пропечатанном или нарисованном «от руки» формате, на ваш выбор:";
                $keyboard = getInlineKeyBoard([
                    [['text' => 'Пропечатанный, 1 и 4 сегменты (правая сторона) 14 зубов (150р)', 'callback_data' => 'alb114']],
                    [['text' => 'Пропечатанный, 2 и 3 сегменты (левая сторона) 14 зубов (150р)', 'callback_data' => 'alb123']],
                    [['text' => 'Пропечатанный, 1 и 3 сегменты, 14 зубов (150р)', 'callback_data' => 'alb113']],
                    [['text' => 'Пропечатанный, 2 и 4 сегменты, 14 зубов (150р)', 'callback_data' => 'alb124']],
                    [['text' => 'Пропечатанный, 28 зубов (300р)', 'callback_data' => 'alb3']],
                    [['text' => '«От руки», 1 и 4 сегменты (правая сторона), 14 зубов (400р)', 'callback_data' => 'alb214']],
                    [['text' => '«От руки», 2 и 3 сегменты (левая сторона), 14 зубов (400р)', 'callback_data' => 'alb223']],
                    [['text' => '«От руки», 1 и 3 сегменты, 14 зубов (400р)', 'callback_data' => 'alb213']],
                    [['text' => '«От руки», 2 и 4 сегменты, 14 зубов (400р)', 'callback_data' => 'alb224']],
                    [['text' => '«От руки», 28 зубов (800р)', 'callback_data' => 'alb4']],
                ]);
                $reply_markup = $keyboard;
                message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                break;
            case('z'):
                $text_return="На занятии вы научитесь методике лепки зубов по контурным тетрадям МГМСУ! За один сеанс мы изготовим 2 зуба на ваш выбор.
Встречу можно запланировать, как в общественном пространстве, так и у вас дома. Для лепки зубов вам понадобятся: контурная тетрадь, стоматологическая гладилка средних размеров, стоматологический шпатель, зуботехнический шпатель, канцелярский нож, пластилин (можем предоставить). Стоимость занятий – 1000р/час.";
                message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                $text_return="Если вы заинтересованы в приобретении навыков лепки, пожалуйста, нажмите продолжить";
                 $keyboard = getInlineKeyBoard([
                    [['text' => 'Продолжить', 'callback_data' => 'less']],
                ]);
                $reply_markup = $keyboard;
                message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                break;
            //Зубы из пластилина комплектация
            case('wkor_p'):
                $text_return="Понял! Теперь определимся с комплектацией заказа";
                $keyboard = getInlineKeyBoard([
                    [['text' => 'Комплект из 14 зубов(без «зубов мудрости») ', 'callback_data' => 'w14z']],
                    [['text' => 'Комплект из 16 зубов', 'callback_data' => 'w16z']],
                    [['text' => 'Выбрать отдельные зубы', 'callback_data' => 'wotdel']],
                ]);
                $reply_markup = $keyboard;
                message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                break;
            case('nkor_p'):
                $text_return="Понял! Теперь определимся с комплектацией заказа";
                $keyboard = getInlineKeyBoard([
                    [['text' => 'Комплект из 14 зубов(без «зубов мудрости») ', 'callback_data' => 'n14z']],
                    [['text' => 'Комплект из 16 зубов', 'callback_data' => 'n16z']],
                    [['text' => 'Выбрать отдельные зубы', 'callback_data' => 'notdel']],
                ]);
                $reply_markup = $keyboard;
                message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                break;
            //Подкомплектация из пластилина
            case('w14z'):
                $text_return="Выберите комплект:";
                $keyboard = getInlineKeyBoard([
                    [['text' => '1 и 4 сегменты(правая сторона) ', 'callback_data' => 'pw1414']],
                    [['text' => '2 и 3 сегменты(левая сторона)', 'callback_data' => 'pw1423']],
                    [['text' => '1 и 3 сегменты(по программе МГМСУ)', 'callback_data' => 'pw1413']],
                    [['text' => '2 и 4 сегменты', 'callback_data' => 'pw1424']],
                ]);
                $reply_markup = $keyboard;
                message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                break;
            case('w16z'):
                $text_return="Выберите комплект:";
                $keyboard = getInlineKeyBoard([
                    [['text' => '1 и 4 сегменты(правая сторона) ', 'callback_data' => 'pw1614']],
                    [['text' => '2 и 3 сегменты(левая сторона)', 'callback_data' => 'pw1623']],
                    [['text' => '1 и 3 сегменты', 'callback_data' => 'pw1613']],
                    [['text' => '2 и 4 сегменты', 'callback_data' => 'pw1624']],
                ]);
                $reply_markup = $keyboard;
                message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                break;
            case('wotdel'):
                $text_return="Выберите нужные зубы";
                $keyboard = getInlineKeyBoard([
                    [
                    ['text' => '11', 'callback_data' => 'pw0011'],
                    ['text' => '12', 'callback_data' => 'pw0012'],
                    ['text' => '13', 'callback_data' => 'pw0013'],
                    ['text' => '14', 'callback_data' => 'pw0014'],
                    ['text' => '15', 'callback_data' => 'pw0015'],
                    ['text' => '16', 'callback_data' => 'pw0016'],
                    ['text' => '17', 'callback_data' => 'pw0017'],
                    ['text' => '18', 'callback_data' => 'pw0018'],
                    ],
                    [
                    ['text' => '21', 'callback_data' => 'pw0021'],
                    ['text' => '22', 'callback_data' => 'pw0022'],
                    ['text' => '23', 'callback_data' => 'pw0023'],
                    ['text' => '24', 'callback_data' => 'pw0024'],
                    ['text' => '25', 'callback_data' => 'pw0025'],
                    ['text' => '26', 'callback_data' => 'pw0026'],
                    ['text' => '27', 'callback_data' => 'pw0027'],
                    ['text' => '28', 'callback_data' => 'pw0028'],
                    ],
                    [
                    ['text' => '31', 'callback_data' => 'pw0031'],
                    ['text' => '32', 'callback_data' => 'pw0032'],
                    ['text' => '33', 'callback_data' => 'pw0033'],
                    ['text' => '34', 'callback_data' => 'pw0034'],
                    ['text' => '35', 'callback_data' => 'pw0035'],
                    ['text' => '36', 'callback_data' => 'pw0036'],
                    ['text' => '37', 'callback_data' => 'pw0037'],
                    ['text' => '38', 'callback_data' => 'pw0038'],
                    ],
                    [
                    ['text' => '41', 'callback_data' => 'pw0041'],
                    ['text' => '42', 'callback_data' => 'pw0042'],
                    ['text' => '43', 'callback_data' => 'pw0043'],
                    ['text' => '44', 'callback_data' => 'pw0044'],
                    ['text' => '45', 'callback_data' => 'pw0045'],
                    ['text' => '46', 'callback_data' => 'pw0046'],
                    ['text' => '47', 'callback_data' => 'pw0047'],
                    ['text' => '48', 'callback_data' => 'pw0048'],
                    ],
                ]);
                $reply_markup = $keyboard;
                message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                break;
            case('n14z'):
                $text_return="Выберите комплект:";
                $keyboard = getInlineKeyBoard([
                    [['text' => '1 и 4 сегменты(правая сторона) ', 'callback_data' => 'pn1414']],
                    [['text' => '2 и 3 сегменты(левая сторона)', 'callback_data' => 'pn1423']],
                    [['text' => '1 и 3 сегменты(по программе МГМСУ)', 'callback_data' => 'pn1413']],
                    [['text' => '2 и 4 сегменты', 'callback_data' => 'pn1424']],
                ]);
                $reply_markup = $keyboard;
                message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                break;
            case('n16z'):
                $text_return="Выберите комплект:";
                $keyboard = getInlineKeyBoard([
                    [['text' => '1 и 4 сегменты(правая сторона) ', 'callback_data' => 'pn1614']],
                    [['text' => '2 и 3 сегменты(левая сторона)', 'callback_data' => 'pn1623']],
                    [['text' => '1 и 3 сегменты', 'callback_data' => 'pn1613']],
                    [['text' => '2 и 4 сегменты', 'callback_data' => 'pn1624']],
                ]);
                $reply_markup = $keyboard;
                message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                break;
            case('notdel'):
                $text_return="Выберите нужные зубы";
                $keyboard = getInlineKeyBoard([
                    [
                    ['text' => '11', 'callback_data' => 'pn0011'],
                    ['text' => '12', 'callback_data' => 'pn0012'],
                    ['text' => '13', 'callback_data' => 'pn0013'],
                    ['text' => '14', 'callback_data' => 'pn0014'],
                    ['text' => '15', 'callback_data' => 'pn0015'],
                    ['text' => '16', 'callback_data' => 'pn0016'],
                    ['text' => '17', 'callback_data' => 'pn0017'],
                    ['text' => '18', 'callback_data' => 'pn0018'],
                    ],
                    [
                    ['text' => '21', 'callback_data' => 'pn0021'],
                    ['text' => '22', 'callback_data' => 'pn0022'],
                    ['text' => '23', 'callback_data' => 'pn0023'],
                    ['text' => '24', 'callback_data' => 'pn0024'],
                    ['text' => '25', 'callback_data' => 'pn0025'],
                    ['text' => '26', 'callback_data' => 'pn0026'],
                    ['text' => '27', 'callback_data' => 'pn0027'],
                    ['text' => '28', 'callback_data' => 'pn0028'],
                    ],
                    [
                    ['text' => '31', 'callback_data' => 'pn0031'],
                    ['text' => '32', 'callback_data' => 'pn0032'],
                    ['text' => '33', 'callback_data' => 'pn0033'],
                    ['text' => '34', 'callback_data' => 'pn0034'],
                    ['text' => '35', 'callback_data' => 'pn0035'],
                    ['text' => '36', 'callback_data' => 'pn0036'],
                    ['text' => '37', 'callback_data' => 'pn0037'],
                    ['text' => '38', 'callback_data' => 'pn0038'],
                    ],
                    [
                    ['text' => '41', 'callback_data' => 'pn0041'],
                    ['text' => '42', 'callback_data' => 'pn0042'],
                    ['text' => '43', 'callback_data' => 'pn0043'],
                    ['text' => '44', 'callback_data' => 'pn0044'],
                    ['text' => '45', 'callback_data' => 'pn0045'],
                    ['text' => '46', 'callback_data' => 'pn0046'],
                    ['text' => '47', 'callback_data' => 'pn0047'],
                    ['text' => '48', 'callback_data' => 'pn0048'],
                    ],

                ]);
                $reply_markup = $keyboard;
                message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                break;
            case('alb114'):
            case('alb123'):
            case('alb113'):
            case('alb124'):
            case('alb3'):
            case('alb214'):
            case('alb223'):
            case('alb213'):
            case('alb224'):
            case('alb4'):
                addProductToPocket($chat_id, $message);
                $text_return= "Альбом успешно добавлен в корзину";
                message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                break;
            case("less"):
                addProductToPocket($chat_id, $message);
                $text_return= "Занятие успешно добавлено в корзину";
                message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                break;
            case('teeth'):
                $text_return="Какой степени аккуратности изготовить зубы? (не влияет на цену)";
                $keyboard = getInlineKeyBoard([
                    [['text' => 'Менее аккуратные, чтобы точно не придрались преподаватели', 'callback_data' => 'cach1']],
                    [['text' => 'Не имеет значения(обычной, как на фотографиях)', 'callback_data' => 'cach2']],
                    [['text' => 'Пожалуйста постарайтесь', 'callback_data' => 'cach3']],
                ]);
                $reply_markup = $keyboard;
                message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                break;
            case('noteeth'):
            case('cach1'):
            case('cach2'):
            case('cach3'):
                switch($message){
                    case('cach1'):
                        $quality = 'Менее';
                        break;
                    case('cach2'):
                        $quality = 'Обычно';
                        break;
                    case('cach3'):
                        $quality = 'Более';
                        break;
                }
                setQuality($chat_id, $quality);
                $text_return="  Принял!
    В среднем, выполнение заказа займёт 1-3 дня, в зависимости от его объёма, но, всё же я должен спросить
    Как срочно нужно с вами связаться?";
                $keyboard = getInlineKeyBoard([
                    [['text' => 'Желательно успеть к определённой дате ', 'callback_data' => 'time2']],
                    [['text' => 'Супер срочно! (Свяжемся как можно скорее для уточнения деталей)', 'callback_data' => 'time3']],
                ]);
                $reply_markup = $keyboard;
                message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                break;
            case('time3'):
                setTerm($chat_id,"Срочно");
                $text_return="Учтём!";
                $keyboard = getInlineKeyBoard([
                    [['text' => 'Оставить комментарий к заказу', 'callback_data' => 'comment']],
                    [['text' => 'На этом всё', 'callback_data' => 'ok']],
                ]);
                $reply_markup = $keyboard;
                message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                break;
            case('time2'):
                setLastMessage($chat_id,'time');
                $text_return="Пожалуйста, введите дату
                (Например: 12 декабря или 12.12)";
                 message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                break;
            case('ok'):
                $text_return="Хорошо! Пожалуйста, напишите как можно будет с вами связаться?";
                message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                setLastMessage($chat_id,'contacts');
                break;
            case('comment'):
                $text_return="Напишите ваши пожелания";
                message_to_telegram($bot_token, $chat_id, $text_return, $reply_markup);
                setLastMessage($chat_id,'comment');
                break;
        }

    }

    
    
?>