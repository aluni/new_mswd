<?php

namespace SWD\MadridBundle\Utils;

/**
 * Servicio que se encarga de comprobaciones de seguridad. Es usado en distintas partes del proyecto
 * 
 * @author Álvaro Peláez Santana
 * @copyright ALUNI MADRID S.L.
 */
class SeguridadService {

    protected $session, $authChecker, $tokenStorage, $em, $conn;

    public function __construct($session, $authChecker, $tokenStorage, $em, $conn) {
        $this->tokenStorage = $tokenStorage;
        $this->session = $session;
        $this->em = $em;
        $this->conn = $conn;
        $this->authChecker = $authChecker;
    }
    /**
     * Devuelve si el usuario actual es o no empleado.
     * 
     * @return boolean
     */
    public function esEmpleado() {
        return $this->authChecker->isGranted('ROLE_EMPLEADO');
    }

    /**
     * Devuelve si el usuario actual es o no usuario registrado.
     * 
     * @return boolean
     */
    public function esUsuario() {
        return $this->authChecker->isGranted('ROLE_USER');
    }

    /**
     * Devuelve si el usuario actual es o no estudiante.
     * 
     * @return boolean
     */
    public function esParticipante() {
        return $this->authChecker->isGranted('ROLE_PARTICIPANTE');
    }

    /**
     * Devuelve si el usuario actual es o no comercial.
     * 
     * @return boolean
     */
    public function esInstitucion() {
        return $this->authChecker->isGranted('ROLE_INSTITUCION');
    }

    /**
     * Devuelve si el usuario actual es o no administrador.
     * 
     * @return boolean
     */
    public function esAdmin() {
        return $this->authChecker->isGranted('ROLE_ADMIN');
    }


}
