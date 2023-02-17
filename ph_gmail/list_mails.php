<?php
$mail_id = '1860574830b4fd26';
require __DIR__ . '/vendor/autoload.php';


use Google\Client;
use Google\Service\Gmail;

function getClient()
{
    $client = new Client();
    $client->setApplicationName('Gmail API PHP Quickstart');
    // $client->setScopes('https://www.googleapis.com/auth/gmail.addons.current.message.readonly');
    $client->setScopes('https://www.googleapis.com/auth/gmail.readonly');
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


// Get the API client and construct the service object.
$client = getClient();
$service = new Gmail($client);

$list = $service->users_messages->listUsersMessages('me', [
    'maxResults' => 10,
    'q' => 'test'
]);

$messageList = $list->getMessages();
$inboxMessage = [];

foreach($messageList as $mlist) {
    $optParamsGet2['format'] = 'full';
    $single_message = $service->users_messages->get('me', $mlist->id, $optParamsGet2);

    $message_id = $mlist->id;
    $headers = $single_message->getPayload()->getHeaders();
    $snippet = $single_message->getSnippet();


    foreach($headers as $single) {


            // print_r($single);

        if ($single->getName() == 'Subject') {
            $message_subject = $single->getValue();
            // print($message_subject);print('<br>');

        } elseif ($single->getName() == 'Date') {
            $message_date = $single->getValue();
            $message_date = date('M jS Y h:i A', strtotime($message_date));
        } elseif ($single->getName() == 'From') {
            $message_sender = $single->getValue();
            $message_sender = str_replace('"', '', $message_sender);
        }
    }


    if ($single->threadId = '1860574830b4fd26') {

        $inboxMessage[] = [
            'messageId' => $message_id,
            'messageSnippet' => $snippet,
            'messageSubject' => $message_subject,
            'messageDate' => $message_date,
            'messageSender' => $message_sender,
            'threadId' => $single->threadId
        ];
    }


}

        print('<hr><pre>');
        print_r($inboxMessage);    
        print('</pre>');
    

