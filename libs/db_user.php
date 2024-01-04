<?php
function setUser($id,$name,$first_name){
    require_once __DIR__ . '/../libs/db.php';
    $user = R::dispense('user');
    $user->chat_id = $id;
    $user->name = $name;
    $user->first_name = $first_name;
    $user = R::store($user);
}
function findUserByChatId($id){
    return R::getAll('SELECT id FROM user WHERE chat_id = '.$id.';')[0]['id'];
}
function checkUserInBase($id){
    if (empty(R::findOne('user', 'chat_id = '.$id))){
        return true;
    }else{return false;}
}
function setLastMessage($id, $message){
    $user=R::load('user',findUserByChatId($id));
    $user->lastmessage = $message;
    $user = R::store($user);
}
function findLastMessage($id){
    return R::findOne('user','id = '.findUserByChatId($id))['lastmessage'];
}
function findLastMessageId($id){
    return R::findOne('user','id = '.findUserByChatId($id))['lastpocketmessage'];
}
function setLastMessageId($id, $message_id){
    $user=R::load('user',findUserByChatId($id));
    $user->lastpocketmessage = $message_id;
    $user = R::store($user);
}
?>