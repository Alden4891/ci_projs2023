<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/google-api/vendor/autoload.php';

class gmail_model extends CI_Model {

    private $client;

    public function __construct()
    {
        parent::__construct();

        $this->client = new Google_Client();
        $this->client->setApplicationName('Gmail API PHP Quickstart');
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
    }

    ## EXPERIMENTAL UDFs -------------------------------------------------------------------------------------

    public function add_label($labelName) {
        //WARNING: REQUIRES MODIFY
        // Set the name and label list visibility of the new label
        $labelVisibility = 'labelShow';

        // Create a Label object with the label name and label visibility
        $newLabel = new Google_Service_Gmail_Label();
        $newLabel->setName($labelName);
        $newLabel->setVisibility($labelVisibility);

        // Call the users.labels.create method to create the new label
        $createdLabel = $this->service->users_labels->create('me', $newLabel);

        // Output the ID and name of the created label
        // echo 'Label created with ID: ' . $createdLabel->getId() . ' and name: ' . $createdLabel->getName();    
        return $createdLabel->getId(); 
    }

    public function set_label($messageId,$labelId) {
        //WARNING: REQUIRES MODIFY
        // Set the ID of the message you want to modify and the ID of the label you want to apply
        // $messageId = 'MESSAGE_ID_HERE';
        // $labelId = 'LABEL_ID_HERE';

        // Create a ModifyMessageRequest object to specify the label to add
        $modifyRequest = new Google_Service_Gmail_ModifyMessageRequest();
        $modifyRequest->setAddLabelIds(array($labelId));

        // Call the messages.modify method to add the label to the message
        $this->service->users_messages->modify('me', $messageId, $modifyRequest);
    }

    public function delete_email($messageId) {
        $this->service->users_messages->trash("me", $message_id);
    }
    ## WORKING UDFs ------------------------------------------------------------------------------------------


    public function send_email($to, $subject, $body, $attachments = array()){
        $service = new Google_Service_Gmail($this->client);

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
        $send_message = $service->users_messages->send("me", $message);

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
        $service = new Google_Service_Gmail($this->client);
        $thread = $service->users_threads->get('me', $thread_id);
        $messages = $thread->getMessages();

        foreach ($messages as $message) {
            $arr_attachment = array();

            $messageId = $message->getId();
            $message = $service->users_messages->get('me', $messageId);
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
                        $attachment = $service->users_messages_attachments->get('me', $messageId, $attachmentId);

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
