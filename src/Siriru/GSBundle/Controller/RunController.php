<?php

namespace Siriru\GSBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Siriru\GSBundle\Entity\Run;
use Siriru\GSBundle\Form\RunType;

/**
 * Run controller.
 *
 * @Route("/run")
 */
class RunController extends Controller
{

    /**
     * Lists all Run entities.
     *
     * @Route("/", name="run")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SiriruGSBundle:Run')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a Run entity.
     *
     * @Route("/{id}", name="run_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SiriruGSBundle:Run')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Run entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Run entity.
     *
     * @Route("/{id}/edit", name="run_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SiriruGSBundle:Run')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Run entity.');
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
    * Creates a form to edit a Run entity.
    *
    * @param Run $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Run $entity)
    {
        $form = $this->createForm(new RunType(), $entity, array(
            'action' => $this->generateUrl('run_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Run entity.
     *
     * @Route("/{id}", name="run_update")
     * @Method("PUT")
     * @Template("SiriruGSBundle:Run:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SiriruGSBundle:Run')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Run entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('goldsprint_show', array('id' => $entity->getType()->getGoldsprint()->getId())));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Run entity.
     *
     * @Route("/{id}", name="run_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SiriruGSBundle:Run')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Run entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('run'));
    }

    /**
     * Creates a form to delete a Run entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('run_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
