<?php
function setOrd($id){
    require_once __DIR__ . '/../libs/db.php';
    $user = R::dispense('ord');
    $user->user = $id;
    $user->status = 'Не готов';
    $user = R::store($user);
}
function checkOrdInBase($id){
    if (empty(R::findOne('ord', 'user = '.$id))){
        return true;
    }else{return false;}
}
function checkOrdReady($id){
    $ord = findUserActiveOrd($id);
    $data = R::getAll('SELECT * FROM ord WHERE id ='.$ord.';');
    if (empty($data[0]['time']) || empty($data[0]['term']) || ($data[0]['price']==0)){
        return true;
    }else{return false;}
}
function findUserActiveOrd($id){
    return R::getAll('SELECT id FROM ord WHERE user = '.$id.' AND status = "Не готов";')[0]['id'];
}
function addProductToPocket($user_id,$prod){
    $prod_in_pocket = R::dispense('prodinord');
    $prod_in_pocket->user = findUserByChatId($user_id);
    $prod_in_pocket->ord = findUserActiveOrd($user_id);
    $prod_in_pocket->product = substr($prod, 0, 4);
    $prod_in_pocket->name = substr($prod, 4,2);
    $prod_in_pocket = R::store($prod_in_pocket);
}
function setColor($user_id, $color){
    $ord = R::load('ord', findUserActiveOrd($user_id));
    $ord->color = $color;
    R::store($ord);
}
function setQuality($user_id, $quality){
    $ord = R::load('ord', findUserActiveOrd($user_id));
    $ord->quality = $quality;
    R::store($ord);
}
function setTerm($user_id, $term){
    $ord = R::load('ord', findUserActiveOrd($user_id));
    $ord->term = $term;
    R::store($ord);
}
function setComment($user_id, $comment){
    $ord = R::load('ord', findUserActiveOrd($user_id));
    $ord->comments = $comment;
    R::store($ord);
}
function setContacts($user_id, $contacts){
    $ord = R::load('ord', findUserActiveOrd($user_id));
    $ord->contacts = $contacts;
    R::store($ord);
}
function setTime($user_id){
    date_default_timezone_set('Europe/Moscow');
    $ord = R::load('ord', findUserActiveOrd($user_id));
    $ord->time = date('Y-m-d');
    R::store($ord);
}
function setPrice($user_id, $price){    
    $ord = R::load('ord', findUserActiveOrd($user_id));
    $ord->price = $price;
    R::store($ord);

}
function setStatus($user_id){
    $ord = R::load('ord', findUserActiveOrd($user_id));
    $ord->status = "Заказан";
    R::store($ord);
}
?>