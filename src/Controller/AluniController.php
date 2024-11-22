<?php

namespace App\Controller;

use App\Services\GoogleServicesManager;
use App\Services\SeguridadService;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Google_Service_Gmail;
use Google_Service_Gmail_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mime\Email;

class AluniController extends AbstractController {

    protected Connection $conn;

    public function __construct(
        protected ManagerRegistry $doctrine,
        protected EntityManagerInterface $em,
        protected SeguridadService $seguridad,
        #[Autowire(service: 'fos_user.util.user_manipulator')]
        protected $userManipulator,
        protected GoogleServicesManager $googleManager) {
        $this->conn = $em->getConnection();
    }

    protected function enviarEmail($cuerpo, $asunto, $to, $adjuntos = []): void {
        $mensaje =(new Email())
            ->from('info@studentwelcomeday.com')
            ->to($to)
            ->subject($asunto)
            ->html($cuerpo);
        foreach ($adjuntos as $adjunto) {
            if (!empty($adjunto)) {
                if (is_file($adjunto)) {
                    $mensaje->attachFromPath($adjunto);
                } else if (is_array($adjunto)) {
                    $mensaje->attach($adjunto['data'], $adjunto['nombre']);
                }
            }
        }
        try {
            // The message needs to be encoded in Base64URL
            $mime = rtrim(strtr(base64_encode($mensaje->toString()), '+/', '-_'), '=');
            $msg = new Google_Service_Gmail_Message();
            $msg->setRaw($mime);
            // Get the API client and construct the service object.
            $datosCliente = ['nombreApp' => "MSWD",
                'alias' => 'gmail',
                'scopes' => implode(' ', [
                        Google_Service_Gmail::GMAIL_READONLY,
                        Google_Service_Gmail::GMAIL_COMPOSE,
                        Google_Service_Gmail::GMAIL_MODIFY]
                ),
                'usuario' => 'info@studentwelcomeday.com'];
            $client = $this->googleManager->getClient($datosCliente);
            $gmailService = new Google_Service_Gmail($client);
            //The special value **me** can be used to indicate the authenticated user.
            $gmailService->users_messages->send("me", $msg);
            return;
        } catch (Exception $e) {
            print_r($e->getMessage());
            return;
        }
    }
}
