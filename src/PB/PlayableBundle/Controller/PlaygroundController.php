<?php

namespace PB\PlayableBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use PB\PlayableBundle\Entity\Playground;
use PB\PlayableBundle\Form\PlaygroundType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Playground controller.
 *
 */
class PlaygroundController extends Controller
{
    const CONTENT_TYPE_JSON = 'json';
    const STATUS_NOT_ACCEPTABLE = 406;

    /**
     * Lists all Playground entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Playground[] $playgrounds */
        $playgrounds = $em->getRepository('PBPlayableBundle:Playground')->findAll();

        $responseData = [];

        foreach ($playgrounds as $playground) {
            $pg = [];
            $pg['name'] = $playground->getName();
            $pg['latitude'] = $playground->getLatitude();
            $pg['longitude'] = $playground->getLongitude();
            $responseData[] = $pg;
        }

        return new Response(json_encode($responseData));
    }

    public function createAction(Request $request)
    {
        if ($request->getContentType() != self::CONTENT_TYPE_JSON) {
            return new Response('Illegal content type', self::STATUS_NOT_ACCEPTABLE);
        }

        $data = json_decode($request->getContent(), true);

        $playground = new Playground();
        $playground->setName($data['name']);
        $playground->setLatitude($data['latitude']);
        $playground->setLongitude($data['longitude']);

        $em = $this->getDoctrine()->getManager();
        $em->persist($playground);
        $em->flush();

        return new Response();
    }

    /**
    * Creates a form to create a Playground entity.
    *
    * @param Playground $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Playground $entity)
    {
        $form = $this->createForm(new PlaygroundType(), $entity, array(
            'action' => $this->generateUrl('playground_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Playground entity.
     *
     */
    public function newAction()
    {
        $entity = new Playground();
        $form   = $this->createCreateForm($entity);

        return $this->render('PBPlayableBundle:Playground:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Playground entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PBPlayableBundle:Playground')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Playground entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PBPlayableBundle:Playground:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing Playground entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PBPlayableBundle:Playground')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Playground entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PBPlayableBundle:Playground:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Playground entity.
    *
    * @param Playground $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Playground $entity)
    {
        $form = $this->createForm(new PlaygroundType(), $entity, array(
            'action' => $this->generateUrl('playground_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Playground entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PBPlayableBundle:Playground')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Playground entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('playground_edit', array('id' => $id)));
        }

        return $this->render('PBPlayableBundle:Playground:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Playground entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PBPlayableBundle:Playground')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Playground entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('playground'));
    }

    /**
     * Creates a form to delete a Playground entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('playground_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
