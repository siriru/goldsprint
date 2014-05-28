<?php

namespace Siriru\GSBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Entity
* @ORM\Table(name="goldsprint_type")
* @ORM\InheritanceType("JOINED")
* @ORM\DiscriminatorColumn(name="discr", type="string")
* @ORM\DiscriminatorMap({"tournament" = "Tournament", "championship" = "Championship", "championship_tournament" = "ChampionshipTournament", "free_session" = "FreeSession"})
*/

abstract class GoldsprintType
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
     * @ORM\Column(type="integer")
     */
    protected $step;

    /**
     * @ORM\Column(type="integer")
     */
    protected $last_step;

    /**
     * @ORM\OneToMany(targetEntity="Siriru\GSBundle\Entity\Run", mappedBy="type", cascade={"remove", "persist"})
     * @ORM\OrderBy({"step" = "ASC"})
     */
    protected $runs;

    /**
     * @ORM\OneToOne(targetEntity="Goldsprint", inversedBy="type")
     * @ORM\JoinColumn(nullable=false)
     **/
    protected $goldsprint;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->step = 1;
        $this->runs = new ArrayCollection();
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
     * Set step
     *
     * @param integer $step
     * @return GoldsprintType
     */
    public function setStep($step)
    {
        $this->step = $step;

        return $this;
    }

    /**
     * Get step
     *
     * @return integer 
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * Add runs
     *
     * @param \Siriru\GSBundle\Entity\Run $run
     * @return GoldsprintType
     */
    public function addRun(Run $run)
    {
        $this->runs[] = $run;

        return $this;
    }

    /**
     * Remove runs
     *
     * @param \Siriru\GSBundle\Entity\Run $run
     */
    public function removeRun(Run $run)
    {
        $this->runs->removeElement($run);
    }

    /**
     * Get runs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRuns()
    {
        return $this->runs;
    }

    /**
     * Set goldsprint
     *
     * @param \Siriru\GSBundle\Entity\Goldsprint $goldsprint
     * @return GoldsprintType
     */
    public function setGoldsprint(Goldsprint $goldsprint)
    {
        $this->goldsprint = $goldsprint;

        return $this;
    }

    /**
     * Get goldsprint
     *
     * @return \Siriru\GSBundle\Entity\Goldsprint
     */
    public function getGoldsprint()
    {
        return $this->goldsprint;
    }

    abstract public function start();

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    abstract function setLastStep();

    public function getLastStep()
    {
        return $this->last_step;
    }

    protected function createRound($players)
    {
        shuffle($players);
        $count = count($players)/2;
        for($i=0;$i<$count;$i++) {
            $this->createRun(array_pop($players), array_pop($players));
        }
        if(count($players) > 0) {
            $this->createRun(array_pop($players));
        }
    }

    protected function createRun(Player $p1=null, Player $p2=null)
    {
        $run = new Run($p1, $p2);
        $run->setStep($this->step);
        $run->setType($this);
        $this->addRun($run);
    }

    protected function getRunsAtStep($step)
    {
        $runs = array();
        foreach($this->getRuns() as $run) {
            if($run->getStep() == $step) $runs[] = $run;
        }
        return $runs;
    }

    public function formatForTwig()
    {
        $results = array();
        for($step=1;$step<=$this->last_step;$step++){
            $results[$step] = array();
            foreach($this->getRunsAtStep($step) as $run) {
                $results[$step][] = $run;
            }
        }

        return $results;
    }
}
