<?php

namespace Siriru\GSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tournament")
 */
class Tournament extends GoldsprintType
{
    public function __construct()
    {
        parent::__construct();
        $this->name = "tournament";
    }

    public function setLastStep()
    {
        //on rajoute un round pour la finale des perdants
        if ($this->getGoldsprint()->getPlayers()->count() == 0) $this->last_step = 1;
        else $this->last_step = 1 + log($this->getGoldsprint()->getPlayers()->count())/log(2);

        return $this;
    }

    public function start()
    {
        $players = $this->getGoldsprint()->getPlayers()->toArray();
        $this->createRound($players);
    }

    public function updateFirstRuns($players)
    {
        shuffle($players);
        foreach($this->getRuns() as $run) {
            //uniquement au step 1 et si le run est en solo
            if($run->getStep() == 1 and $run->getPlayer2() == null) {
                $run->setPlayer2(array_pop($players));
            }
        }
        $this->createRound($players);
    }

    public function nextStep()
    {
        //on augmente le step de 1
        $this->setStep($this->step+1);
        //si le gs n'est pas finito
        if(!$this->getGoldsprint()->getFinished()) {
            //on récupère la liste des qualifiés au step précédent
            $players = $this->getQualifiedPlayer($this->step - 1);
            //si c'est la finale, on crée la finale des perdants et on cloture le tournoi
            if($this->step == $this->last_step - 1) {
                $loosers = array();
                foreach($this->getRunsAtStep($this->step - 1) as $run) {
                    $loosers[] = $run->getLooser();
                }
                $this->createRound($loosers);
                $this->setStep($this->step+1);
                $this->createRound($players);
                $this->getGoldsprint()->setFinished(true);
            }
            else {
                $this->createRound($players);
            }
        }
    }

    public function isStepOver()
    {
        foreach($this->getRuns() as $run) {
            if($run->getStep() == $this->step and $run->getWinner() === null) return false;
        }
        return true;
    }

    public function getResults()
    {
        $results = array();
        $step = 1;
        while($step <= $this->last_step) {
            $temp = array();
            foreach($this->getRunsAtStep($step) as $run) {
                $temp[] = $run;
            }
            usort($temp, function($run1, $run2) {
                $time1 = $run1->getLooserTime();
                $time2 = $run2->getLooserTime();
                if ($time1 == $time2) {
                    return 0;
                }
                return ($time1 > $time2) ? -1 : 1;
            });

            foreach($temp as $run) {
                $results[] = $run->getLooser();
                if($step >= $this->last_step - 1) $results[] = $run->getWinner();
            }
            $step++;
            if($step == $this->last_step - 2) $step++;
        }

        return $results;
    }

    private function getQualifiedPlayer($step)
    {
        $players = array();
        foreach($this->getRunsAtStep($step) as $run) {
            $players[] = $run->getWinner();
        }
        return $players;
    }
}
