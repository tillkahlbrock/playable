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
        return new Response('Not implemented yet', 404);
    }

    public function deleteAction(Request $request, $id)
    {
        return new Response('Not implemented yet', 404);
    }
}
