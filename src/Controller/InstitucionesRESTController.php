<?php

namespace SWD\MadridBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Controlador encargado del servicio REST de instituciones de s. Usa funcionalidades del FOSRestBundle (creación de 
 * rutas y de respuestas en formato json), el cual usa  a su vez el JMSSerializationBundle (encargados de la 
 * serialización de entidades).
 * 
 * @author Álvaro Peláez Santana
 * @copyright ALUNI MADRID S.L.
 * @Route("/instituciones")
 */
class InstitucionesRESTController extends FOSRestController {

    /**
     * @View(serializerGroups={"lista-instituciones"})
     * @Security("has_role('ROLE_PARTICIPANTE') || has_role('ROLE_EMPLEADO')")
     * @param Request $request
     * @return Collection
     */
    public function getInstitucionesAction(Request $request) {
        if($this->get('seg_service')->esParticipante()){
            $checkeos = $this->getUser()->getCheckeos();
            $instituciones = [];
            foreach($checkeos as $checkeo) {
                $instituciones[] = $checkeo->getInstitucion();
            }
        } else {
            $instituciones = $this->getDoctrine()->getRepository('SWDMadridBundle:Institucion')->findByEnabled(1);
        }
        return $instituciones;
    }

    /**
     * Devuelve una institucion determinada en formato json.
     *
     * @View()
     * @param integer $id
     * @return Institucion
     */
    public function getInstitucionAction($id) {
        return $this->getDoctrine()->getRepository('SWDMadridBundle:Institucion')->find($id);
    }

}
