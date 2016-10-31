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
            $query = $em->createQueryBuilder('u')
                    ->getQuery();
            $users = $query->getResult();

            $encoders = array(new XmlEncoder(), new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());
            $serializer = new Serializer($normalizers, $encoders);
            $usersJson = $serializer->serialize($users, 'json');

            return new Response($usersJson, 201, array(
                'content-type' => 'application/json'
            ));
        } catch (Exception $exception) {

            throw new Exception('Error occured');
        }
    }

    /**
     * @Route("api/v1/postMessage/", name="post_message", methods="POST")
     */
    public function postMessageAction() {
        try {
            $data = json_decode($this->getRequest()->getContent(), true);

            $message = new Message();
            $message->setMessage($data['message']);
            $message->setReceiverId($data['receiver_id']);
            $message->setSenderId($data['sender_id']);
            $message->setSendDate(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($message);

            $em->flush();

            return new Response('sent', 201, array(
                'content-type' => 'application/json'
            ));
        } catch (Exception $exception) {

            throw new Exception('Error occured');
        }
    }

    /**
     * @Route("api/v1/getUserMessages/{user_id}/{friend_id}", name="get_user_messages", methods="GET")
     */
    public function getUserMessagesAction($user_id, $friend_id) {
        try {
            $em = $this->getDoctrine()->getManager()->getRepository('AppBundle:Message');

            $user_ids = "$user_id, $friend_id";
            $query = $em->createQueryBuilder('m')
                    ->where("m.sender IN ($user_id, $friend_id)")
                    ->andWhere("m.receiver IN ($user_id, $friend_id)")
                    ->orderBy('m.sendDate', 'DESC')
                    ->getQuery();

            $messages = $query->getResult();

            $encoders = array(new XmlEncoder(), new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());
            $serializer = new Serializer($normalizers, $encoders);
            $messagesJson = $serializer->serialize($messages, 'json');

            return new Response($messagesJson, 201, array(
                'content-type' => 'application/json'
            ));
        } catch (Exception $exception) {

            throw new Exception('Error occured');
        }
    }

}
