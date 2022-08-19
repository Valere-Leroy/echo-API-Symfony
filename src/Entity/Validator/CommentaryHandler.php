<?php

namespace App\Entity\Validator;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Représente le formulaire d'ajout d'un commentaire sur un echo.
 * Etant donner que nous communiquons via API, nous devons valider
 * à la main les données reçu. Cette class sert à stocker les données reçu du formulaire React
 * et faire la validation, avant d'envoyer dans l'Entity Commentary
 * 
 * /!\ Cette entity n'est pas et ne sera pas mapper par Doctrine /!\
 */

class CommentaryHandler
{
    /**
     * @Assert\NotBlank(message="Le contenu est requis")
     */
    protected $content;

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
}