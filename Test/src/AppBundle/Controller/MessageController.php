<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends Controller {

    /**
     * @Route("api/v1/getAllUsers/", name="get_all_users", methods="POST")
     */
    public function getAllUsersAction() {
        try {
            $em = $this->getDoctrine()->getManager();
            $users = $em->getRepository('AppBundle:User')->findAll();

            return new Response($users);
        } catch (Exception $exception) {

            throw new Exception('Error occured');
        }
    }

    /**
     * @Route("api/v1/getOnlineUsers/", name="get_online_users", methods="POST")
     */
    public function getOnlineUsersAction() {
        try {
            $em = $this->getDoctrine()->getManager()->getRepository('AppBundle:User');

            $delay = new \DateTime('2 minutes ago');
            $criteria = new \Doctrine\Common\Collections\Criteria();
            $criteria->where($criteria->expr()->gt('lastActivityAt', $delay));

            $users = $em->matching($criteria);

            return new Response($users);
        } catch (Exception $exception) {

            throw new Exception('Error occured');
        }
    }

}
