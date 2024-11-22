<?php

namespace App\Services;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Servicio que se encarga de comprobaciones de seguridad. Es usado en distintas partes del proyecto
 * 
 * @author Álvaro Peláez Santana
 * @copyright ALUNI MADRID S.L.
 */
class SeguridadService {


    public function __construct(protected AuthorizationCheckerInterface $authChecker) {}

    /**
     * Devuelve si el usuario actual es o no empleado.
     * 
     * @return boolean
     */
    public function esEmpleado(): bool {
        return $this->authChecker->isGranted('ROLE_EMPLEADO');
    }

    /**
     * Devuelve si el usuario actual es o no usuario registrado.
     * 
     * @return boolean
     */
    public function esUsuario(): bool {
        return $this->authChecker->isGranted('ROLE_USER');
    }

    /**
     * Devuelve si el usuario actual es o no estudiante.
     * 
     * @return boolean
     */
    public function esParticipante(): bool {
        return $this->authChecker->isGranted('ROLE_PARTICIPANTE');
    }

    /**
     * Devuelve si el usuario actual es o no comercial.
     * 
     * @return boolean
     */
    public function esInstitucion(): bool {
        return $this->authChecker->isGranted('ROLE_INSTITUCION');
    }

    /**
     * Devuelve si el usuario actual es o no administrador.
     * 
     * @return boolean
     */
    public function esAdmin(): bool {
        return $this->authChecker->isGranted('ROLE_ADMIN');
    }


}
