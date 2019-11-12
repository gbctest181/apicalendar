<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use Google_Service_Calendar;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



/**
 * @Route("/client")
 */
class ClientController extends AbstractController
{
        /**
         * @Route("/", name="client_index", methods={"GET"})
         */
        public function index(): Response
    {

        function getClient(){
            $code = "4/swE_DbBTA4XI6aN4rqr4xq-9yU3uhExkdfR1DwNcdcyv3G678vYPsZE";
            $developperKey = "mr2Ws6bLZG3A09S31P4-IC8d";

            $client = new \Google_Client();
            //$client -> setApplicationName ( "testgbc" );
            $client ->setDeveloperKey("$developperKey");
            $client->setAuthConfig('./client_id.json ');
            //$client->addScope(\Google_Service_Calendar::CALENDAR);
            $client->setAccessType('offline');
//________________________________________________test_authentification provisoire__________________________
            $client->setApplicationName('Google Calendar API PHP Quickstart');
            $client->setScopes((array)Google_Service_Calendar::CALENDAR);
           // $client->setAuthConfig('credentials.json');
            //$client->setAccessType('offline');
            $client->setPrompt('sélectionner le consentement du compte');
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
                    printf("Ouvrez le lien dans votre navigateur:"."<br/>"."\n%s\n", $authUrl);
                    print "<br/<".'Entrer le code de vérification : ';
                    //$authCode = trim(fgets(STDIN));
                    $authCode ="4/swFOPF_4xvA7Os2jkHNlRPwrrrNLEujrMmpvpvxOv2UFUOdTbeixbIU";

                    // Exchange authorization code for an access token.
                    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
                    $client->setAccessToken($accessToken);

                }
                // Save the token to a file.
                if (!file_exists(dirname($tokenPath))) {
                    mkdir(dirname($tokenPath), 0700, true);
                }
                file_put_contents($tokenPath, json_encode($client->getAccessToken()));
            }
//-----------------07/12 code 4/swE_DbBTA4XI6aN4rqr4xq-9yU3uhExkdfR1DwNcdcyv3G678vYPsZE
//_______________________________________________________________________________
            $redirect_uri = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
            $client->setRedirectUri($redirect_uri);
            if(isset($_GET[$code]))
            {
                $client->fetchAccessTokenWithAuthCode($_GET[$code]); //code de vérification
            }
            return $client;
        }

        // Get the API client and construct the service object.
        $client = getClient();
        $service = new Google_Service_Calendar($client);

        // Print the next 10 events on the user's calendar.
        $calendarId = 'primary';
        $optParams = array(

            'maxResults' => 20,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => date('c')

        );
        $results = $service->events->listEvents($calendarId, $optParams);
        $events = $results->getItems();

        if (empty($events)) {
            print "Pas d'événement.\n";
        } else {
            print "Prochains événements:\n";
            foreach ($events as $event) {
                $start = $event->start->dateTime;
                if (empty($start)) {
                    $start = $event->start->date;
                }
                printf("%s (%s)\n", $event->getSummary(), $start);
            }
        }
        return $this->render('client/index.html.twig', [
            'service' => $service,
            'client' => $client,
            'optparam' => $optParams,
            'events' =>$events,

        ]);
    }

    /**
     * @Route("/new", name="client_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($client);
            $entityManager->flush();

            return $this->redirectToRoute('client_index');
        }
        return $this->render('client/new.html.twig', [
            'client' => $client,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="client_show", methods={"GET"})
     */
    public function show(Client $client): Response
    {
        return $this->render('client/show.html.twig', [
            'client' => $client,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="client_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Client $client): Response
    {
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('client_index');
        }

        return $this->render('client/edit.html.twig', [
            'client' => $client,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="client_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Client $client): Response
    {
        if ($this->isCsrfTokenValid('delete'.$client->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($client);
            $entityManager->flush();
        }

        return $this->redirectToRoute('client_index');
    }
}
