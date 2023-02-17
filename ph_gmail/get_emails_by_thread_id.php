

<?php
$mail_id = '1860574830b4fd26';
// $mail_id = '18605d106283800e';

require __DIR__ . '/vendor/autoload.php';


use Google\Client;
use Google\Service\Gmail;

function getClient(){
    
    $client = new Client();
    $client->setApplicationName('Gmail API PHP Quickstart');
    // $client->setScopes('https://www.googleapis.com/auth/gmail.addons.current.message.readonly');
    $client->setScopes(['https://www.googleapis.com/auth/gmail.readonly']);
    $client->setAuthConfig('credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    // Load previously authorized token from a file, if it exists.
    // The file token.json stores the user's access and refresh tokens, and is
    // created automatically when the authorization flow completes for the first
    // time.
    $tokenPath = 'token.json';
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

function get_formatted_body($message) {
    $parts = $message->getPayload()->getParts();
    if (empty($parts)) {
        $body = base64_decode(strtr($message->getPayload()->body->data, '-_', '+/'));
    } else {
        $body = base64_decode(strtr($parts[1]->body->data, '-_', '+/'));
    }
    return $body;
}

// Get the API client and construct the service object.
$client = getClient();
$service = new Gmail($client);

// Get the thread details
$thread = $service->users_threads->get('me', $mail_id);

// Get the messages in the thread
$messages = $thread->getMessages();

// Loop through each message in the thread
foreach ($messages as $message) {
    // Get the message ID
    $messageId = $message->getId();
    
    // Get the message details
    $message = $service->users_messages->get('me', $messageId);
    
    // Get the message headers
    $headers = $message->getPayload()->getHeaders();

    $msgId = $message->id;
    $fbody = get_formatted_body($message);

    print('<pre>');
    print("$msgId <br><br> $fbody");
    print('</pre>'); 


// Get the sender email address
    $from = '';
    foreach ($headers as $header) {
        if ($header->getName() == 'From') {
            $from = $header->getValue();
            break;
        }
    }
    print("Sender: $from");


    // Loop through the parts of the message to find any attachments
    $hasAttachments = false;
    if (is_array($message->getPayload()->getParts())) {
        foreach ($message->getPayload()->getParts() as $part) {
            if ($part->getFilename() && $part->getBody()) {
                $hasAttachments = true;

                // Get the attachment data and decode it
                $attachmentData = $part->getBody()->getData();
                $decodedData = base64_decode($attachmentData);
                $filename = $part->getFilename();
                $attachmentId = $part->getBody()->getAttachmentId();


                print("filename: $filename <br>");
                // print("attachmentId: $attachmentId <br>");

                // Get the attachment data using the Gmail API
                $attachment = $service->users_messages_attachments->get('me', $messageId, $attachmentId);

                // Generate a download link for the attachment
                // $downloadUrl = 'https://www.googleapis.com/gmail/v1/users/me/messages/' . $messageId . '/attachments/' . $attachmentId;

                // print("$filename - [$attachment]<br>");

                // echo 'Download link: ' . $downloadUrl;

            }
        }        
    }
    if ($hasAttachments) {
        echo 'The email has attachments.<br>';
    } else {
        echo 'The email does not have any attachments. <br>';
    }
    print('<hr>');

}
