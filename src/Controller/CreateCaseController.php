<?php

namespace App\Controller;

use App\Entity\FenceReviewCase;
use App\Entity\RentBoardCase;
use App\Entity\ResidentComplaintBoardCase;
use App\Repository\BoardRepository;
use App\Repository\MunicipalityRepository;
use App\Service\CaseManager;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Routing\Annotation\Route;

class CreateCaseController extends AbstractController
{
    /**
     * @Route("/municipality/{municipalityName}/board/{boardName}/case/create", name="rescase")
     */
    public function createCase(BoardRepository $boardRepository, CaseManager $caseManager, LockFactory $lockFactory, MunicipalityRepository $municipalityRepository, Request $request, string $municipalityName, string $boardName): Response
    {
        // Check that municipality exists
        $municipality = $municipalityRepository->findOneBy(['name' => $municipalityName]);

        if (null === $municipality) {
            throw new Exception('Municipality not found.');
        }

        // Check that board exists
        $board = $boardRepository->findOneBy(['name' => $boardName]);

        if (null === $board) {
            throw new Exception('Board not found.');
        }

        // Match on which case object to create
        $caseType = $board->getCaseFormType();

        $case = null;

        switch ($caseType) {
            case 'ResidentComplaintBoardCaseType':
                $case = new ResidentComplaintBoardCase();
                break;
            case 'RentBoardCaseType':
                $case = new RentBoardCase();
                break;
            case 'FenceReviewCaseType':
                $case = new FenceReviewCase();
                break;
        }

        if (null === $case) {
            throw new Exception('Case object was not created.');
        }

        $case->setMunicipality($municipality);
        $case->setBoard($board);

        $case->setCaseType($caseType);

        $form = $this->createForm('App\\Form\\'.$caseType, $case, ['board' => $board]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $case = $form->getData();

            $lock = $lockFactory->createLock('case-number-generation');

            $lock->acquire(true);

            // case number generation
            $caseNumber = $caseManager->generateCaseNumber($municipality);

            $case->setCaseNumber($caseNumber);

            $em = $this->getDoctrine()->getManager();
            $em->persist($case);
            $em->flush();

            $lock->release();

            return $this->redirectToRoute('default');
        }

        return $this->render('case/createCase.html.twig', [
            'case_form' => $form->createView(),
        ]);
    }
}
