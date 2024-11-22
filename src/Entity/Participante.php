<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Participante
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Participante extends Usuario {

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=50)
     * @Serializer\Groups({"lista-participantes"})
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="apellidos", type="string", length=50)
     * @Serializer\Groups({"lista-participantes"})
     */
    private $apellidos;

    /**
     * @var integer
     *
     * @ORM\Column(name="sexo", type="smallint")
     * @Serializer\Groups({"lista-participantes"})
     */
    private $sexo;

    /**
     * @var string
     *
     * @ORM\Column(name="universidad", type="string", length=100)
     * @Serializer\Groups({"lista-participantes"})
     */
    private $universidad;

    /**
     * @var string
     *
     * @ORM\Column(name="como_conoce", type="string", length=150)
     * @Serializer\Groups({"lista-participantes"})
     */
    private $comoConoce;

    /**
     * @var string
     *
     * @ORM\Column(name="nacionalidad", type="string", length=50)
     * @Serializer\Groups({"lista-participantes"})
     */
    private $nacionalidad;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="text")
     * @Serializer\Groups({"lista-participantes"})
     */
    private $observaciones = '';

    /**
     * @var string
     *
     * @ORM\Column(name="numero_entrada", type="string", length=5)
     * @Serializer\Groups({"lista-participantes"})
     */
    private $numeroEntrada = '00000';

    /**
     * @var boolean
     *
     * @ORM\Column(name="asistido", type="boolean")
     * @Serializer\Groups({"lista-participantes"})
     */
    private $asistido = false;
    
    /**
     * @var DateTime
     *
     * @ORM\Column(name="hora_entrada", type="time")
     * @Serializer\Groups({"lista-participantes"})
     * @Serializer\Type("DateTime<'H:i:s'>")
     */
    private $horaEntrada;

    /**
     * @ORM\ManyToMany(targetEntity="Sorteo", inversedBy="participantes", cascade={"persist"})
     * @Serializer\Type("ArrayCollection<App\Entity\Sorteo>")
     * @Serializer\Groups({"lista-participantes"})
     */
    private $sorteos;

    /**
     * @ORM\OneToMany(targetEntity="Checkeo", mappedBy="participante", cascade={"persist"}) 
     * @Serializer\Type("ArrayCollection<App\Entity\Checkeo>")
     * @Serializer\Groups({"lista-participantes"})
     */
    private $checkeos;

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Participante
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
     * Set apellidos
     *
     * @param string $apellidos
     *
     * @return Participante
     */
    public function setApellidos($apellidos) {
        $this->apellidos = $apellidos;

        return $this;
    }

    /**
     * Get apellidos
     *
     * @return string
     */
    public function getApellidos() {
        return $this->apellidos;
    }

    /**
     * Set sexo
     *
     * @param integer $sexo
     *
     * @return Participante
     */
    public function setSexo($sexo) {
        $this->sexo = $sexo;

        return $this;
    }

    /**
     * Get sexo
     *
     * @return integer
     */
    public function getSexo() {
        return $this->sexo;
    }

    /**
     * Set universidad
     *
     * @param string $universidad
     *
     * @return Participante
     */
    public function setUniversidad($universidad) {
        $this->universidad = $universidad;

        return $this;
    }

    /**
     * Get universidad
     *
     * @return string
     */
    public function getUniversidad() {
        return $this->universidad;
    }

    /**
     * Set comoConoce
     *
     * @param string $comoConoce
     *
     * @return Participante
     */
    public function setComoConoce($comoConoce) {
        $this->comoConoce = $comoConoce;

        return $this;
    }

    /**
     * Get comoConoce
     *
     * @return string
     */
    public function getComoConoce() {
        return $this->comoConoce;
    }

    /**
     * Set nacionalidad
     *
     * @param string $nacionalidad
     *
     * @return Participante
     */
    public function setNacionalidad($nacionalidad) {
        $this->nacionalidad = $nacionalidad;

        return $this;
    }

    /**
     * Get nacionalidad
     *
     * @return string
     */
    public function getNacionalidad() {
        return $this->nacionalidad;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     *
     * @return Participante
     */
    public function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string
     */
    public function getObservaciones() {
        return $this->observaciones;
    }

    /**
     * Set numeroEntrada
     *
     * @param string $numeroEntrada
     *
     * @return Participante
     */
    public function setNumeroEntrada($numeroEntrada) {
        $this->numeroEntrada = $numeroEntrada;

        return $this;
    }

    /**
     * Get numeroEntrada
     *
     * @return string
     */
    public function getNumeroEntrada() {
        return $this->numeroEntrada;
    }

    /**
     * Set asistido
     *
     * @param boolean $asistido
     *
     * @return Participante
     */
    public function setAsistido($asistido) {
        $this->asistido = $asistido;

        return $this;
    }

    /**
     * Get asistido
     *
     * @return boolean
     */
    public function getAsistido() {
        return $this->asistido;
    }

    /**
     * Add sorteo
     *
     * @param Sorteo $sorteo
     *
     * @return Participante
     */
    public function addSorteo(Sorteo $sorteo)
    {
        $this->sorteos[] = $sorteo;

        return $this;
    }

    /**
     * Remove sorteo
     *
     * @param Sorteo $sorteo
     */
    public function removeSorteo(Sorteo $sorteo)
    {
        $this->sorteos->removeElement($sorteo);
    }

    /**
     * Get sorteos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSorteos()
    {
        return $this->sorteos;
    }

    /**
     * Add checkeo
     *
     * @param Checkeo $checkeo
     *
     * @return Participante
     */
    public function addCheckeo(Checkeo $checkeo)
    {
        $this->checkeos[] = $checkeo;

        return $this;
    }

    /**
     * Remove checkeo
     *
     * @param Checkeo $checkeo
     */
    public function removeCheckeo(Checkeo $checkeo)
    {
        $this->checkeos->removeElement($checkeo);
    }

    /**
     * Get checkeos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCheckeos()
    {
        return $this->checkeos;
    }
    
    public function __toString() {
        return $this->nombre . ' ' . $this->apellidos;
    }

    /**
     * Set horaEntrada
     *
     * @param DateTime $horaEntrada
     *
     * @return Participante
     */
    public function setHoraEntrada(DateTime $horaEntrada): static {
        $this->horaEntrada = $horaEntrada;

        return $this;
    }

    /**
     * Get horaEntrada
     *
     * @return DateTime
     */
    public function getHoraEntrada()
    {
        return $this->horaEntrada;
    }
}
