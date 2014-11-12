<?php

namespace Core\CharacterSheetBundle\Controller;

use Core\CharacterSheetBundle\Entity\CharacterSheet;
use Core\CharacterSheetBundle\Form\Type\CharacterSheetType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CharacterSheetsController extends FOSRestController
{
    /**
    * @return array
    * @View()
    */
    public function getSheetsAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $characterSheets = $entityManager->getRepository('CoreCharacterSheetBundle:CharacterSheet')->findBy(array('user' => $user), array('createdAt' => 'DESC'));

        return array('characterSheets' => $characterSheets);
    }

    /**
    * @return array
    * @View()
    */
    public function getSheetAction($characterSheetId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $characterSheet = $entityManager->getRepository('CoreCharacterSheetBundle:CharacterSheet')->find($characterSheetId);

        if (!$characterSheet) {
            throw $this->createNotFoundException('Character sheet Not Found');
        }

        return array('CharacterSheet' => $CharacterSheet);
    }

    /**
    * @return array
    * @View()
    */
    public function postSheetsAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $form = $this->createForm(new CharacterSheetType(), $characterSheet = new CharacterSheet());

        $jsonPost = json_decode($request->getContent(), true);
        if ($request->isMethod('POST')) {
            $form->bind($jsonPost);
            if ($form->isValid()) {
                $characterSheet->setUser($user);
                $entityManager->persist($characterSheet);
                $entityManager->flush();
                return array('code' => 200, 'text' => 'POST OK');
            }
        }
       return array('code' => 400, $form);
    }

    /**
    * @return array
    * @View()
    */
    public function putSheetsAction(Request $request, $characterSheetId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $characterSheet = $entityManager->getRepository('CoreCharacterSheetBundle:CharacterSheet')->find($characterSheetId);
        $form = $this->createForm(new CharacterSheetType(), $characterSheet);

        if (!$characterSheet) {
            throw $this->createNotFoundException('Character sheet Not Found');
        }

        $jsonPost = json_decode($request->getContent(), true);
        if ($request->isMethod('PUT')) {
            $form->bind($jsonPost);
            if ($form->isValid()) {
                $characterSheet->setUser($user);
                $characterSheet->setUpdatedAt(new \DateTime());
                $entityManager->persist($characterSheet);
                $entityManager->flush();
                return array('code' => 200, 'text' => 'PUT OK');
            }
        }
       return array('code' => 400, $form);
    }

    /**
    * @return array
    * @View()
    */
    public function deleteSheetsAction($characterSheetId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $characterSheet = $entityManager->getRepository('CoreCharacterSheetBundle:CharacterSheet')->find($characterSheetId);

        if (!$characterSheet) {
            throw $this->createNotFoundException('Character sheet Not Found');
        }

        $entityManager->remove($characterSheet);
        $entityManager->flush();

        return array('code' => 200, 'text' => 'DELETE OK');
    }
}
