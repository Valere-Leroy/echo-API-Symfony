<?php

namespace App\Entity\Validator;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Représente le formulaire d'inscription.
 * Etant donner que nous communiquons via API, nous devons valider
 * à la main les données reçu. Cette class sert à stocker les données reçu du formulaire React
 * et faire la validation, avant d'envoyer dans l'Entity User
 * 
 * /!\ Cette entity n'est pas et ne sera pas mapper par Doctrine /!\
 */

class RegistrationHandler 
{
    /**
     * @Assert\NotBlank(message="Le nom de compte est requis")
     *  @Assert\Length(
     *  min="5", minMessage="Votre nom de compte doit contenir entre 8 et 40 caractères",
     *  max="40", maxMessage="Votre nom de compte doit contenir entre 8 et 40 caractères "
     * )
     */
    protected $username;

    /**
     * @Assert\NotBlank(message="L'Email est requis")
     * @Assert\Email(
     *  message="Adresse email invalide"
     * )
     */
    protected $email;

    /**
     *  @Assert\Length(
     *  min="5", minMessage="Votre mot de passe doit contenir entre 5 et 36 caractères",
     *  max="36", maxMessage="Votre de passe doit contenir entre 5 et 36 caractères"
     * )
     * @Assert\Regex(
     *  pattern="/^(?i)^(?=.*[a-z])(?=.*\d)/i",
     *  match=true,
     *  message="Votre mot de passe doit contenir au moins une lettre et un chiffre"
     * )
     */
    protected $password;

    /**
     * @Assert\EqualTo(propertyPath="password", message="Vos mots de passe ne correspondent pas")
     */
    protected $confirmPassword;

    /**
     * Get the value of username
     */ 
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the value of username
     *
     * @return  self
     */ 
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get the value of email
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */ 
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of password
     */ 
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */ 
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of confirmPassword
     */ 
    public function getConfirmPassword()
    {
        return $this->confirmPassword;
    }

    /**
     * Set the value of confirmPassword
     *
     * @return  self
     */ 
    public function setConfirmPassword($confirmPassword)
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }
}