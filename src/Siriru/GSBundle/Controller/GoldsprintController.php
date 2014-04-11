<?php

namespace Siriru\GSBundle\Controller;

use Siriru\GSBundle\Entity\Championship;
use Siriru\GSBundle\Entity\ChampionshipTournament;
use Siriru\GSBundle\Entity\Match;
use Siriru\GSBundle\Entity\Player;
use Siriru\GSBundle\Entity\Tournament;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Siriru\GSBundle\Entity\Goldsprint;
use Siriru\GSBundle\Form\GoldsprintType;

/**
 * Goldsprint controller.
 *
 * @Route("/goldsprint")
 */
class GoldsprintController extends Controller
{

    /**
     * Lists all Goldsprint entities.
     *
     * @Route("/", name="goldsprint")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SiriruGSBundle:Goldsprint')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Goldsprint entity.
     *
     * @Route("/", name="goldsprint_create")
     * @Method("POST")
     * @Template("SiriruGSBundle:Goldsprint:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Goldsprint();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('goldsprint_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Goldsprint entity.
    *
    * @param Goldsprint $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Goldsprint $entity)
    {
        $form = $this->createForm(new GoldsprintType(), $entity, array(
            'action' => $this->generateUrl('goldsprint_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Goldsprint entity.
     *
     * @Route("/new", name="goldsprint_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Goldsprint();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Goldsprint entity.
     *
     * @Route("/{id}", name="goldsprint_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SiriruGSBundle:Goldsprint')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Goldsprint entity.');
        }
        if($entity->getStarted()) return $this->render('SiriruGSBundle:Goldsprint:show.html.twig', array('entity' => $entity));
        else return $this->render('SiriruGSBundle:Goldsprint:config.html.twig', array('entity' => $entity));
    }

    /**
     * Displays a form to edit an existing Goldsprint entity.
     *
     * @Route("/{id}/edit", name="goldsprint_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SiriruGSBundle:Goldsprint')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Goldsprint entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Goldsprint entity.
    *
    * @param Goldsprint $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Goldsprint $entity)
    {
        $form = $this->createForm(new GoldsprintType(), $entity, array(
            'action' => $this->generateUrl('goldsprint_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Goldsprint entity.
     *
     * @Route("/{id}", name="goldsprint_update")
     * @Method("PUT")
     * @Template("SiriruGSBundle:Goldsprint:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SiriruGSBundle:Goldsprint')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Goldsprint entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('goldsprint'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Goldsprint entity.
     *
     * @Route("/{id}", name="goldsprint_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SiriruGSBundle:Goldsprint')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Goldsprint entity.');
            }
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('goldsprint'));
    }

    /**
     * Creates a form to delete a Goldsprint entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('goldsprint_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    /**
     * Start the GS with the selected option
     *
     * @Route("/{id}/start/{type}", name="goldsprint_start")
     * @Method("GET")
     */
    public function startAction($id, $type)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SiriruGSBundle:Goldsprint')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Goldsprint entity.');
        }

        if (!$entity->getStarted()) {
            switch($type) {
                case "tournament":
                    $type = new Tournament();
                    break;
                case "championship":
                    $type = new Championship();
                    break;
                case "championship-tournament":
                    $type = new ChampionshipTournament();
                    break;
                default:
                    throw $this->createNotFoundException('Unable to start with type '.$type);

            }
            $entity->setType($type);
            $type->setLastStep();
            $type->start();
            $entity->setStarted(true);
            $em->persist($entity);
            $em->persist($type);
            $em->flush();

            return $this->redirect($this->generateUrl('goldsprint_show', array('id' => $entity->getId())));
        }

        else throw $this->createNotFoundException('Goldsprint is already started !!');
    }

    /**
     * Next step
     *
     * @Route("/{id}/new-players", name="goldsprint_new_players")
     * @Method("GET")
     * @Template("SiriruGSBundle:Goldsprint:addPlayers.html.twig")
     */
    public function newPlayersAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SiriruGSBundle:Goldsprint')->find($id);
        if (!$entity) throw $this->createNotFoundException('Unable to find Goldsprint entity.');

        if ($entity->getType()->getStep() > 1) throw $this->createNotFoundException('It is not possible to add players after the first step');

        $players = $em->getRepository('SiriruGSBundle:Player')->findByEnabled(true);

        $players = $entity->getOtherPlayers($players);

        return array('entity' => $entity, 'players' => $players);
    }

    /**
     * Next step
     *
     * @Route("/{id}/add-players", name="goldsprint_add_players")
     * @Method("POST")
     */
    public function addPlayersAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $players = $request->request->get('players');

        $goldsprint = $em->getRepository('SiriruGSBundle:Goldsprint')->find($id);
        if (!$goldsprint) throw $this->createNotFoundException('Unable to find Goldsprint entity.');

        $players = $em->getRepository('SiriruGSBundle:Player')->findById($players);
        foreach($players as $player) {
            if(!$goldsprint->getPlayers()->contains($player)) $goldsprint->addPlayer($player);
        }
        //il faut créer de nouveaux matchs pour les nouveaux joueurs $goldsprint->getType()->updateFirstRuns($players);
        $type = $goldsprint->getType();
        $type->setLastStep();
        $type->updateFirstRuns($players);
        $em->flush();

        return $this->redirect($this->generateUrl('goldsprint_show', array('id' => $id)));
    }

    /**
     * Next step
     *
     * @Route("/{id}/next-step", name="goldsprint_next_step")
     * @Method("GET")
     */
    public function nextStepAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SiriruGSBundle:Goldsprint')->find($id);
        if (!$entity) throw $this->createNotFoundException('Unable to find Goldsprint entity.');

        if (!$entity->getStarted()) throw $this->createNotFoundException('Goldsprint is not started !');

        if ($entity->getPlayers()->count() == 0) throw $this->createNotFoundException('Goldsprint needs more players !');

        if ($entity->getType()->isStepOver()) {
            $type = $entity->getType();
            $type->nextStep();
            $em->persist($type);
            $em->flush();

            return $this->redirect($this->generateUrl('goldsprint_show', array('id' => $entity->getId())));

        }

        else throw $this->createNotFoundException('All runs must have a winner !');
    }
}
