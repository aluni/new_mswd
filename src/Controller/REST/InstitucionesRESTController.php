<?php

namespace App\Controller\REST;

use App\Entity\Institucion;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Controlador encargado del servicio REST de instituciones de s. Usa funcionalidades del FOSRestBundle (creación de 
 * rutas y de respuestas en formato json), el cual usa  a su vez el JMSSerializationBundle (encargados de la 
 * serialización de entidades).
 * 
 * @author Álvaro Peláez Santana
 * @copyright ALUNI MADRID S.L.
 */
class InstitucionesRESTController extends AluniRESTController {

    /**
     * @Rest\View(serializerGroups={"lista-instituciones"})
     * @Rest\Get("/instituciones", name="get_instituciones")
     * @Security("is_granted('ROLE_PARTICIPANTE') || is_granted('ROLE_EMPLEADO')")
     * @return array|Institucion[]|object[]
     */
    public function getInstitucionesAction(): array {
        if($this->seguridad->esParticipante()){
            $checkeos = $this->getUser()->getCheckeos();
            $instituciones = [];
            foreach($checkeos as $checkeo) {
                $instituciones[] = $checkeo->getInstitucion();
            }
        } else {
            $instituciones = $this->doctrine->getRepository(Institucion::class)->findBy(['enabled' => 1]);
        }
        return $instituciones;
    }

    /**
     * Devuelve una institucion determinada en formato json.
     *
     * @Rest\View()
     * @Rest\Get("/instituciones/{id}")
     * @param integer $id
     * @return Institucion
     */
    public function getInstitucionAction(int $id): Institucion {
        return $this->doctrine->getRepository(Institucion::class)->find($id);
    }

}
