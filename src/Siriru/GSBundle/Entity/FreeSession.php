<?php

namespace Siriru\GSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="free_session")
 */
class FreeSession extends GoldsprintType
{
    public function __construct()
    {
        parent::__construct();
        $this->name = "free-session";
    }

    public function setLastStep()
    {
        $this->last_step = 1;

        return $this;
    }

    public function start()
    {
        $players = $this->getGoldsprint()->getPlayers()->toArray();
        $this->createRound($players);
    }

    public function updateFirstRuns()
    {

    }

    public function nextStep()
    {
        $this->getGoldsprint()->setFinished(true);
    }

    public function isStepOver()
    {
        return true;
    }

    public function newRun()
    {
        $this->createRun();
    }

    public function getResults()
    {
        $results = array();
        foreach($this->getRuns() as $run) {
            $player1 = $run->getPlayer1();
            $player2 = $run->getPlayer2();
            if($player2 !== null) {
                if(!array_key_exists($player1->getName(), $results)) {
                    $results[$player1->getName()] = array(
                        'object' => $player1,
                        'time' => $run->getTime1()
                    );
                }
                else {
                    if ($results[$player1->getName()]['time'] > $run->getTime1()) $results[$player1->getName()]['time'] = $run->getTime1();
                }
            }

            if($player2 !== null) {
                if(!array_key_exists($player2->getName(), $results)) {
                    $results[$player2->getName()] = array(
                        'object' => $player2,
                        'time' => $run->getTime2()
                    );
                }
                else {
                    if ($results[$player2->getName()]['time'] > $run->getTime2()) $results[$player2->getName()]['time'] = $run->getTime2();
                }
            }
        }
        return $this->sortResults($results);
    }

    private function sortResults($results)
    {
        usort($results, function($player1, $player2) {
            $times1 = $player1['time'];
            $times2 = $player2['time'];

            if($times1 !== $times2) return ($times1 > $times2) ? 1 : -1;
            elseif($times1 === null) return 1;
            elseif($times2 === null) return -1;

            return 0;
        });
        return $results;
    }

    public function getBestTimes()
    {
        $results = array();
        foreach($this->runs as $run) {
            $results = $this->checkIfBestTime($results, $run->getPlayer1(), $run->getTime1());
            $results = $this->checkIfBestTime($results, $run->getPlayer2(), $run->getTime2());
        }
        asort($results);
        return $results;
    }

    private function checkIfBestTime($results, $player, $time)
    {
        if($time !== null) {
            if(!array_key_exists($player->getName(), $results)) $results[$player->getName()] = $time;
            else {
                if($results[$player->getName()] > $time) $results[$player->getName()] = $time;
            }
        }
        return $results;
    }
}