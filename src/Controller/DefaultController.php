<?php

namespace App\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Google_Service_Gmail;
use Google_Service_Gmail_Message;
use Swift_Message;
use Swift_Attachment;

class DefaultController extends AluniController {

    /**
     * @Route("/students", name="home")
     * @Template
     */
    public function indexAction(Request $request) {
        $cc = $request->query->get('cc');
        $paises = $this->getDoctrine()->getRepository('SWDMadridBundle:Pais')->findAll();
        $comoConoce = $this->getDoctrine()->getRepository('SWDMadridBundle:ComoConoce')->findBy([], ['comoConoce' => 'ASC']);
        return ['paises' => $paises, 'comoConoce' => $comoConoce, 'cc' => $cc];
    }

    /**
     * @Route("/clausula-privacidad", name="clausulaPrivacidad")
     * @Template
     */
    public function clausulaPrivacidadAction() {
        return [];
    }

    /**
     * @Route("/aviso-legal", name="avisoLegal")
     * @Template
     */
    public function avisoLegalAction() {
        return [];
    }

    /**
     * @Route("/politica-cookies", name="politicaCookies")
     * @Template
     */
    public function politicaCookiesAction() {
        return [];
    }

    /**
     * @Route("/concurso", name="concurso")
     * @Template
     */
    public function concursoAction() {
        return [];
    }

    /**
     * @Route("/enviarEmailContactar", name="enviarEmailContactar")
     * @Method({"POST"})
     */
    public function enviarEmailContactarAction(Request $request) {
        $email = $request->request->get('email');
        $nombre = $request->request->get('nombre');
        $mensaje = "<p>Nombre: <b>$nombre</b></p>"
                . "<p>Email: <b>$email</b></p>"
                . "<p>Mensaje: <b>" . $request->request->get('mensaje') . "</b></p>";
        $this->enviarEmail($mensaje, "Mensaje desde contactar MSWD - $nombre", 'info@studentwelcomeday.com', 'diego.poole@gmail.com', $email);
        return new Response('Email enviado correctamente');
    }

    /**
     * @Route("/lista-ganadores", name="lista_ganadores")
     * @Template
     */
    public function listaGanadoresAction() {
        $sorteos = $this->getDoctrine()->getRepository('SWDMadridBundle:Sorteo')->findAll();
        return ['sorteos' => $sorteos];
    }

    /**
     * @Route("/boletin-universia", name="boletin_universia")
     * @Template("SWDMadridBundle:Mails:boletinUniversia.html.twig")
     */
    public function boletinUniversiaAction() {
        return [];
    }

    private function enviarEmail($cuerpo, $asunto, $from, $to, $replyTo, $adjuntos = []) {
        $replyTo = $from;
        $mensaje = Swift_Message::newInstance()
                ->setSubject($asunto)
                ->setFrom($from)
                ->setReplyTo($replyTo)
                ->setTo($to)
                ->setBody($cuerpo, 'text/html');
        foreach ($adjuntos as $adjunto) {
            $mensaje->attach(Swift_Attachment::fromPath($adjunto));
        }
        try {
            // The message needs to be encoded in Base64URL
            $mime = rtrim(strtr(base64_encode($mensaje), '+/', '-_'), '=');
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
                'usuario' => $from];
            $client = $this->get('google_manager')->getClient($datosCliente);
            $gmailService = new Google_Service_Gmail($client);
            //The special value **me** can be used to indicate the authenticated user.
            $gmailService->users_messages->send("me", $msg);
            return true;
        } catch (Exception $e) {
            print_r($e->getMessage());
            return -1;
        }
        //$this->get('mailer')->send($mensaje);
    }

}
