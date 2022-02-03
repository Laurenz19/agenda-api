<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CandidatRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=CandidatRepository::class)
 * @ApiResource(
 * attributes={
 *      "order":{"Nom":"desc"}
 * },
 * normalizationContext={
 *      "groups":{"candidat_read"}
 * },
 * denormalizationContext={
 *  "disable_type_enforcement"=true
 * })
 * 
 * @ApiFilter(SearchFilter::class, properties={"Nom":"partial"})
 * @ApiFilter(OrderFilter::class, properties={"Nom"})
 * @UniqueEntity(fields="email", message="email: {{ value }} appartient déjà à un autre candidat!")
 * @UniqueEntity(fields="tel", message="tel: {{ value }} appartient déjà à un autre candidat!")
 * @UniqueEntity(fields="Nom", message="{{ value }} Existe déjà dans la base de données!")
 */
class Candidat
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"candidat_read", "entretien_read",})
     */
    private $id;

   /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le nom est obligatoire!")
     * @Groups({"candidat_read", "entretien_read",})
     * @Assert\Type(
     *     type="String",
     *     message="le type du nom n'est pas valide (String)"
     * )
     * @Assert\Length(
     *      min=2,
     *      max=255,
     *      minMessage="Le nom doit avoir au minimum '{{ limit }}' caractères!",
     *      maxMessage="le nom doit avoir au maximum '{{ limit }}' caractères!"
     * )
     */
    private $Nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $diplome;

     /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="L'email est obligatoire!")
     * @Email(message="Le format de l'email: {{ value }} n'est pas valide!")
     * @Assert\Length(
     *      min=6,
     *      max=254,
     *      minMessage="Un email doit avoir au minimum '{{ limit }}' caractères!",
     *      maxMessage="Un email doit avoir au maximum '{{ limit }}' caractères!"
     * )
     * @Groups({"candidat_read", "entretien_read"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=10)
     * @Assert\NotBlank(message="Le tel est obligatoire!")
     * @Groups({"candidat_read", "entretien_read"})
     * @Assert\Type(
     *     type="string",
     *     message="le type du tel n'est pas valide (string)"
     * )
     * @Assert\Length(
     *      min=2,
     *      max=10,
     *      minMessage="Tel doit avoir au minimum '{{ limit }}' caractères!",
     *      maxMessage="Tel doit avoir au maximum '{{ limit }}' caractères!"
     * )
     */
    private $tel;

    /**
     * @ORM\Column(type="array", nullable=true)
     * @Groups({"candidat_read"})
     *  @Assert\Type(
     *     type="array",
     *     message="les compétences doit être un tableau (array)"
     * )
     */
    private $competences = [];

    /**
     * @ORM\OneToMany(targetEntity=Entretien::class, mappedBy="candidat", orphanRemoval=true)
     */
    private $entretiens;

    public function __construct()
    {
        $this->entretiens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): self
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getDiplome(): ?string
    {
        return $this->diplome;
    }

    public function setDiplome(?string $diplome): self
    {
        $this->diplome = $diplome;

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

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getCompetences(): ?array
    {
        return $this->competences;
    }

    public function setCompetences(?array $competences): self
    {
        $this->competences = $competences;

        return $this;
    }

    /**
     * @return Collection|Entretien[]
     */
    public function getEntretiens(): Collection
    {
        return $this->entretiens;
    }

    public function addEntretien(Entretien $entretien): self
    {
        if (!$this->entretiens->contains($entretien)) {
            $this->entretiens[] = $entretien;
            $entretien->setCandidat($this);
        }

        return $this;
    }

    public function removeEntretien(Entretien $entretien): self
    {
        if ($this->entretiens->removeElement($entretien)) {
            // set the owning side to null (unless already changed)
            if ($entretien->getCandidat() === $this) {
                $entretien->setCandidat(null);
            }
        }

        return $this;
    }
}
