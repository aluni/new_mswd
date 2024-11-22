<?php

namespace App\Controller;


use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Participante;

class ParticipantesController extends AluniController {

    /**
     * @Route("/guardarInscripcion", name="guardarInscripcion")
     */
    public function guardarInscripcionAction(Request $request): Response {
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
        
        $this->em->persist($participante);
        $this->em->flush();
        $numeroEntrada = $this->generarNumeroEntrada($participante);
        $participante->setNumeroEntrada($numeroEntrada);
        $this->em->flush();
        $nombreCompleto = $participante->getNombre() . ' ' . $participante->getApellidos();
        $titulo = "$nombreCompleto we welcome you to the MSWD!";
        $email = $participante->getEmail();
        $plantilla = $this->renderView('mails/validacion.html.twig', [
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
    public function validarParticipanteAction(Pdf $pdf, $numeroEntrada): RedirectResponse {
        
        $participante = $this->em->getRepository(Participante::class)->findOneBy(['numeroEntrada' => $numeroEntrada]);
        if (empty($participante)) {
            throw new AccessDeniedHttpException('Acceso no permitido');
        }
        $this->userManipulator->activate($participante->getUsername());
        $this->userManipulator->changePassword($participante->getUsername(), $participante->getNumeroEntrada());
        $random = substr($participante->getEmail(), 1, 2);

        $ficheroTicket = $this->getParameter('kernel.project_dir') . '/public/tickets/' . $random . $numeroEntrada . '.pdf';
        $urlFichero = $this->generateUrl('home', [], true) . 'tickets/' . $random . $numeroEntrada . '.pdf';
        if (file_exists($ficheroTicket)) {
            unlink($ficheroTicket);
        }
        $pdf->generate($this->generateUrl('verTicket', ['numeroEntrada' => $numeroEntrada], true), $ficheroTicket);
        $nombreCompleto = $participante->getNombre() . ' ' . $participante->getApellidos();
        $titulo = "$nombreCompleto here you have your ticket!";
        $email = $participante->getEmail();
        $plantilla = $this->renderView('mails/ticket.html.twig', [
            'titulo' => $titulo,
            'nombre' => $nombreCompleto,
            'numeroEntrada' => $numeroEntrada,
            'urlFichero' => $urlFichero]);
        $adjuntos = [];
        $adjuntos[] = $ficheroTicket;
        $this->enviarEmail($plantilla, $titulo, 'info@studentwelcomeday.com', $email, $adjuntos);
        $this->em->flush();
        $this->addFlash('success', "Tu email has sido validado, recibirás otro email con tu ticket de entrada. | "
                . "You have been validated, you will receive another email with the ticket");
        return $this->redirect($this->generateUrl('home'));
    }

    /**
     * @Route("/imdabestever/verTicket/{numeroEntrada}", name="verTicket")
     * @Template()
     */
    public function verTicketAction($numeroEntrada): array {
        
        $participante = $this->em->getRepository(Participante::class)->findOneByNumeroEntrada($numeroEntrada);
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
    public function infoParticipanteAction(): RedirectResponse|array {
        if ($this->seguridad->esInstitucion() || $this->seguridad->esEmpleado()) {
            return $this->redirect($this->generateUrl('lista-participantes'));
        }
        $participante = $this->getUser();
        $random = substr($participante->getEmail(), 1, 2);
        $urlTicket = 'tickets/' . $random . $participante->getNumeroEntrada() . '.pdf';
        return ['participante' => $participante, 'urlTicket' => $urlTicket];
    }

    //========================================FUNCIONES AUXILIARES====================================================//

    private function generarNumeroEntrada($participante): string {
        $numeroReal = substr('0000' . $participante->getId(), -4);
        return substr(time() . $numeroReal, -5);
    }

}
