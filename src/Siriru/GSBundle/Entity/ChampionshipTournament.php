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
        foreach($this->getRuns() as $run) {
            if($run->getStep() == $this->step and $run->getTime1() === null or $run->getStep() == $this->step and $run->getTime2() === null) return true;
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

    public function getResults()
    {
    }

    private function createRound($players)
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

    private function createRun(Player $p1, Player $p2=null)
    {
        $run = new Run($p1, $p2);
        $run->setStep($this->step);
        $run->setType($this);
        $this->addRun($run);
    }

    private function getRunsAtStep($step)
    {
        $runs = array();
        foreach($this->getRuns() as $run) {
            if($run->getStep() == $step) $runs[] = $run;
        }
        return $runs;
    }
}