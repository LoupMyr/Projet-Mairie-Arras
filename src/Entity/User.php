<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;   
    
    /**  
    * @ORM\Column(type="string", length=30)     
    */    
    private $nom;    
    
    /**     
    * @ORM\Column(type="string", length=30)     
    */    
    private $prenom; 
                  
    #[ORM\OneToMany(mappedBy:"proprietaire",targetEntity:Fichier::class,)] 
    
    private $fichiers;

    #[ORM\ManyToMany(targetEntity: Fichier::class, mappedBy: 'partager')]
    private $fichiersPartager;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Telecharger::class, orphanRemoval: true)]
    private $telechargers;

    public function __construct()    
    {
        $this->fichiers = new ArrayCollection();
        $this->fichiersPartager = new ArrayCollection();
        $this->telechargers = new ArrayCollection();    
    }

    public function getId(): ?int    
    {        
        return $this->id;    
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
        /**     
        * A visual identifier that represents this user.    
        * @see UserInterface     
        */    
        public function getUserIdentifier(): string    
        {        
            return (string) $this->email;    
        }    
        /**     
        * @deprecated since Symfony 5.3, use getUserIdentifier instead     
        */    
        
        public function getUsername(): string    
        {        
            return (string) $this->email;    
        }    
        /**     
        * @see UserInterface     
        */    
        
        public function getRoles(): array    
        {        
            $roles = $this->roles;        
            // guarantee every user at least has ROLE_USER        
            $roles[] = 'ROLE_USER';        
            return array_unique($roles);
        }    
        
        public function setRoles(array $roles): self    
        {        
            $this->roles = $roles;        
            return $this;    
        }    
        /**     
        * @see PasswordAuthenticatedUserInterface     
        */    
        public function getPassword(): string    
        {        
            return $this->password;    
        }    
        
        public function setPassword(string $password): self    
        {        
            $this->password = $password;        
            return $this;    
        }    
        /**     
        * Returning a salt is only needed, if you are not using a modern     
        * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.     
        * @see UserInterface     
        */    

        public function getSalt(): ?string    
        {        
            return null;    
        }    
        /**     
        * @see UserInterface     
        */    
        public function eraseCredentials()    
        {        
            // If you store any temporary, sensitive data on the user, clear it here
            // $this->plainPassword = null;    
        }    
        
        public function isVerified(): bool    
        {        
            return $this->isVerified;    
        }

        public function setIsVerified(bool $isVerified): self
     {        
        $this->isVerified = $isVerified;        
        return $this;    
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
    /**     
    * @return Collection|Fichier[]     
    */    
    public function getFichiers(): Collection    
    {        
        return $this->fichiers;    
    }
    public function addFichier(Fichier $fichier): self    
    {        
        if (!$this->fichiers->contains($fichier)) 
        {            
            $this->fichiers[] = $fichier; $fichier->setProprietaire($this);        
        }        
        return $this;    
    }    
    public function removeFichier(Fichier $fichier): self    
    {        
        if ($this->fichiers->removeElement($fichier)) {
        // set the owning side to null (unless already changed)            
        if ($fichier->getProprietaire() === $this) 
        {                
            $fichier->setProprietaire(null);            
        }        
    }        
    return $this;    
}

    /**
     * @return Collection|Fichier[]
     */
    public function getFichiersPartager(): Collection
    {
        return $this->fichiersPartager;
    }

    public function addFichiersPartager(Fichier $fichiersPartager): self
    {
        if (!$this->fichiersPartager->contains($fichiersPartager)) {
            $this->fichiersPartager[] = $fichiersPartager;
            $fichiersPartager->addPartager($this);
        }

        return $this;
    }

    public function removeFichiersPartager(Fichier $fichiersPartager): self
    {
        if ($this->fichiersPartager->removeElement($fichiersPartager)) {
            $fichiersPartager->removePartager($this);
        }

        return $this;
    }

    /**
     * @return Collection|Telecharger[]
     */
    public function getTelechargers(): Collection
    {
        return $this->telechargers;
    }

    public function addTelecharger(Telecharger $telecharger): self
    {
        if (!$this->telechargers->contains($telecharger)) {
            $this->telechargers[] = $telecharger;
            $telecharger->setUser($this);
        }

        return $this;
    }

    public function removeTelecharger(Telecharger $telecharger): self
    {
        if ($this->telechargers->removeElement($telecharger)) {
            // set the owning side to null (unless already changed)
            if ($telecharger->getUser() === $this) {
                $telecharger->setUser(null);
            }
        }

        return $this;
    }
}