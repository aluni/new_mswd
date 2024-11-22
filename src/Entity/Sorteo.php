<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Sorteo
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Sorteo {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Groups({"lista-participantes"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     * @Serializer\Groups({"lista-participantes"})
     */
    private $nombre;

    /**
     * @var integer
     *
     * @ORM\Column(name="cantidad", type="integer")
     */
    private $cantidad;

    /**
     * @var string
     *
     * @ORM\Column(name="condicion", type="string", length=50)
     */
    private $condicion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @ORM\ManyToMany(targetEntity="Participante", mappedBy="sorteos")
     * @Serializer\Type("ArrayCollection<App\Entity\Participante>")
     */
    private $participantes;

    /**
     * @ORM\ManyToOne(targetEntity="Institucion", inversedBy="sorteos")
     * @Serializer\Type("App\Entity\Institucion")
     */
    private $institucion;

    /** @ORM\PrePersist */
    public function fechaOnPrePersist(){
        $this->fecha = date_create();
    }
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Sorteo
     */
    public function setNombre($nombre) {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre() {
        return $this->nombre;
    }

    /**
     * Set cantidad
     *
     * @param integer $cantidad
     *
     * @return Sorteo
     */
    public function setCantidad($cantidad) {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return integer
     */
    public function getCantidad() {
        return $this->cantidad;
    }

    /**
     * Set condicion
     *
     * @param string $condicion
     *
     * @return Sorteo
     */
    public function setCondicion($condicion) {
        $this->condicion = $condicion;

        return $this;
    }

    /**
     * Get condicion
     *
     * @return string
     */
    public function getCondicion() {
        return $this->condicion;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return Sorteo
     */
    public function setFecha($fecha) {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha() {
        return $this->fecha;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->participantes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add participante
     *
     * @param Participante $participante
     *
     * @return Sorteo
     */
    public function addParticipante(Participante $participante) {
        $this->participantes[] = $participante;

        return $this;
    }

    /**
     * Remove participante
     *
     * @param Participante $participante
     */
    public function removeParticipante(Participante $participante) {
        $this->participantes->removeElement($participante);
    }

    /**
     * Get participantes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParticipantes() {
        return $this->participantes;
    }

    /**
     * Set institucion
     *
     * @param Institucion $institucion
     *
     * @return Sorteo
     */
    public function setInstitucion(Institucion $institucion = null) {
        $this->institucion = $institucion;

        return $this;
    }

    /**
     * Get institucion
     *
     * @return Institucion
     */
    public function getInstitucion() {
        return $this->institucion;
    }

    public function __toString() {
        return $this->nombre;
    }

}
