<?php

namespace SWD\MadridBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use SWD\MadridBundle\Entity\Participante;
use SWD\MadridBundle\Entity\Checkeo;
use Google_Service_Gmail;
use Google_Service_Gmail_Message;
use Swift_Message;
use Swift_Attachment;

class ParticipantesController extends Controller {

    /**
     * @Route("/guardarInscripcion", name="guardarInscripcion")
     */
    public function guardarInscripcionAction(Request $request) {
        $datos = $request->request->all();
        $participante = new Participante();
        foreach ($datos as $clave => $valor) {
            $metodo = 'set' . $clave;
            if (method_exists($participante, $metodo)) {
                $participante->$metodo($valor);
            }
        }
        $participante->addRole('ROLE_PARTICIPANTE');
        $participante->setUsername($participante->getEmail());
        $participante->setPassword('1q2w3e');
        $participante->setHoraEntrada(date_create());
        $em = $this->getDoctrine()->getManager();
        $em->persist($participante);
        $em->flush();
        $numeroEntrada = $this->generarNumeroEntrada($participante);
        $participante->setNumeroEntrada($numeroEntrada);
        $em->flush();
        $nombreCompleto = $participante->getNombre() . ' ' . $participante->getApellidos();
        $titulo = "$nombreCompleto we welcome you to the MSWD!";
        $email = $participante->getEmail();
        $plantilla = $this->renderView('SWDMadridBundle:Mails:validacion.html.twig', [
            'titulo' => $titulo,
            'nombre' => $nombreCompleto,
            'numeroEntrada' => $numeroEntrada]);
        $this->enviarEmail($plantilla, $titulo, 'info@studentwelcomeday.com', $email);
        return new Response(""
                . "Inscripción guardada, recibirás un email con las instrucciones para descargar tu entrada | "
                . "Sign up saved, you will receive an email with the instructions to download the ticket<br>"
                . "<a href='https://www.facebook.com/groups/454624111583061/' target='_blank'>"
                . "<i class='fa fa-facebook'></i> Join now our official forum at facebook!"
                . "</a>");
    }

    /**
     * @Route("/validarParticipante/{numeroEntrada}", name="validarParticipante")
     */
    public function validarParticipanteAction($numeroEntrada) {
        $em = $this->getDoctrine()->getManager();
        $participante = $em->getRepository('SWDMadridBundle:Participante')->findOneByNumeroEntrada($numeroEntrada);
        if (empty($participante)) {
            throw new AccessDeniedHttpException('Acceso no permitido');
        }
        $this->get('fos_user.util.user_manipulator')->activate($participante->getUsername());
        $this->get('fos_user.util.user_manipulator')->changePassword($participante->getUsername(), $participante->getNumeroEntrada());
        $random = substr($participante->getEmail(), 1, 2);
        $ficheroTicket = $this->get('kernel')->getRootDir() . '/../web/tickets/' . $random . $numeroEntrada . '.pdf';
        $urlFichero = $this->generateUrl('home', [], true) . 'tickets/' . $random . $numeroEntrada . '.pdf';
        if (file_exists($ficheroTicket)) {
            unlink($ficheroTicket);
        }
        $this->get('knp_snappy.pdf')->generate($this->generateUrl('verTicket', ['numeroEntrada' => $numeroEntrada], true), $ficheroTicket);
        $nombreCompleto = $participante->getNombre() . ' ' . $participante->getApellidos();
        $titulo = "$nombreCompleto here you have your ticket!";
        $email = $participante->getEmail();
        $plantilla = $this->renderView('SWDMadridBundle:Mails:ticket.html.twig', [
            'titulo' => $titulo,
            'nombre' => $nombreCompleto,
            'numeroEntrada' => $numeroEntrada,
            'urlFichero' => $urlFichero]);
        $adjuntos = [];
        $adjuntos[] = $ficheroTicket;
        $this->enviarEmail($plantilla, $titulo, 'info@studentwelcomeday.com', $email, $adjuntos);
        $em->flush();
        $this->addFlash('success', "Tu email has sido validado, recibirás otro email con tu ticket de entrada. | "
                . "You have been validated, you will receive another email with the ticket");
        return $this->redirect($this->generateUrl('home'));
    }

    /**
     * @Route("/imdabestever/verTicket/{numeroEntrada}", name="verTicket")
     * @Template()
     */
    public function verTicketAction($numeroEntrada) {
        $em = $this->getDoctrine()->getManager();
        $participante = $em->getRepository('SWDMadridBundle:Participante')->findOneByNumeroEntrada($numeroEntrada);
        if (empty($participante)) {
            throw new AccessDeniedHttpException('Acceso no permitido');
        }
        return ['participante' => $participante, 'numeroEntrada' => $numeroEntrada];
    }

    /**
     * @Route("/info-participante", name="infoParticipante")
     * @Security("has_role('ROLE_USER')")
     * @Template
     */
    public function infoParticipanteAction() {
        if ($this->get('seg_service')->esInstitucion() || $this->get('seg_service')->esEmpleado()) {
            return $this->redirect($this->generateUrl('lista-participantes'));
        }
        $participante = $this->getUser();
        $random = substr($participante->getEmail(), 1, 2);
        $urlTicket = 'tickets/' . $random . $participante->getNumeroEntrada() . '.pdf';
        return ['participante' => $participante, 'urlTicket' => $urlTicket];
    }

    //========================================FUNCIONES AUXILIARES====================================================//
    private function enviarEmail($plantilla, $asunto, $from, $to, $adjuntos = []) {
        $replyTo = $from;
        $mensaje = Swift_Message::newInstance()
                ->setSubject($asunto)
                ->setFrom($from)
                ->setReplyTo($replyTo)
                ->setTo($to)
                ->setBody($plantilla, 'text/html');
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

    private function generarNumeroEntrada($participante) {
        $numeroReal = substr('0000' . $participante->getId(), -4);
        return substr(time() . $numeroReal, -5);
    }

}
