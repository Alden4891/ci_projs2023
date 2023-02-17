<?php
// Load Gmail API library
require_once __DIR__ . '/vendor/autoload.php';

use Google\Client;
use Google\Service\Gmail;


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

function get_mime_type($file_path) {
    // Create a new fileinfo resource
    $finfo = finfo_open(FILEINFO_MIME_TYPE);

    // Get the MIME type of the file
    $mime_type = finfo_file($finfo, $file_path);

    // Close the fileinfo resource
    finfo_close($finfo);

    return $mime_type;
}


function get_filename_from_path($file_path) {
	$array = explode('/', $file_path);
	$fileName = $array[sizeof($array)-1];
	return $fileName;
}

// Set up client credentials
$client = getClient();

// $client->setAuthConfig('path/to/client_secret.json');
// $client->setScopes(['https://www.googleapis.com/auth/gmail.send']);

// Set up Gmail API service
$service = new Gmail($client);

// Set up message details
$message = new Google_Service_Gmail_Message();
$message->setTo(['alden.roxy@gmail.com.com']);
$message->setSubject('Email with attachments using Gmail API');
$message->setBody('This is a test email sent using Gmail API with attachments.');


$files = array("C:/xampp/htdocs/ci_drout/uploads/file1.jpg"
	          ,"C:/xampp/htdocs/ci_drout/uploads/file2.jpg");


// Attach file 1
$file1 = 'path/to/attachment1.pdf';
$attachment1 = new Google_Service_Gmail_MessagePart();
$attachment1->setContentType(get_mime_type($files[0]));
$attachment1->setFilename('attachment1.pdf');
$attachment1->setDisposition('attachment');
$attachment1->setData(base64_encode(file_get_contents($file1)));
$messagePart1 = new Google_Service_Gmail_MessagePart();
$messagePart1->setParts([$attachment1]);
$messagePart1->setMimeType('multipart/related');
$message->setPayload($messagePart1);

print($attachment1);

// Attach file 2
$file2 = 'path/to/attachment2.jpg';
$attachment2 = new Google_Service_Gmail_MessagePart();
$attachment2->setContentType(get_mime_type($files[1]));
$attachment2->setFilename('attachment2.jpg');
$attachment2->setDisposition('attachment');
$attachment2->setData(base64_encode(file_get_contents($file2)));
$messagePart2 = new Google_Service_Gmail_MessagePart();
$messagePart2->setParts([$attachment2]);
$messagePart2->setMimeType('multipart/related');
$message->getPayload()->setParts(array($messagePart1, $messagePart2));

// Encode and send message
$rawMessage = base64_encode($message->toJSON());
// $sendmessage = $service->users_messages->send("me", new Google_Service_Gmail_Message(['raw' => $rawMessage]));

// Output result
// echo "Email sent with attachments successfully!";
?>