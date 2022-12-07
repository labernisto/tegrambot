<?php

echo 'WELCOME TO TELEGRAM CHATBOT - UPOU';

$hfrom = '«FROM» ';
$hsubject = '«SUBJECT» ';
$hmessage = '«MESSAGE» ';
$hrecipient = '«RECIPIENT» ';
$heom = '«EOM»';
 
$str = '/([a-z0-9_\.\-])+\@(([a-z0-9\-])+\.)+([a-z0-9]{2,4})+/i';
$token = '5989406103:AAGBF7xnHPBsRCRCVBveWgBjgPCYTZzE0XQ';
$cpromt = 'Are you sure you want to send this message(s)?';

$access_token = $token;
$api = 'https://api.telegram.org/bot' . $access_token;
$output = json_decode(file_get_contents('php://input'), TRUE);
$jsonview = json_encode($output, JSON_PRETTY_PRINT);

$chat_id = $output['message']['chat']['id'];
$message = $output['message']['text'];
$callback_query = $output['callback_query'];
$data = $callback_query['data'];
$message_id = $output['callback_query']['message']['message_id'];
$chat_id_in = $callback_query['message']['chat']['id'];

$inline_button1 = array("text"=>"Yes","callback_data"=>'Yes');
$inline_button2 = array("text"=>"No","callback_data"=>'No');
$inline_keyboard = [[$inline_button1,$inline_button2]];
$keyboard=array("inline_keyboard"=>$inline_keyboard);
$replyMarkup = json_encode($keyboard); 

   if ($data == 'Yes') {

     $mess = str_replace($cpromt, ' ', $output['callback_query']['message']['text']);
     $mess = addslashes($mess);

     $tgsender = $hfrom . $output['callback_query']['from']['first_name'];

     $arguments =  $tgsender . $mess . $heom;
     $cbid = $output['callback_query']['id'];

     file_put_contents('chats/' . $cbid . '.txt', $arguments);

     $emailSending = shell_exec("python3 emailer.py " . $cbid . '.txt');
     sendMessageR($chat_id_in, $emailSending); 

    }

   if ($data == 'No') {
    sendMessageR($chat_id_in, 'Email sending was cancelled!'); 
   }

    $origMess =  implode(" ", $output['message']['reply_to_message']);
    preg_match_all($str, $origMess, $valid);

    foreach($valid[0] as $mail){
       $origEmail = $mail;
    }

     $subject = implode(" ", $output['message']['reply_to_message']); 
     $subject = substr($subject, strpos($subject, $hsubject) + strlen($hsubject), strpos($subject, $hmessage) - strpos($subject, $hsubject) - strlen($hmessage));


     sendMessage($chat_id, $hrecipient . $origEmail . chr(10) . $hsubject . $subject . chr(10) . $hmessage .  $message . chr(10) . chr(10) . $cpromt, $replyMarkup);



function sendMessage($chat_id, $message, $replyMarkup) {
  file_get_contents($GLOBALS['api'] . '/sendMessage?chat_id=' . $chat_id . '&text=' . urlencode($message) . '&reply_markup=' . $replyMarkup . '&parse_mode=html');
}

function sendMessageR($chat_id, $message) {
  file_get_contents($GLOBALS['api'] . '/sendMessage?chat_id=' . $chat_id . '&text=' . urlencode($message) );
}

?>