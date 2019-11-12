<?php


namespace App\Entity;


class Calendar
{
    public function getClient(){
        //$client -> setDeveloperKey ( " YOUR_APP_KEY " );
        $client = new \Google_Client();
        $client -> setApplicationName ( "test1" );
        $client->setAuthConfig('./client_id.json ');
        $client->addScope(\Google_Service_Calendar::CALENDAR);

        $guzzleClient = new \GuzzleHttp\Client(array( 'curl' => array( CURLOPT_SSL_VERIFYPEER => true, ), ));
        $client->setHttpClient($guzzleClient);

        $redirect_uri = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
        $client->setRedirectUri($redirect_uri);

        if(isset($_GET['mr2Ws6bLZG3A09S31P4-IC8d'])){
            $token = $client->fetchAccessTokenWithAuthCode($_GET['mr2Ws6bLZG3A09S31P4-IC8d']); //code de vérification
        }
        return $client;
    }
}