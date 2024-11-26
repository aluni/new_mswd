<?php

namespace App\Controller;


use App\Entity\ComoConoce;
use App\Entity\Pais;
use App\Entity\Participante;
use App\Form\SorteoType;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Form\ParticipanteFiltrosType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Álvaro Peláez Santana
 * @copyright ALUNI MADRID S.L.
 */
class AdminController extends AluniController {

    /**
     * @return array
     * @Route("/lista-participantes", name="lista-participantes")
     * @Security("is_granted('ROLE_INSTITUCION') || is_granted('ROLE_EMPLEADO')")
     * @Template
     * @throws Exception
     */
    public function listaParticipantesAction(): array {
        $paises = $this->conn->executeQuery('SELECT es FROM Pais')->fetchFirstColumn();
        foreach ($paises as $i => $pais) {
            $paises[$pais] = $pais;
            unset($paises[$i]);
        }
        $formFiltrosParticipante = $this->createForm(ParticipanteFiltrosType::class, null, ['paises' => $paises]);
        $paises = $this->doctrine->getRepository(Pais::class)->findAll();
        $formSorteo = $this->createForm(SorteoType::class);
        $comoConoce = $this->doctrine->getRepository(ComoConoce::class)->findBy([], ['comoConoce' => 'ASC']);
        return ['paises' => $paises,
            'comoConoce' => $comoConoce,
            'formFiltrosParticipante' => $formFiltrosParticipante->createView(),
            'formSorteo' => $formSorteo->createView(),
            'cc' => ''];
    }

    /**
     * @return array
     * @Route("/sorteos", name="sorteos")
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     * @Template
     * @throws Exception
     */
    public function sorteosAction(): array {
        $paises = $this->conn->executeQuery('SELECT es FROM Pais')->fetchFirstColumn();
        foreach ($paises as $i => $pais) {
            $paises[$pais] = $pais;
            unset($paises[$i]);
        }
        $participantesREST = $this->generateUrl('participantesREST');
        $formFiltrosParticipante = $this->createForm(ParticipanteFiltrosType::class, null, ['paises' => $paises]);
        $formSorteo = $this->createForm(SorteoType::class);
        return ['participantesREST' => $participantesREST,
            'formFiltrosParticipante' => $formFiltrosParticipante->createView(),
            'formSorteo' => $formSorteo->createView(),
            'sorteos' => true,
            'cc' => ''];
    }

    /**
     * @Route("/participantes/{id}", requirements={"_format"="json"}, methods={"PUT"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function updateParticipanteAction(Request $request, $id): JsonResponse {
        $participante = $this->doctrine->getRepository(Participante::class)->find($id);
        $datosParticipante = json_decode($request->getContent(), true);
        $participante->setAsistido($datosParticipante['asistido']);
        $participante->setSorteo($datosParticipante['sorteo']);
        $this->doctrine->getManager()->flush();
        return new JsonResponse(['mensaje' => 'Guardado correctamente']);
    }

}
