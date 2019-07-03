<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExperienceVersionRepository")
 */
class ExperienceVersion
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $version = 1;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Experience", inversedBy="versions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $experience;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $state = 'writing';

    public function __construct(Experience $experience, int $version, string $title)
    {
        $this->experience = $experience;
        $this->version = $version;
        $this->title = $title;
        $this->description = '';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getExperience(): ?Experience
    {
        return $this->experience;
    }

    public function setExperience(?Experience $experience): self
    {
        $this->experience = $experience;

        return $this;
    }

    public function startNewVersion(): self
    {
        $newExperience = new ExperienceVersion($this->experience, $this->experience->currentVersionCount() + 1, $this->title);

        $newExperience->description = $this->description;

        return $newExperience;
    }

    public function version(): int
    {
        return $this->version;
    }

    public function setVersion(int $version)
    {
        $this->version = $version;
    }

    public function __toString()
    {
        return (string) $this->version;
    }

    public function setState($state)
    {
        $this->state = $state;
    }

    public function getState()
    {
        return $this->state;
    }

    public function canBeEdited(): bool
    {
        return $this->state !== 'accepted';
    }

    public function canBePromotedAsLiveVersion(): bool
    {
        return $this->isAccepted() && !$this->isTheSelectedVersion();
    }

    public function isAccepted(): bool
    {
        return $this->state === 'accepted';
    }

    public function isTheSelectedVersion(): bool
    {
        return $this->experience->selectedVersion() == $this;
    }
}
