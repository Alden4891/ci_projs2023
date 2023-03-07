<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/google-api/vendor/autoload.php';

class gmail_model extends CI_Model {

    private $client;

    public function __construct()
    {
        parent::__construct();

        $this->client = new Google_Client();
        $this->client->setApplicationName('APP NAME');
        $this->client->setAuthConfig(APPPATH . 'libraries/google-client-secret.json');
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');

       // Add the required scope to access all user data.
        $this->client->addScope(Google_Service_Gmail::GMAIL_READONLY);
        $this->client->addScope(Google_Service_Oauth2::USERINFO_PROFILE);
        $this->client->addScope(Google_Service_Oauth2::USERINFO_EMAIL);

        // Load previously authorized credentials from a file.
        $tokenPath = APPPATH . 'libraries/token_read_compose_modify.json';
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $this->client->setAccessToken($accessToken);
        }

        // If there is no previous token or it's expired.
        if ($this->client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if ($this->client->getRefreshToken()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            } else {
                $authUrl = $this->client->createAuthUrl();
                // Redirect the user to Google's authorization page to authorize the application.
                header("Location: $authUrl");
                // exit;
            }
            // Save the token to a file.
            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));
        }

        $this->service = new Google_Service_Gmail($this->client);
    }

    ## EXPERIMENTAL UDFs -------------------------------------------------------------------------------------

function get_email_by_id($emailId) {
   

    $message = $this->service->users_messages->get('me', $emailId, ['format' => 'full']);
    $payload = $message->getPayload();
    $headers = $payload->getHeaders();
    $parts = $payload->getParts();
    $dateSent = '';
    $toEmail = '';
    $fromEmail = '';
    $subject = '';
    $snippet = $message->getSnippet();
    $textBody = '';
    $htmlBody = '';
    $attachments = [];

    foreach ($headers as $header) {
        $name = $header->getName();
        $value = $header->getValue();
        
        if ($name == 'Date') {
            $dateSent = $value;
        } else if ($name == 'To') {
            $toEmail = $value;
        } else if ($name == 'From') {
            $fromEmail = $value;
        } else if ($name == 'Subject') {
            $subject = $value;
        }
    }

    if (count($parts) == 0) {
        $textBody = $payload->getBody()->getData();
    } else {
        foreach ($parts as $part) {
            $partHeaders = $part->getHeaders();
            $partName = $part->getFilename();
            $partMimeType = $part->getMimeType();
            $partBody = $part->getBody();

            if ($partName != '' || $partMimeType != '') {
                $attachments[] = [
                    'name' => $partName,
                    'mime_type' => $partMimeType,
                    'data' => $partBody,
                ];
            } else if ($part->getParts() && count($part->getParts()) > 0) {
                foreach ($part->getParts() as $subpart) {
                    if ($subpart->getMimeType() == 'text/plain') {
                        $textBody = $subpart->getBody()->getData();
                    } else if ($subpart->getMimeType() == 'text/html') {
                        $htmlBody = $subpart->getBody()->getData();
                    }
                }
            } else if ($part->getMimeType() == 'text/plain' && empty($textBody)) {
                $textBody = $partBody->getData();
            } else if ($part->getMimeType() == 'text/html' && empty($htmlBody)) {
                $htmlBody = $partBody->getData();
            }
        }
    }

    $decodedTextBody = base64_decode(str_replace(['-', '_'], ['+', '/'], $textBody));
    $decodedHtmlBody = base64_decode(str_replace(['-', '_'], ['+', '/'], $htmlBody));

    return [
        'message_id' => $message->getId(),
        'date_sent' => $dateSent,
        'to_email' => $toEmail,
        'from_email' => $fromEmail,
        'subject' => $subject,
        'snippet' => $snippet,
        'body' => $decodedHtmlBody,
        'attachments' => $attachments,
    ];
}


// public function get_email_by_id2($emailId) {
//     $message =$this->service->users_messages->get('me', $emailId, ['format' => 'full']);
//     $payload = $message->getPayload();
//     $headers = $payload->getHeaders();
//     $parts = $payload->getParts();
//     $dateSent = '';
//     $toEmail = '';
//     $fromEmail = '';
//     $subject = '';
//     $snippet = $message->getSnippet();
//     $body = '';
//     $attachments = [];

//     foreach ($headers as $header) {
//         $name = $header->getName();
//         $value = $header->getValue();
        
//         if ($name == 'Date') {
//             $dateSent = $value;
//         } else if ($name == 'To') {
//             $toEmail = $value;
//         } else if ($name == 'From') {
//             $fromEmail = $value;
//         } else if ($name == 'Subject') {
//             $subject = $value;
//         }
//     }

//     if (count($parts) == 0) {
//         $body = $payload->getBody()->getData();
//     } else {
//         foreach ($parts as $part) {
//             $partHeaders = $part->getHeaders();
//             $partName = $part->getFilename();
//             $partMimeType = $part->getMimeType();
//             $partBody = $part->getBody();

//             if ($partName != '' || $partMimeType != '') {
//                 $attachments[] = [
//                     'name' => $partName,
//                     'mime_type' => $partMimeType,
//                     'data' => $partBody,
//                 ];
//             } else if ($part->getParts() && count($part->getParts()) > 0) {
//                 foreach ($part->getParts() as $subpart) {
//                     if ($subpart->getMimeType() == 'text/plain') {
//                         $body = $subpart->getBody()->getData();
//                         break;
//                     }
//                 }
//             } else if ($part->getMimeType() == 'text/plain') {
//                 $body = $part->getBody()->getData();
//                 break;
//             }
//         }
//     }

//     $decodedBody = base64_decode(str_replace(['-', '_'], ['+', '/'], $body));

//     return [
//         'message_id' => $message->getId(),
//         'date_sent' => $dateSent,
//         'to_email' => $toEmail,
//         'from_email' => $fromEmail,
//         'subject' => $subject,
//         'snippet' => $snippet,
//         'body' => $decodedBody,
//         'attachments' => $attachments,
//     ];
// }


    ## WORKING UDFs ------------------------------------------------------------------------------------------


    public function get_mails($query = 'is:sent'){

        // Retrieve sent emails
        $userId = 'me';
        $results = $this->service->users_messages->listUsersMessages($userId, ['q' => $query]);

        // Create array to store email information
        $emails = [];

        // Loop through the 10 most recent sent emails
        $count = 0;
        foreach ($results->getMessages() as $message) {
          $message = $this->service->users_messages->get($userId, $message->id);
          $payload = $message->getPayload();
          $threadId = $message->getThreadId();
          $message_id = $message->getId();


          //display only the original sent message
          // if ($threadId !== $message_id || $message_id !== '') {
          //   continue;
          // }

          $headers = $payload->getHeaders();
          $subject = '';
          $message_id = '';
          $date_sent = '';
          $label_name = '';
          $status = '';
          $labelIds = $message->getLabelIds();
          foreach ($labelIds as $labelId) {
            $label = $this->service->users_labels->get($userId, $labelId);
            $label_name = $label->getName();
          }
          foreach ($headers as $header) {
            if ($header->name == 'Subject') {
              $subject = $header->value;
            }
            // if ($header->name == 'Message-ID') {
            //   $message_id = $header->value;
            // }
            if ($header->name == 'Date') {
              $date_sent = date('Y-m-d H:i:s', strtotime($header->value));
            }
          }

         

          $messageData = $this->service->users_messages->get($userId, $message->getId(), ['format' => 'full']);
          $headers = $messageData->getPayload()->getHeaders();
          foreach ($headers as $header) {
            if ($header->name == 'From') {
              $from = $header->value;
            }
            if ($header->name == 'To') {
              $to = $header->value;
            }
            if ($header->name == 'Date') {
              $date = $header->value;
            }
            if ($header->name == 'Subject') {
              $subject = $header->value;
            }
          }
          $labelIds = $message->getLabelIds();
          if (in_array('UNREAD', $labelIds)) {
            $status = 'Unread';
          } else {
            $status = 'Read';
          }

          // Count number of replies
          
          $thread = $this->service->users_threads->get($userId, $threadId, ['format' => 'full']);
          if ($thread->getMessages() != null) {
            $num_replies = count($thread->getMessages()) - 1;
          }

          // Add email information to array
          $emails[] = [
            'subject' => $subject,
            'message_id' => $message_id,
            'threadId' => $threadId,
            'date_sent' => $date_sent,
            'label_name' => $label_name,
            'status' => $status,
            'num_replies' => $num_replies
          ];
          $count++;
          if ($count >= 30) {
            break;
          }
        }
        return $emails; // Return the emails array outside of the foreach loop
    }



    public function delete_email($message_id) {
        return $this->service->users_messages->trash("me", $message_id);
    }

    public function getLabels(){
        
        $labels = $this->service->users_labels->listUsersLabels('me');

        // Loop through the labels and create an array of label IDs and captions
        $label_array = array();
        foreach ($labels as $label) {
            $label_array[$label->getId()] = $label->getName();
        }
        return $label_array;
    }

    public function set_label($messageId,$labelId ) {
       
     
        // Create a ModifyMessageRequest object to specify the label to add
        $modifyRequest = new Google_Service_Gmail_ModifyMessageRequest();
        $modifyRequest->setAddLabelIds(array($labelId));

        // Call the messages.modify method to add the label to the message
        $this->service->users_messages->modify('me', $messageId, $modifyRequest);
    }

    public function add_label($labelName,$userId = "me") {

       
        //WARNING: REQUIRES MODIFY
        // Create the label
        $label = new Google_Service_Gmail_Label();
        $label->setName($labelName);

        $createdLabel = $this->service->users_labels->create($userId, $label);

        return $createdLabel->getId();
    }

    public function send_email($to, $subject, $body, $attachments = array()){


        // Replace with the email address of the sender.
        $from = 'aaquinones.fo12@dswd.gov.ph';

        // Create the message with the attachments.
        $message = new Google_Service_Gmail_Message();
        $boundary = uniqid(rand(), true);
        $message_text = "MIME-Version: 1.0\n";
        $message_text .= "From: " . $from . "\n";
        $message_text .= "To: " . $to . "\n";
        $message_text .= "Subject: " . $subject . "\n";
        $message_text .= "Content-Type: multipart/mixed; boundary=" . $boundary . "\n\n";
        $message_text .= "--" . $boundary . "\n";
        $message_text .= "Content-Type: text/plain; charset=UTF-8\n";
        $message_text .= "Content-Transfer-Encoding: 7bit\n\n";
        $message_text .= $body . "\n\n";

        foreach ($attachments as $attachment) {
            $filename = basename($attachment);
            $filesize = filesize($attachment);
            $filetype = mime_content_type($attachment);
            $filedata = base64_encode(file_get_contents($attachment));
            $message_text .= "--" . $boundary . "\n";
            $message_text .= "Content-Type: " . $filetype . "; name=\"" . $filename . "\"\n";
            $message_text .= "Content-Description: " . $filename . "\n";
            $message_text .= "Content-Disposition: attachment; filename=\"" . $filename . "\"; size=" . $filesize . ";\n";
            $message_text .= "Content-Transfer-Encoding: base64\n\n";
            $message_text .= $filedata . "\n\n";
        }

        $message_text .= "--" . $boundary . "--";

        // Encode the message in base64 and send it.
        $message->setRaw(base64_encode($message_text));
        $send_message = $this->service->users_messages->send("me", $message);

        return $send_message->getId();
    }

    function get_formatted_body($message) {
        $parts = $message->getPayload()->getParts();
        if (empty($parts)) {
            $body = base64_decode(strtr($message->getPayload()->body->data, '-_', '+/'));
        } else {
            $body = base64_decode(strtr($parts[1]->body->data, '-_', '+/'));
        }
        return $body;
    }

    public function get_email_by_threadID($thread_id) {
        $ds = array();
        $thread = $this->service->users_threads->get('me', $thread_id);
        $messages = $thread->getMessages();

        foreach ($messages as $message) {
            $arr_attachment = array();

            $messageId = $message->getId();
            $message = $this->service->users_messages->get('me', $messageId);
            $headers = $message->getPayload()->getHeaders();

            $msgId = $message->id;
            $fbody = $this->get_formatted_body($message);

            $from = '';
            $subject = '';
            foreach ($headers as $header) {
                if ($header->getName() == 'From') {
                    $from = $header->getValue();
                    // break;
                }
                if ($header->getName() == 'Subject') {
                    $subject = $header->getValue();
                    // break;
                }
            }

            // Get the name and email address of the sender
            $senderName = "me";
            $senderEmail = "me";
            $headers = $message->getPayload()->getHeaders();
            // var_dump($headers);
            $fromHeader = array_filter($headers, function($header) {
                return $header->name === 'From';
            });
            $fromValue = reset($fromHeader)->value;
            $fromRegex = '/(.*) <([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})>/';
            if (preg_match($fromRegex, $fromValue, $matches)) {
                $senderName = $matches[1];
                $senderEmail = $matches[2];
            } else {
                // echo "No match found\n";
            }

            // Loop through the parts of the message to find any attachments
            $hasAttachments = false;
            if (is_array($message->getPayload()->getParts())) {
                foreach ($message->getPayload()->getParts() as $part) {
                    if ($part->getFilename() && $part->getBody()) {
                        $hasAttachments = true;

                        // Get the attachment data using the Gmail API
                        $attachmentId = $part->getBody()->getAttachmentId();
                        $attachment = $this->service->users_messages_attachments->get('me', $messageId, $attachmentId);

                        // Get the attachment data and decode it
                        $attachmentData = base64_decode($attachment->getData());
                        // $decodedData = base64_decode($attachmentData);
                        $filename = $part->getFilename();
                        $contentType = $part->getMimeType();       

                        $dl_path = SITE_URL("attachments/".$messageId."_".$filename);
                        array_push($arr_attachment,array(
                              "attachmentId" => $attachmentId
                            , "filename" => $filename
                            , "dl_filename" => $messageId."_".$filename
                            , "dl_link" => "<a href = \"$dl_path\" >$dl_path</a>"
                            , "mime_type" => $contentType
                        ));

                        // Write the attachment data to a file  
                        $dl_path = "attachments/".$messageId."_".$filename;
                        if (!file_exists($dl_path)) {
                           $handle = fopen($dl_path, 'wb');
                           fwrite($handle, base64_decode(strtr($attachment->getData(), '-_', '+/')));          
                           fclose($handle);                        
                        }
     
                    }   
                }       
            }
            if ($hasAttachments) {
                // echo 'The email has attachments.<br>';
            } else {
                // echo 'The email does not have any attachments. <br>';
            }

            array_push($ds,array(
                      "messageId" => $messageId
                    , "subject" => $subject
                    , "senderName" => $senderName
                    , "senderEmail" => $senderEmail
                    , "formattedBody" => $fbody
                    , "has_attachments" => ($hasAttachments == true ? 1 : 0)
                    , "attachments" => $arr_attachment

            ));
        }
        return $ds;
    }
}
