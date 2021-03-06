<?php

namespace Core\CharacterSheetBundle\Controller;

use Core\CharacterSheetBundle\Entity\CharacterSheet;
use Core\CharacterSheetBundle\Form\Type\CharacterSheetType;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Request;

class CharacterSheetsController extends FOSRestController
{
    /**
    * @return array
    * @View()
    */
    public function getSheetsAction($userId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository('CoreUserBundle:User')->find($userId);
        $characterSheets = $entityManager->getRepository('CoreCharacterSheetBundle:CharacterSheet')->findBy(array('user' => $user), array('createdAt' => 'DESC'));

        return array(
            'character_sheets' => $characterSheets,
        );
    }

    /**
    * @return array
    * @View()
    * @Get("/sheet/{sheetId}")
    */
    public function getSheetAction($sheetId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $characterSheet = $entityManager->getRepository('CoreCharacterSheetBundle:CharacterSheet')->find($sheetId);

        if (!$characterSheet) {
            return array(
                'code' => 404,
                'data' => 'Charactersheet not found',
            );
        }

        return array(
            'character_sheet' => $CharacterSheet,
        );
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

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $characterSheet->setSheetFile($request->files->get('sheetFile'));
                $characterSheet->setImageFile($request->files->get('imageFile'));
                $characterSheet->setFullName($request->get('fullName'));
                $characterSheet->setDetails($request->get('details'));
                $characterSheet->setBackground($request->get('background'));
                $characterSheet->setUser($user);
                $entityManager->persist($characterSheet);
                $entityManager->flush();

                return array(
                    'code' => 201,
                    'data' => $characterSheet,
                );
            }
        }

        return array(
            'code' => 400,
            $form,
        );
    }

    /**
    * @return array
    * @View()
    */
    public function putSheetsAction(Request $request, $SheetId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $characterSheet = $entityManager->getRepository('CoreCharacterSheetBundle:CharacterSheet')->find($SheetId);
        $form = $this->createForm(new CharacterSheetType(), $characterSheet);

        if (!$characterSheet) {
            return array(
                'code' => 404,
                'data' => 'Charactersheet not found',
            );
        }
        if ($request->isMethod('PUT')) {
            $form->bind($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $characterSheet->setUser($user);
                $characterSheet->setUpdatedAt(new \DateTime());
                $characterSheet->setSheetFile($request->files->get('sheetFile'));
                $characterSheet->setImageFile($request->files->get('imageFile'));
                $characterSheet->setFullName($request->get('fullName'));
                $characterSheet->setDetails($request->get('details'));
                $characterSheet->setBackground($request->get('background'));
                $entityManager->persist($characterSheet);
                $entityManager->flush();

                return array(
                    'code' => 200,
                    'data' => $CharacterSheet,
                );
            }
        }

        return array(
            'code' => 400,
            $form,
        );
    }

    /**
    * @return array
    * @View()
    */
    public function deleteSheetsAction($SheetId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $characterSheet = $entityManager->getRepository('CoreCharacterSheetBundle:CharacterSheet')->find($SheetId);

        if (!$characterSheet) {
            return array(
                'code' => 404,
                'data' => 'Charactersheet not found',
            );
        }

        $entityManager->remove($characterSheet);
        $entityManager->flush();

        return array(
            'code' => 200,
            'data' => 'Delete done',
        );
    }
}
