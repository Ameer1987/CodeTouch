<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\Message;
use AppBundle\Form\MessageType;

class FrontendController extends Controller {

    /**
     * @Route("/", name="homepage", methods="GET")
     */
    public function indexAction() {
        $response = $this->forward('AppBundle:Backend:getAllUsers');

        return $this->render('frontend/index.html.twig', array(
                    'users' => json_decode($response->getContent(), true),
        ));
    }

}
