<?php

namespace PB\PlayableBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use PB\PlayableBundle\Entity\Playground;
use Symfony\Component\HttpFoundation\Response;

/**
 * Playground controller.
 *
 */
class PlaygroundController extends Controller
{
    const CONTENT_TYPE_JSON = 'json';
    const STATUS_NOT_ACCEPTABLE = 406;
    const STATUS_NO_CONTENT = 204;

    public function addAction(Request $request)
    {
        $playground = new Playground();
        $form = $this->createFormBuilder($playground)
            ->add('name', 'text')
            ->add('latitude', 'number')
            ->add('longitude', 'number')
            ->add('save', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $data = $form->getData();
            $playground->setName($data->getName());
            $playground->setLongitude($data->getLongitude());
            $playground->setLatitude($data->getLatitude());
            $em->persist($playground);
            $em->flush();
            return $this->redirect($this->generateUrl('playground'));
        }

        return $this->render(
            'PBPlayableBundle:Playground:add.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    public function listAction()
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

    public function updateAction(Request $request, $id)
    {

        if ($request->getContentType() != self::CONTENT_TYPE_JSON) {
            return new Response('Illegal content type', self::STATUS_NOT_ACCEPTABLE);
        }

        $em = $this->getDoctrine()->getManager();

        /** @var Playground $playground */
        $playground = $em->getRepository('PBPlayableBundle:Playground')->find($id);

        if (!$playground) {
            throw $this->createNotFoundException('Unable to find Playground entity.');
        }

        $data = json_decode($request->getContent(), true);

        $playground->setName($data['name']);
        $playground->setLatitude($data['latitude']);
        $playground->setLongitude($data['longitude']);

        $em->persist($playground);
        $em->flush();

        return new Response('successfully updated playground (' . $id . ')');
    }

    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Playground $playground */
        $playground = $em->getRepository('PBPlayableBundle:Playground')->find($id);

        $data['name'] = $playground->getName();
        $data['latitude'] = $playground->getLatitude();
        $data['longitude'] = $playground->getLongitude();

        return new Response(json_encode($data));
    }

    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Playground $playground */
        $playground = $em->getRepository('PBPlayableBundle:Playground')->find($id);

        if (!$playground) {
            return $this->createNotFoundException();
        }

        $em->remove($playground);
        $em->flush();

        return new Response('', self::STATUS_NO_CONTENT);
    }
}
