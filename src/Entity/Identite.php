<?php

namespace App\Entity;

use App\Repository\IdentiteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IdentiteRepository::class)]
class Identite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 30)]
    private $nom;

    #[ORM\Column(type: 'string', length: 30)]
    private $prenom;

    #[ORM\Column(type: 'date')]
    private $dateNaissance;

    #[ORM\Column(type: 'string', length: 100)]
    private $lieuNaissance;

    #[ORM\Column(type: 'string', length: 100)]
    private $adresse;

    #[ORM\Column(type: 'integer')]
    private $codePostal;

    #[ORM\ManyToOne(targetEntity: Fichier::class, inversedBy: 'identites')]
    #[ORM\JoinColumn(nullable: false)]
    private $domicile;

    #[ORM\ManyToOne(targetEntity: Fichier::class, inversedBy: 'identites')]
    #[ORM\JoinColumn(nullable: false)]
    private $carte;

    #[ORM\Column(type: 'string', length: 100)]
    private $email;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getLieuNaissance(): ?string
    {
        return $this->lieuNaissance;
    }

    public function setLieuNaissance(string $lieuNaissance): self
    {
        $this->lieuNaissance = $lieuNaissance;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getCodePostal(): ?int
    {
        return $this->codePostal;
    }

    public function setCodePostal(int $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getDomicile(): ?Fichier
    {
        return $this->domicile;
    }

    public function setDomicile(?Fichier $domicile): self
    {
        $this->domicile = $domicile;

        return $this;
    }

    public function getCarte(): ?Fichier
    {
        return $this->carte;
    }

    public function setCarte(?Fichier $carte): self
    {
        $this->carte = $carte;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
}
