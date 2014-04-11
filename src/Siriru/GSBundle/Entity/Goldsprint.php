<?php

namespace Siriru\GSBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Siriru\GSBundle\Entity\GoldsprintRepository")
 * @ORM\Table(name="goldsprint")
 */
class Goldsprint
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $name;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $date;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $location;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $description;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $started;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $finished;

    /**
     * @ORM\OneToOne(targetEntity="GoldsprintType", mappedBy="goldsprint", cascade={"remove", "persist"})
     */
    protected $type;

    /**
     * @ORM\ManyToMany(targetEntity="Player")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    protected $players;

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->players = new ArrayCollection();
        $this->created = new \DateTime();
        $this->started = false;
        $this->finished = false;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Goldsprint
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return Goldsprint
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Goldsprint
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add players
     *
     * @param \Siriru\GSBundle\Entity\Player $players
     * @return Goldsprint
     */
    public function addPlayer(Player $players)
    {
        $this->players[] = $players;

        return $this;
    }

    /**
     * Remove players
     *
     * @param \Siriru\GSBundle\Entity\Player $players
     */
    public function removePlayer(Player $players)
    {
        $this->players->removeElement($players);
    }

    /**
     * Get players
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Goldsprint
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Goldsprint
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set started
     *
     * @param boolean $started
     * @return Goldsprint
     */
    public function setStarted($started)
    {
        $this->started = $started;

        return $this;
    }

    /**
     * Get started
     *
     * @return boolean 
     */
    public function getStarted()
    {
        return $this->started;
    }

    /**
     * Set finished
     *
     * @param boolean $finished
     * @return Goldsprint
     */
    public function setFinished($finished)
    {
        $this->finished = $finished;

        return $this;
    }

    /**
     * Get finished
     *
     * @return boolean 
     */
    public function getFinished()
    {
        return $this->finished;
    }

    /**
     * Set type
     *
     * @param \Siriru\GSBundle\Entity\GoldsprintType $type
     * @return Goldsprint
     */
    public function setType(GoldsprintType $type = null)
    {
        $this->type = $type;
        $this->type->setGoldsprint($this);
        return $this;
    }

    /**
     * Get type
     *
     * @return \Siriru\GSBundle\Entity\GoldsprintType 
     */
    public function getType()
    {
        return $this->type;
    }

    public function getOtherPlayers($players)
    {
        $results = new ArrayCollection();
        foreach($players as $player) {
            if(!$this->players->contains($player)) $results->add($player);
        }

        return $results;
    }
}
