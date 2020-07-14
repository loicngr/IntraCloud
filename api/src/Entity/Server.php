<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ServerRepository")
 */
class Server
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $port;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Document", mappedBy="server")
     */
    private $documents;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $username;

    /**
     * @var string The hashed password
     * @ORM\Column(type="text")
     */
    private $password;

    /**
     * @var string The hashed privateKey
     * @ORM\Column(type="text", nullable=true)
     */
    private $privateKey;

    /**
     * @var string The hashed passphrase
     * @ORM\Column(type="text", nullable=true)
     */
    private $passphrase;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $defaultPath;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="json")
     */
    private $acceptedRoles = [];

    public function __construct()
    {
        $this->documents = new ArrayCollection();
    }

    /**
     * @return integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPort(): ?string
    {
        return $this->port;
    }

    public function setPort(string $port): self
    {
        $this->port = $port;

        return $this;
    }

    /**
     * @return Collection|Document[]
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setServer($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
            // set the owning side to null (unless already changed)
            if ($document->getServer() === $this) {
                $document->setServer(null);
            }
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPrivateKey(): ?string
    {
        return $this->privateKey;
    }

    public function setPrivateKey(?string $privateKey): self
    {
        $this->privateKey = $privateKey;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassphrase(): ?string
    {
        return $this->passphrase;
    }

    public function setPassphrase(?string $passphrase): self
    {
        $this->passphrase = $passphrase;

        return $this;
    }

    public function getDefaultPath(): ?string
    {
        return $this->defaultPath;
    }

    public function setDefaultPath(string $defaultPath): self
    {
        $this->defaultPath = $defaultPath;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAcceptedRoles(): ?array
    {
		$roles = $this->acceptedRoles;
		if (!in_array('ROLE_ADMIN', $roles)):
			$roles[] = 'ROLE_ADMIN';
		endif;

		return array_unique($roles);
    }

    public function setAcceptedRoles(array $acceptedRoles): self
    {
        $this->acceptedRoles = $acceptedRoles;

        return $this;
    }
}
