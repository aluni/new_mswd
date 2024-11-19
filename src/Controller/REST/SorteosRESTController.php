<?php

namespace App\Controller\REST;

use Doctrine\Common\Collections\Collection;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations\View;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controlador encargado del servicio REST de sorteos de s. Usa funcionalidades del FOSRestBundle (creación de 
 * rutas y de respuestas en formato json), el cual usa  a su vez el JMSSerializationBundle (encargados de la 
 * serialización de entidades).
 * 
 * @author Álvaro Peláez Santana
 * @copyright ALUNI MADRID S.L.
 * @Route("/sorteos")
 */
class SorteosRESTController extends AluniRESTController {

    /**
     * @View(serializerGroups={"lista-sorteos"})
     * @Security("has_role('ROLE_INSTITUCION') || has_role('ROLE_EMPLEADO')")
     * @param Request $request
     * @return Collection
     */
    public function getSorteosAction(Request $request) {
        if ($this->get('seg_service')->esInstitucion()) {
            $checkeos = $this->getUser()->getCheckeos();
            $sorteos = [];
            foreach ($checkeos as $checkeo) {
                $sorteos[] = $checkeo->getSorteo();
            }
        } else {
            $sorteos = $this->doctrine->getRepository('SWDMadridBundle:Sorteo')->findAll();
        }
        return $sorteos;
    }

    /**
     * Devuelve una sorteo determinada en formato json.
     *
     * @View()
     * @param integer $id
     * @return Sorteo
     */
    public function getSorteoAction($id) {
        return $this->doctrine->getRepository('SWDMadridBundle:Sorteo')->find($id);
    }

    /**
     * Actualiza un recibo con sus desgloses a partir de la información que llega por la Request en formato json.
     * 
     * @param Request $request
     * @param integer $id
     * @return Response
     */
    public function postSorteoAction(Request $request) {
        $sorteo = $this->get('jms_serializer')->deserialize($request->getContent(), 'App\Entity\Sorteo', 'json');
        $em = $this->doctrine->getManager();
        $this->em->persist($sorteo);
        foreach ($sorteo->getParticipantes() as $participante) {
            $participante->addSorteo($sorteo);
            $this->em->persist($participante);
        }
        $this->em->flush();
        return new JsonResponse(['mensaje' => "Sorteo creado correctamente"]);
    }

    /**
     * Actualiza un recibo con sus desgloses a partir de la información que llega por la Request en formato json.
     * 
     * @param Request $request
     * @param integer $id
     * @return Response
     */
    public function putSorteoAction(Request $request, $id) {
        $sorteo = $this->get('jms_serializer')->deserialize(
                $request->getContent(), 'App\Entity\Sorteo', 'json');
        $em = $this->doctrine->getManager();
        $this->em->persist($sorteo);
        $this->em->flush();
        return new JsonResponse(['mensaje' => "Sorteo $id actualizado correctamente"]);
    }

}
