<?php

namespace Core\CharacterSheetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Core\CharacterSheetBundle\Entity\CharacterSheet;
use Core\CharacterSheetBundle\Form\Type\CharacterSheetType;

class CharacterSheetController extends Controller
{
    public function indexAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $characterSheets = $entityManager->getRepository('CoreCharacterSheetBundle:CharacterSheet')->findBy(array('user' => $user), array('createdAt' => 'DESC'));

        return $this->render('CoreCharacterSheetBundle::index.html.twig', array(
            'characterSheets' => $characterSheets,
        ));
    }

    public function showAction($characterSheetsId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $characterSheet = $entityManager->getRepository('CoreCharacterSheetBundle:CharacterSheet')->find($characterSheetsId);

        if (!$characterSheet) {
            throw $this->createNotFoundException('Character sheet Not Found');
        }

        return $this->render('CoreCharacterSheetBundle::show.html.twig', array(
            'characterSheet' => $characterSheet,
        ));
    }

    public function addAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $form = $this->createForm(new CharacterSheetType(), $characterSheet = new CharacterSheet());

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $characterSheet->setUser($user);
                $entityManager->persist($characterSheet);
                $entityManager->flush();

                return $this->redirect($this->generateUrl('core_characterSheet_homepage'));
            }
        }

        return $this->render('CoreCharacterSheetBundle::create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function updateAction(Request $request, $characterSheetId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $characterSheet = $entityManager->getRepository('CoreCharacterSheetBundle:CharacterSheet')->find($characterSheetId);
        $form = $this->createForm(new CharacterSheetType(), $characterSheet);

        if (!$characterSheet) {
            throw $this->createNotFoundException('Character sheet Not Found');
        }

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $characterSheet->setUser($user);
                $characterSheet->setUpdatedAt(new \DateTime());
                $entityManager->persist($characterSheet);
                $entityManager->flush();

                return $this->redirect($this->generateUrl('core_characterSheet_homepage'));
            }
        }

        return $this->render('CoreCharacterSheetBundle::update.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function deleteAction($characterSheetId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $characterSheet = $entityManager->getRepository('CoreCharacterSheetBundle:CharacterSheet')->find($characterSheetId);

        if (!$characterSheet) {
            throw $this->createNotFoundException('Character sheet Not Found');
        }

        $entityManager->remove($characterSheet);
        $entityManager->flush();

        return $this->redirect($this->generateUrl('core_characterSheet_homepage'));
    }
}
