<?php

namespace LOCKSSOMatic\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class DefaultController extends Controller
{
    /**
     * Render a static index page.
     *
     * @return Response the response
     */
    public function indexAction()
    {
        return $this->render('LOCKSSOMaticCoreBundle:Default:index.html.twig');
    }
    
    /**
     * Contoller for creating manually uploaded LOM deposits.
     * 
     * LOCKSS-O-Matic offers two ways to deposit content URLs:
     * via SWORD and manually. Manual deposits are created by
     * uploading a CSV file listing content URLs. The content
     * provider for these deposits needs to be either selected
     * from a list by the user uploading the file or predetermined
     * in some way (e.g., by user permission), and need to exist
     * at the time the depoist is created.
     * 
     * @param object $request
     * 
     * @todo Create a custom validator for Content URLs that
     * verifies the URL returns a 200 to LOM. On failure of a single
     * URL, reject the entire deposit. This validator should also
     * apply to deposits created via SWORD.
     */
    public function uploadDepositAction(Request $request)
    {
        $defaultData = array('message' => 'Type your message here');
        $form = $this->createFormBuilder($defaultData)
            ->add('attachment', 'file', array('label' => "Upload a CSV file containing URLs"))
            ->add('create deposit', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            // No data yet, but we could put the content provider ID in a hidden field.
            $data = $form->getData();
            $logger = $this->get('logger');
            if ($form['attachment']->getData()->move('/tmp', 'uploadtestsaved.txt')) {
                $this->doSomething();
            }
        }

        return $this->render('LOCKSSOMaticCoreBundle:Default:uploadDeposit.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    /**
     * Placeholder / test function.
     */
    public function doSomething()
    {
        $logger = $this->get('logger');
        $lines = file('/tmp/uploadtestsaved.txt');
        foreach ($lines as $line) {
            $logger->info(trim($line));
        }
    }
}
