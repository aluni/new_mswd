<?php

namespace App\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Institucion
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Institucion extends Usuario {

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=50)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=100)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text")
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="logo", type="string", length=50)
     */
    private $logo;

    /**
     * @var integer
     *
     * @ORM\Column(name="rango", type="integer")
     */
    private $rango;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=50)
     */
    private $alias;

    /**
     * @ORM\OneToMany(targetEntity="Checkeo", mappedBy="institucion") 
     */
    private $checkeos;

    /**
     * @ORM\OneToMany(targetEntity="Sorteo", mappedBy="institucion") 
     */
    private $sorteos;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $logotipo;

    public function getAbsolutePath() {
        return null === $this->logo ? null : $this->getUploadRootDir() . '/' . $this->logo;
    }

    public function getWebPath() {
        return null === $this->logo ? null : $this->getUploadDir() . '/' . $this->logo;
    }

    protected function getUploadRootDir() {
        // the absolute directory logo where uploaded
        // documents should be saved
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir() {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'logos';
    }

    /**
     * Sets logotipo.
     *
     * @param UploadedFile $logotipo
     */
    public function setLogotipo(UploadedFile $logotipo = null) {
        $this->logotipo = $logotipo;
        // check if we have an old image logo
        if (isset($this->logo)) {
            // store the old name to delete after the update
            $this->temp = $this->logo;
            $this->logo = null;
        } else {
            $this->logo = 'initial';
        }
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload() {
        if (null !== $this->getLogotipo()) {
            // do whatever you want to generate a unique name
            $logotiponame = sha1(uniqid(mt_rand(), true));
            $this->logo = $logotiponame . '.' . $this->getLogotipo()->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload() {
        if (null === $this->getLogotipo()) {
            return;
        }

        // if there is an error when moving the logotipo, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getLogotipo()->move($this->getUploadRootDir(), $this->logo);

        // check if we have an old image
        if (isset($this->temp) && is_file($this->getUploadRootDir() . '/' . $this->temp)) {
            // delete the old image
            unlink($this->getUploadRootDir() . '/' . $this->temp);
            // clear the temp image logo
            $this->temp = null;
        }
        $this->logotipo = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload() {
        if (is_file($this->getAbsolutePath())) {
            unlink($this->getAbsolutePath());
        }
    }

    /**
     * Get logotipo.
     *
     * @return UploadedFile
     */
    public function getLogotipo() {
        return $this->logotipo;
    }

    /**
     * @ORM\PrePersist()
     */
    public function crearUsuarioInstitucion() {
        $this->addRole('ROLE_INSTITUCION');
        $alias = $this->getAlias();
        $this->setUsername($alias);
        $this->setEmail($alias);
        $pass = 'mswd' . substr($alias, -2) . substr($alias, 0, 2);
        $this->setPlainPassword($pass);
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Institucion
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
     * Set url
     *
     * @param string $url
     *
     * @return Institucion
     */
    public function setUrl($url) {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Institucion
     */
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion() {
        return $this->descripcion;
    }

    /**
     * Set logo
     *
     * @param string $logo
     *
     * @return Institucion
     */
    public function setLogo($logo) {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return string
     */
    public function getLogo() {
        return $this->logo;
    }

    /**
     * Set rango
     *
     * @param integer $rango
     *
     * @return Institucion
     */
    public function setRango($rango) {
        $this->rango = $rango;

        return $this;
    }

    /**
     * Get rango
     *
     * @return integer
     */
    public function getRango() {
        return $this->rango;
    }

    /**
     * Set alias
     *
     * @param string $alias
     *
     * @return Institucion
     */
    public function setAlias($alias) {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias() {
        return $this->alias;
    }

    /**
     * Add checkeo
     *
     * @param \App\Entity\Checkeo $checkeo
     *
     * @return Institucion
     */
    public function addCheckeo(\App\Entity\Checkeo $checkeo) {
        $this->checkeos[] = $checkeo;

        return $this;
    }

    /**
     * Remove checkeo
     *
     * @param \App\Entity\Checkeo $checkeo
     */
    public function removeCheckeo(\App\Entity\Checkeo $checkeo) {
        $this->checkeos->removeElement($checkeo);
    }

    /**
     * Get checkeos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCheckeos() {
        return $this->checkeos;
    }

    /**
     * Add sorteo
     *
     * @param \App\Entity\Sorteo $sorteo
     *
     * @return Institucion
     */
    public function addSorteo(\App\Entity\Sorteo $sorteo) {
        $this->sorteos[] = $sorteo;

        return $this;
    }

    /**
     * Remove sorteo
     *
     * @param \App\Entity\Sorteo $sorteo
     */
    public function removeSorteo(\App\Entity\Sorteo $sorteo) {
        $this->sorteos->removeElement($sorteo);
    }

    /**
     * Get sorteos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSorteos() {
        return $this->sorteos;
    }

    public function __toString() {
        return $this->nombre;
    }

}
