<?php

$gmail = new Google_Service_Gmail($client);
$message = new Google_Service_Gmail_Message();
$optParam = array();
$referenceId = '';
$thread = $gmail->users_threads->get($userId, $threadId);
$optParam['threadId'] = $threadId;
$threadMessages = $thread->getMessages($optParam);
$messageId = $threadMessages[0]->getId();
$messageDetails = $this->getMessageDetails($messageId); //omitted for simplicity: returns prepared message data.
$messageDetails = $messageDetails['data'];
$subject = $messageDetails['headers']['Subject'];
$mail = new PHPMailer();
$mail->CharSet = 'UTF-8';
$mail->From = $from_email;
$mail->FromName = $from_name;
$mail->addAddress($to);
$mail->Subject = $subject;
$mail->Body = $body;
$mail->preSend();
$mime = $mail->getSentMIMEMessage();
$raw = $this->Base64UrlEncode($mime); //omitted for simplicity: Encodes the data in base 64 format for sending.
$message->setRaw($raw);
$message->setThreadId($threadId);
$response = $gmail->users_messages->send($userId, $message);

?>