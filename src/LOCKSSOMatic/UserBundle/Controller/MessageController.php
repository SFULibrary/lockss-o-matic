<?php

namespace LOCKSSOMatic\UserBundle\Controller;

use LOCKSSOMatic\UserBundle\Entity\Message;
use LOCKSSOMatic\UserBundle\Form\MessageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Message controller.
 *
 * @Route("/message")
 */
class MessageController extends Controller
{

    /**
     * Lists all Message entities.
     *
     * @Route("/", name="message")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LOCKSSOMaticUserBundle:Message')->findBy(array(
            'user' => $user
        ));
        foreach($entities as $entity) {
            $entity->setSeen(true);
        }
        $em->flush();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * @Route("/clear", name="message_clear")
     */
    public function clearMessagesAction() {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LOCKSSOMaticUserBundle:Message')->findBy(array(
            'user' => $user
        ));
        foreach($entities as $entity) {
            if($entity->getSeen()) {
                $em->remove($entity);
            }
        }
        $em->flush();
        $this->addFlash("success", "Messages cleared.");
        return $this->redirect($this->generateUrl('message'));
    }
    
}
