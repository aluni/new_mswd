<?php

namespace App\Controller\REST;

use Doctrine\Common\Collections\Collection;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations\View;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controlador encargado del servicio REST de participantes de s. Usa funcionalidades del FOSRestBundle (creación de 
 * rutas y de respuestas en formato json), el cual usa  a su vez el JMSSerializationBundle (encargados de la 
 * serialización de entidades).
 * 
 * @author Álvaro Peláez Santana
 * @copyright ALUNI MADRID S.L.
 * @Route("/participantes")
 */
class ParticipantesRESTController extends AluniRESTController {

    /**
     * @View(serializerGroups={"lista-participantes"})
     * @Security("has_role('ROLE_INSTITUCION') || has_role('ROLE_EMPLEADO')")
     * @param Request $request
     * @return Collection
     */
    public function getParticipantesAction(Request $request) {
        if ($this->get('seg_service')->esInstitucion()) {
            $checkeos = $this->getUser()->getCheckeos();
            $participantes = [];
            foreach ($checkeos as $checkeo) {
                $participantes[] = $checkeo->getParticipante();
            }
        } else {
            $participantes = $this->doctrine->getRepository('SWDMadridBundle:Participante')->findAll();
        }
        return $participantes;
    }

    /**
     * Devuelve una participante determinada en formato json.
     *
     * @View(serializerGroups={"lista-participantes"})
     * @param integer $id
     * @return Participante
     */
    public function getParticipanteAction($id) {
        return $this->doctrine->getRepository('SWDMadridBundle:Participante')->find($id);
    }

    /**
     * Actualiza un recibo con sus desgloses a partir de la información que llega por la Request en formato json.
     * 
     * @param Request $request
     * @param integer $id
     * @return Response
     */
    public function putParticipanteAction(Request $request, $id) {
        $participante = $this->get('jms_serializer')->deserialize(
                $request->getContent(), 
                'App\Entity\Participante', 
                'json');
        $em = $this->doctrine->getManager();
        $this->em->persist($participante);
        $this->em->flush();
        return new JsonResponse(['mensaje' => "Participante $id actualizado correctamente"]);
    }

}
