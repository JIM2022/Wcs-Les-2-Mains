<?php

namespace CoreBundle\Controller;

use CoreBundle\Entity\Contact;
use CoreBundle\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * Render homage
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('CoreBundle:pages:index.html.twig');
    }

    /**
     * Render contact page
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function contactAction(Request $request)
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);

        // Refill the fields in case the form is not valid.
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Send mail
            $this->sendEmail($contact);
            return $this->redirectToRoute('core_contact_redirection');
        }

        return $this->render('@Core/pages/contact/contact.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * Send email from contact form
     * @param Contact $contact
     */
    private function sendEmail(Contact $contact){

        $message = \Swift_Message::newInstance()
            ->setSubject("Vous avez une nouvelle demande")
            ->setFrom(array($contact->getEmail() => $this->getParameter('mailer_sender_name')))
            ->setTo($this->getParameter('mailer_user'))
            ->setContentType("text/html")
            ->setBody($this->renderView(':Email:contactMail.html.twig', array(
                'contact' => $contact
            )));

        // Send the message
        $this->get('mailer')->send($message);
    }

    /**
     * @return Response
     */
    public function redirectAction()
    {
        return $this->render('@Core/pages/contact/redirectionSendContact.html.twig');
    }
}



