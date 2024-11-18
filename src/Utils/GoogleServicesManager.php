<?php

namespace SWD\MadridBundle\Utils;

use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Servicio que engloba un conjunto de funciones auxiliares para el manejo de los servicios de
 * Google
 *
 * @author Álvaro Peláez Santana
 * @copyright ALUNI MADRID S.L.
 */
class GoogleServicesManager {

    protected $session, $em, $conn, $dir;

    public function __construct($dir, EntityManagerInterface $em, SessionInterface $session) {
        $this->em = $em;
        $this->conn = $em->getConnection();
        $this->session = $session;
        $this->dir = $dir;
    }

    /**
     * Petición de  eventos a Google Calendars
     */
    public function getEventosCalendar($email) {
        //El alias de la aplicación es muy importante, también es el de la carpeta donde se guardan y buscán las credenciales para dicha
        //aplicación
        $datosCliente = ['nombreApp' => "Eventos ALUNI.net",
            'alias' => 'calendar',
            'scopes' => implode(' ', array(Google_Service_Calendar::CALENDAR)),
            'usuario' => $email,
            'route' => 'anadir_evento_calendario'];
        $client = $this->getClient($datosCliente);
        if (!$client instanceof Google_Client) {
            return [];
        } else {
            $service = new Google_Service_Calendar($client);
            return $this->getEventos($service);
        }
    }

    function getClient($datosCliente) {
        $client = new Google_Client();
        $client->setApplicationName($datosCliente['nombreApp']);
        $client->setScopes($datosCliente['scopes']);
        $client->setAuthConfigFile($this->dir . '/' . $datosCliente['alias'] . '_client_secret.json');
        $client->setAccessType('offline');
        $client->setApprovalPrompt('force');
        // Load previously authorized credentials from a file.
        $ficheroCredenciales = $this->dir . '/credenciales/' . $datosCliente['alias'] . '/' . $datosCliente['usuario'] . '.json';
        $credentialsPath = $this->expandHomeDirectory($ficheroCredenciales);
        if (file_exists($credentialsPath)) {
            $accessToken = json_decode(file_get_contents($credentialsPath), true);
        } else {
            // Request authorization from the user.
            return $client->createAuthUrl();
        }
        $client->setAccessToken($accessToken);
        // Refresh the token if it's expired.
        if ($client->isAccessTokenExpired()) {
            $refreshTokenSaved = $client->getRefreshToken();
            $client->fetchAccessTokenWithRefreshToken($refreshTokenSaved);
            // pass access token to some variable
            $accessTokenUpdated = $client->getAccessToken();
            // append refresh token
            $accessTokenUpdated['refresh_token'] = $refreshTokenSaved;
            file_put_contents($credentialsPath, json_encode($accessTokenUpdated));
        }
        return $client;
    }

    public function storeCredentials($authCode, $datosCliente) {
        $client = new Google_Client();
        $client->setApplicationName($datosCliente['nombreApp']);
        $client->setScopes($datosCliente['scopes']);
        $client->setAuthConfigFile($this->dir . '/' . $datosCliente['alias'] . '_client_secret.json');
        $client->setAccessType('offline');
        // Load previously authorized credentials from a file.
        $ficheroCredenciales = $this->dir . '/credenciales/' . $datosCliente['alias'] . '/' . $datosCliente['usuario'] . '.json';
        $credentialsPath = $this->expandHomeDirectory($ficheroCredenciales);
        // Exchange authorization code for an access token.
        $authCode = trim($authCode);
        // Exchange authorization code for an access token.
        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
        // Store the credentials to disk.
        if (!file_exists(dirname($credentialsPath))) {
            mkdir(dirname($credentialsPath), 0700, true);
        }
        file_put_contents($credentialsPath, json_encode($accessToken));
        return $credentialsPath;
    }

    public function createEventoCalendar($datos) {
        $evento = new Google_Service_Calendar_Event(array(
            'summary' => $datos['titulo'],
            'location' => $datos['lugar'],
            'description' => $datos['descripcion'],
            'start' => array(
                'dateTime' => $datos['inicio'],
                'timeZone' => 'Europe/Madrid',
            ),
            'end' => array(
                'dateTime' => $datos['fin'],
                'timeZone' => 'Europe/Madrid',
            ),
            'reminders' => array(
                'useDefault' => FALSE,
                'overrides' => array(
                    array('method' => 'email', 'minutes' => 1 * 24 * 60),
                    array('method' => 'email', 'minutes' => 7 * 24 * 60),
                    array('method' => 'popup', 'minutes' => 1 * 24 * 60),
                ),
            ),
            'extendedProperties' => array(
                'foto' => $datos['foto'],
                'tematica' => $datos['tematica'],
                'id' => $datos['id'],
                'link' => array_key_exists('link', $datos) ? $datos['link'] : ''
            )
        ));
        return $evento;
    }

    private function getEventos($service) {
        $params = ['maxResults' => 10,
            'orderBy' => 'startTime',
            'singleEvents' => TRUE,
            'timeMin' => date('c'),
            'q' => 'Aluni'
        ];
        return $service->events->listEvents('primary', $params)['items'];
    }

    /**
     * Expands the home directory alias '~' to the full path.
     * @param string $path the path to expand.
     * @return string the expanded path.
     */
    private function expandHomeDirectory($path) {
        $homeDirectory = getenv('HOME');
        if (empty($homeDirectory)) {
            $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
        }
        return str_replace('~', realpath($homeDirectory), $path);
    }

}
