<?php

require __DIR__ . '/../vendor/autoload.php';

$scopes = [
    Google_Service_Gmail::GMAIL_READONLY,
    Google_Service_Gmail::GMAIL_COMPOSE,
    Google_Service_Gmail::GMAIL_MODIFY,
];


$client = new Google_Client();
$client->setApplicationName('Gmail API PHP');
$client->setScopes($scopes);
$client->setAuthConfig('credentials.json');
$client->setAccessType('offline');
$client->setPrompt('select_account consent');

if (file_exists('token_read_compose_modify.json')) {
    $accessToken = json_decode(file_get_contents('token_read_compose_modify.json'), true);
    if ($client->isAccessTokenExpired()) {
        $client->fetchAccessTokenWithRefreshToken($accessToken['refresh_token']);
        $accessToken = $client->getAccessToken();
        file_put_contents('token_read_compose_modify.json', json_encode($accessToken));
    }
} else {
    $authUrl = $client->createAuthUrl();
    printf("Open the following link in your browser:\n%s\n", $authUrl);
    print 'Enter verification code: ';
    $authCode = trim(fgets(STDIN));
    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
    file_put_contents('token_read_compose_modify.json', json_encode($accessToken));
}
$client->setAccessToken($accessToken);
$service = new Google_Service_Gmail($client);