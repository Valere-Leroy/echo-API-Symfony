<?php

namespace App\Entity\Validator;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Représente le formulaire d'ajout d'un echo.
 * Etant donner que nous communiquons via API, nous devons valider
 * à la main les données reçu. Cette class sert à stocker les données reçu du formulaire React
 * et faire la validation, avant d'envoyer dans l'Entity EchoPost
 * 
 * /!\ Cette entity n'est pas et ne sera pas mapper par Doctrine /!\
 */

class EchoHandler
{
    /**
     * @Assert\NotBlank(message="Le titre est requis")
     *  @Assert\Length(
     *  min="1", minMessage="Votre titre doit contenir au moins un caractère",
     *  max="150", maxMessage="Votre titre ne doit pas contenir plus de 150 caractères"
     * )
     */
    protected $title;

    /**
     *  @Assert\Length(
     *  min="1", minMessage="Votre message doit contenir au moins un caractère"
     * )
     */
    protected $content;


    /**
     * @Assert\Regex(
     *  pattern="/\/*\.(jpg|jpeg|png)$/",
     *  match=true,
     *  message="L'image doit être au format png ou jpg|jpeg"
     * )
     */
    protected $image;

    /**
     * @Assert\NotBlank(message="Votre adresse est requise")
     * @Assert\Regex(
     *  pattern="/([0-9]{1,3}) ?([-a-zA-Zàâäéèêëïîôöùûüç,' \.]*) ((0[1-9])|([1-8][0-9])|(9[0-8])|(2A)|(2B))[0-9]{3} (\w+)/",
     *  match=true,
     *  message="Votre addresse n'est pas valide ou pas au bon format (Exemple: 147 rue de la guinguette 44000 Nantes)"
     * )
     */
    protected $adress;

    /**
     * @Assert\NotBlank(message="La latitude est requise")
     */
    protected $latitude;

    /**
     * @Assert\NotBlank(message="La longitude est requise")
     */
    protected $longitude;

    /**
     * Get min="1", minMessage="Votre titre doit contenir au moins un caractère"
     */ 
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set min="1", minMessage="Votre titre doit contenir au moins un caractère"
     *
     * @return  self
     */ 
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of content
     */ 
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the value of content
     *
     * @return  self
     */ 
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get pattern="/\/*\.(jpg|jpeg|png)$/",
     */ 
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set pattern="/\/*\.(jpg|jpeg|png)$/",
     *
     * @return  self
     */ 
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get pattern="/([0-9]{1,3}) ?([-a-zA-Zàâäéèêëïîôöùûüç, \.]*) ((0[1-9])|([1-8][0-9])|(9[0-8])|(2A)|(2B))[0-9]{3} (\w+)/",
     */ 
    public function getAdress()
    {
        return $this->adress;
    }

    /**
     * Set pattern="/([0-9]{1,3}) ?([-a-zA-Zàâäéèêëïîôöùûüç, \.]*) ((0[1-9])|([1-8][0-9])|(9[0-8])|(2A)|(2B))[0-9]{3} (\w+)/",
     *
     * @return  self
     */ 
    public function setAdress($adress)
    {
        $this->adress = $adress;

        return $this;
    }

    /**
     * Get the value of latitude
     */ 
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set the value of latitude
     *
     * @return  self
     */ 
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get the value of longitude
     */ 
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set the value of longitude
     *
     * @return  self
     */ 
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }
}