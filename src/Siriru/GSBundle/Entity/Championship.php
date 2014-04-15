<?php

namespace Siriru\GSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="championship")
 */
class Championship extends GoldsprintType
{
    public function __construct()
    {
        parent::__construct();
        $this->name = "championship";
    }

    public function setLastStep()
    {
        $this->last_step = 2;

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
            //on récupère la liste des joueurs
            $players = $this->getGoldsprint()->getPlayers()->toArray();
            $this->createRound($players);
            if($this->step == $this->last_step) $this->getGoldsprint()->setFinished(true);
        }
    }

    public function isStepOver()
    {
        foreach($this->getRunsAtStep($this->step) as $run) {
            if($run->getTime1() === null or $run->getTime2() === null) return false;
        }
        return true;
    }

    public function getResults()
    {
        $results = array();
        $step = 1;
        while($step <= $this->step) {
            foreach($this->getRunsAtStep($step) as $run) {
                $player1 = $run->getPlayer1();
                $player2 = $run->getPlayer2();
                if(!array_key_exists($player1->getName(), $results)) {
                    $results[$player1->getName()] = array(
                        'object' => $player1,
                        'time' => array($run->getTime1() == 0 ? null : $run->getTime1())
                    );
                }
                else $results[$player1->getName()]['time'][] = $run->getTime1() == 0 ? null : $run->getTime1();

                if(!array_key_exists($player2->getName(), $results)) {
                    $results[$player2->getName()] = array(
                        'object' => $player2,
                        'time' => array($run->getTime2() == 0 ? null : $run->getTime2())
                    );
                }
                else $results[$player2->getName()]['time'][] = $run->getTime2() == 0 ? null : $run->getTime2();
            }
            $step++;
        }
        //var_dump($results);exit();
        return $this->sortResults($results);
    }

    private function sortResults($results)
    {
        usort($results, function($player1, $player2) {
            $times1 = $player1['time']; usort($times1, array($this, 'sortTime'));
            $times2 = $player2['time']; usort($times2, array($this, 'sortTime'));

            if ($times1[0] == $times2[0]) return 0;
            return ($times1[0] > $times2[0]) ? 1 : -1;
        });
        return $results;
    }

    private function sortTime($time1, $time2)
    {
        if($time1 == $time2) return 0;
        if($time1 == null) return 1;
        if($time2 == null) return -1;
        return ($time1 > $time2) ? 1 : -1;
    }
}