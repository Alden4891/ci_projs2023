<?php
/**
 * Copyright 2018 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
// [START gmail_quickstart]
require __DIR__ . '/vendor/autoload.php';

// if (php_sapi_name() != 'cli') {
//     throw new Exception('This application must be run on the command line.');
// }

use Google\Client;
use Google\Service\Gmail;

/**
 * Returns an authorized API client.
 * @return Client the authorized client object
 */
function getClient()
{
    $client = new Client();
    $client->setApplicationName('Gmail API PHP Quickstart');
    // $client->setScopes('https://www.googleapis.com/auth/gmail.addons.current.message.readonly');
    $client->setScopes('https://www.googleapis.com/auth/gmail.send');
    $client->setAuthConfig('credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    // Load previously authorized token from a file, if it exists.
    // The file token.json stores the user's access and refresh tokens, and is
    // created automatically when the authorization flow completes for the first
    // time.
    $tokenPath = 'token-sendmail.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    // If there is no previous token or it's expired.
    if ($client->isAccessTokenExpired()) {
        // Refresh the token if possible, else fetch a new one.
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            // Check to see if there was an error.
            if (array_key_exists('error', $accessToken)) {
                throw new Exception(join(', ', $accessToken));
            }
        }
        // Save the token to a file.
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    }
    return $client;
}


// Get the API client and construct the service object.
$client = getClient();
$service = new Gmail($client);

//https://mailtrap.io/blog/send-emails-with-gmail-api/

/**
* @param $sender string sender email address
* @param $to string recipient email address
* @param $subject string email subject
* @param $messageText string email text
* @return Google_Service_Gmail_Message
*/
function createMessage($sender, $to, $subject, $messageText) {
 $message = new Google_Service_Gmail_Message();
 $rawMessageString = "From: <{$sender}>\r\n";
 $rawMessageString .= "To: <{$to}>\r\n";
 $rawMessageString .= 'Subject: =?utf-8?B?' . base64_encode($subject) . "?=\r\n";
 $rawMessageString .= "MIME-Version: 1.0\r\n";
 $rawMessageString .= "Content-Type: text/html; charset=utf-8\r\n";
 $rawMessageString .= 'Content-Transfer-Encoding: quoted-printable' . "\r\n\r\n";
 $rawMessageString .= "{$messageText}\r\n";
 $rawMessage = strtr(base64_encode($rawMessageString), array('+' => '-', '/' => '_'));
 $message->setRaw($rawMessage);


        foreach ($files as $key => $filePath) {
            if($filePath!=""){
                $array = explode('/', $filePath);
                $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
                $mimeType = finfo_file($finfo, $filePath);
                $fileName = $array[sizeof($array)-1];
                $fileData = base64_encode(file_get_contents($filePath));

                $strRawMessage .= "\r\n--{$boundary}\r\n";
                $strRawMessage .= 'Content-Type: '. $mimeType .'; name="'. $fileName .'";' . "\r\n";            
                $strRawMessage .= 'Content-ID: <' . $sentMailData->email. '>' . "\r\n";            
                $strRawMessage .= 'Content-Description: ' . $fileName . ';' . "\r\n";
                $strRawMessage .= 'Content-Disposition: attachment; filename="' . $fileName . '"; size=' . filesize($filePath). ';' . "\r\n";
                $strRawMessage .= 'Content-Transfer-Encoding: base64' . "\r\n\r\n";
                $strRawMessage .= chunk_split(base64_encode(file_get_contents($filePath)), 76, "\n") . "\r\n";
                $strRawMessage .= "--{$boundary}\r\n";
            }
        }


 return $message;
}

/**
* @param $service Google_Service_Gmail an authorized Gmail API service instance.
* @param $user string User's email address or "me"
* @param $message Google_Service_Gmail_Message
* @return Google_Service_Gmail_Draft
*/
function createDraft($service, $user, $message) {
 $draft = new Google_Service_Gmail_Draft();
 $draft->setMessage($message);
 try {
   $draft = $service->users_drafts->create($user, $draft);
   print 'Draft ID: ' . $draft->getId();
 } catch (Exception $e) {
   print 'An error occurred: ' . $e->getMessage();
 }
 return $draft;
}


//send message
/**
* @param $service Google_Service_Gmail an authorized Gmail API service instance.
* @param $userId string User's email address or "me"
* @param $message Google_Service_Gmail_Message
* @return null|Google_Service_Gmail_Message
*/
function sendMessage($service, $userId, $message) {
 try {
   $message = $service->users_messages->send($userId, $message);
   print 'Message with ID: ' . $message->getId() . ' sent.';
   return $message;
 } catch (Exception $e) {
   print 'An error occurred: ' . $e->getMessage();
 }
 return null;
}


$msg = createMessage("aaquinones.fo12@dswd.gov.ph", "alden.roxy@gmail.com", "test subject", "this is a test!");
sendMessage($service,"me",$msg);

// [END gmail_quickstart]