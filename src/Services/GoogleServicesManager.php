<?php

namespace App\Services;

use Google_Client;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Servicio que engloba un conjunto de funciones auxiliares para el manejo de los servicios de
 * Google
 *
 * @author Álvaro Peláez Santana
 * @copyright ALUNI MADRID S.L.
 */
class GoogleServicesManager {

    protected string $dir;

    public function __construct(#[Autowire('%kernel.project_dir%/config/google')] string $googleDir) {
        $this->dir = $googleDir;
    }

    function getClient($datosCliente): Google_Client|string {
        $client = new Google_Client();
        $client->setApplicationName($datosCliente['nombreApp']);
        $client->setScopes($datosCliente['scopes']);
        $client->setAuthConfig($this->dir . '/' . $datosCliente['alias'] . '_client_secret.json');
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

    /**
     * Expands the home directory alias '~' to the full path.
     * @param string $path the path to expand.
     * @return string the expanded path.
     */
    private function expandHomeDirectory(string $path): string {
        $homeDirectory = getenv('HOME');
        if (empty($homeDirectory)) {
            $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
        }
        return str_replace('~', realpath($homeDirectory), $path);
    }

}
