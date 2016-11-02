<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use AppBundle\Entity\Message;
use AppBundle\Form\MessageType;

class BackendController extends Controller {

    /**
     * @Route("api/v1/getAllUsers/", name="get_all_users", methods="GET")
     */
    public function getAllUsersAction() {
        try {
            $em = $this->getDoctrine()->getManager()->getRepository('AppBundle:User');
            $user_id = $this->getUser()->getId();
            $query = $em->createQueryBuilder('u')
                    ->where("u.id != $user_id")
                    ->getQuery();
            $users = $query->getResult();

            $encoders = array(new XmlEncoder(), new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());
            $serializer = new Serializer($normalizers, $encoders);
            $usersJson = $serializer->serialize($users, 'json');

            return new Response($usersJson, 200, array(
                'content-type' => 'application/json'
            ));
        } catch (Exception $exception) {

            throw new Exception('Error occured');
        }
    }

    /**
     * @Route("api/v1/postMessage/", name="post_message", methods="POST")
     */
    public function postMessageAction(Request $request) {
        try {
            $em = $this->getDoctrine()->getManager();

            $message = new Message();
            $message->setMessage($request->request->get('message'));

            $receiver = $em->getRepository('AppBundle:User')->findOneById($request->request->get('receiver_id'));
            $message->setReceiver($receiver);

            $sender = $em->getRepository('AppBundle:User')->findOneById($this->getUser()->getId());
            $message->setSender($sender);

            $message->setSendDate(new \DateTime());

            $em->persist($message);

            $em->flush();

            return new Response('sent', 201);
        } catch (Exception $exception) {

            throw new Exception('Error occured');
        }
    }

    /**
     * @Route("api/v1/getUserMessages/{friend_id}", name="get_user_messages", methods="GET")
     */
    public function getUserMessagesAction($friend_id) {
        try {
            $em = $this->getDoctrine()->getManager()->getRepository('AppBundle:Message');

            $user_id = $this->getUser()->getId();
            $query = $em->createQueryBuilder('m')
                    ->where("m.sender IN ($user_id, $friend_id)")
                    ->andWhere("m.receiver IN ($user_id, $friend_id)")
                    ->orderBy('m.sendDate', 'ASC')
                    ->getQuery();

            $messages = $query->getResult();

            $encoders = array(new XmlEncoder(), new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());
            $serializer = new Serializer($normalizers, $encoders);
            $messagesJson = $serializer->serialize($messages, 'json');

            return new Response($messagesJson, 200, array(
                'content-type' => 'application/json'
            ));
        } catch (Exception $exception) {

            throw new Exception('Error occured');
        }
    }

}
