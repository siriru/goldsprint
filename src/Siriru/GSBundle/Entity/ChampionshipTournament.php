<?php

namespace Siriru\GSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="championship_tournament")
 */
class ChampionshipTournament extends GoldsprintType
{
    /**
     * @ORM\Column(type="integer")
     */
    protected $nb_championship_round = 2;

    /**
     * @ORM\Column(type="integer")
     */
    protected $nb_qualified_player = 8;

    public function __construct()
    {
        parent::__construct();
        $this->name = "championship-tournament";
    }

    /**
     * @param mixed $nb_championship_round
     */
    public function setNbChampionshipRound($nb_championship_round)
    {
        $this->nb_championship_round = $nb_championship_round;
    }

    /**
     * @return mixed
     */
    public function getNbChampionshipRound()
    {
        return $this->nb_championship_round;
    }

    /**
     * @param mixed $nb_qualified_player
     */
    public function setNbQualifiedPlayer($nb_qualified_player)
    {
        $this->nb_qualified_player = $nb_qualified_player;
    }

    /**
     * @return mixed
     */
    public function getNbQualifiedPlayer()
    {
        return $this->nb_qualified_player;
    }

    public function setLastStep()
    {
        if ($this->getGoldsprint()->getPlayers()->count() == 0) $this->last_step = 1;
        //on ajoute 1 pour la finale des perdants
        else $this->last_step = 1 + log($this->nb_qualified_player)/log(2) + $this->nb_championship_round;

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
            //si on est encore dans la phase championnat on refait un round avec tout le monde
            if($this->step <= $this->nb_championship_round) {
                $players = $this->getGoldsprint()->getPlayers()->toArray();
                $this->createRound($players);
            }
            //si le step est supérieur au nombre de round du championnat préliminaire, on passe au tournoi
            elseif($this->step == $this->nb_championship_round + 1) {
                $championship_results = array_slice($this->getChampionshipResults(), 0, $this->nb_qualified_player, true);
                $qualified_players = array();
                foreach($championship_results as $result) {
                    $qualified_players[] = $result['object'];
                }
                $this->createRound($qualified_players);
            }
            else {
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
    }

    public function isStepOver()
    {
        if($this->step <= $this->nb_championship_round) {
            foreach($this->getRunsAtStep($this->step) as $run) {
                if($run->getTime1() === null or $run->getTime2() === null) return false;
            }
        }
        else {
            foreach($this->getRunsAtStep($this->step) as $run) {
                if($run->getWinner() === null) return false;
            }
        }
        return true;
    }

    private function getQualifiedPlayer($step)
    {
        $players = array();
        foreach($this->getRunsAtStep($step) as $run) {
            $players[] = $run->getWinner();
        }
        return $players;
    }

    public function getChampionshipResults()
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
            //si on arrive à la fin du championnat on arrête
            if($step > $this->nb_championship_round) break;
        }
        return $this->sortResults($results);
    }

    private function sortResults($results)
    {
        usort($results, function($player1, $player2) {
            $times1 = $player1['time']; usort($times1, array($this, 'sortTime'));
            $times2 = $player2['time']; usort($times2, array($this, 'sortTime'));

            for($i=0;$i<count($times1);$i++) {
                if($times1[$i] !== $times2[$i]) return ($times1[$i] > $times2[$i]) ? 1 : -1;
                elseif($times1[$i] === null) return 1;
                elseif($times2[$i] === null) return -1;
            }
            return 0;
        });
        return $results;
    }

    private function sortTime($time1, $time2)
    {
        if($time1 === $time2) return 0;
        if($time1 === null) return 1;
        if($time2 === null) return -1;
        return ($time1 > $time2) ? 1 : -1;
    }

    public function getTournamentResults()
    {
        $results = array();
        $step = $this->nb_championship_round + 1;
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

        return array_reverse($results);
    }

    public function getResults()
    {
        $results = $this->getTournamentResults();
        $end_of_championship = array_slice($this->getChampionshipResults(), $this->nb_qualified_player, null, true);
        foreach($end_of_championship as $result) {
            $results[] = $result['object'];
        }
        return $results;
    }
}