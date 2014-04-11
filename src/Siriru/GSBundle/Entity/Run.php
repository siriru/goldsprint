<?php

namespace Siriru\GSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="run")
 */
class Run
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $step;

    /**
     * @ORM\ManyToOne(targetEntity="Siriru\GSBundle\Entity\Player")
     */
    protected $player1;

    /**
     * @ORM\ManyToOne(targetEntity="Siriru\GSBundle\Entity\Player")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $player2;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $time1;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $time2;

    /**
     * @ORM\ManyToOne(targetEntity="Siriru\GSBundle\Entity\GoldsprintType", inversedBy="runs")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $type;

    /**
     * Constructor
     */
    public function __construct(Player $player1, Player $player2 = null)
    {
        $this->player1 = $player1;
        $this->player2 = $player2;
        $this->time1 = 0;
        $this->time2 = 0;
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
     * @return Run
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
     * Set time1
     *
     * @param float $time1
     * @return Run
     */
    public function setTime1($time1 = null)
    {
        $this->time1 = $time1;

        return $this;
    }

    /**
     * Get time1
     *
     * @return float
     */
    public function getTime1()
    {
        return $this->time1;
    }

    /**
     * Set time2
     *
     * @param float $time2
     * @return Run
     */
    public function setTime2($time2 = null)
    {
        $this->time2 = $time2;

        return $this;
    }

    /**
     * Get time2
     *
     * @return float
     */
    public function getTime2()
    {
        return $this->time2;
    }

    /**
     * Set player1
     *
     * @param \Siriru\GSBundle\Entity\Player $player1
     * @return Run
     */
    public function setPlayer1(Player $player1 = null)
    {
        $this->player1 = $player1;

        return $this;
    }

    /**
     * Get player1
     *
     * @return \Siriru\GSBundle\Entity\Player 
     */
    public function getPlayer1()
    {
        return $this->player1;
    }

    /**
     * Set player2
     *
     * @param \Siriru\GSBundle\Entity\Player $player2
     * @return Run
     */
    public function setPlayer2(Player $player2 = null)
    {
        $this->player2 = $player2;

        return $this;
    }

    /**
     * Get player2
     *
     * @return \Siriru\GSBundle\Entity\Player 
     */
    public function getPlayer2()
    {
        return $this->player2;
    }

    /**
     * Get winner
     *
     * @return \Siriru\GSBundle\Entity\Player 
     */
    public function getWinner()
    {
        if($this->time1 != null and $this->time2 != null) {
            if($this->time1 == $this->time2) return false;
            elseif($this->time1 < $this->time2) return $this->player1;
            else return $this->player2;
        }
        else return false;
    }

    /**
     * Set type
     *
     * @param \Siriru\GSBundle\Entity\GoldsprintType $type
     * @return Run
     */
    public function setType(GoldsprintType $type)
    {
        $this->type = $type;

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

    public function __toString()
    {
        $player2 = $this->player2 === null ? 'Ghost' : $this->player2;
        return $this->player1.' VS '.$player2;
    }

    public function getLooser()
    {
        $winner = $this->getWinner();
        if($winner) {
            if($winner == $this->player1) return $this->player2;
            else return $this->player1;
        }
        else return false;
    }

    public function getWinnerTime()
    {
        $winner = $this->getWinner();
        if($winner) {
            if($winner == $this->player1) return $this->time1;
            else return $this->time2;
        }
        else return 0;
    }

    public function getLooserTime()
    {
        $looser = $this->getLooser();
        if($looser) {
            if($looser == $this->player1) return $this->time1;
            else return $this->time2;
        }
        else return 0;
    }
}
