<?php

namespace App\Controller;

use App\Entity\CaseEntity;
use App\Entity\Reminder;
use App\Entity\User;
use App\Form\ReminderType;
use App\Repository\ReminderRepository;
use App\Service\ReminderStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ReminderController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var Security
     */
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    /**
     * @Route("/reminder", name="reminder_index")
     */
    public function index(ReminderRepository $reminderRepository): Response
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $reminders = $reminderRepository->findBy([
            'createdBy' => $user->getId()->toBinary(),
        ]);

        return $this->render('reminder/index.html.twig', [
            'reminders' => $reminders,
        ]);
    }

    /**
     * @Route("/reminder/create/{id}", name="reminder_create", methods={"GET", "POST"})
     */
    public function newReminder(CaseEntity $case, Request $request): Response
    {
        $reminder = new Reminder();

        $reminderForm = $this->createForm(ReminderType::class, $reminder);

        $reminderForm->handleRequest($request);

        if ($reminderForm->isSubmitted() && $reminderForm->isValid()) {
            /** @var Reminder $reminder */
            $reminder = $reminderForm->getData();
            $reminder->setCaseEntity($case);
            // TODO: What if date is current date?
            $reminder->setStatus(ReminderStatus::Pending);

            /** @var User $user */
            $user = $this->security->getUser();
            $reminder->setCreatedBy($user);

            $this->entityManager->persist($reminder);
            $this->entityManager->flush();

            return $this->redirectToRoute('case_index');
        }

        return $this->render('reminder/_new.html.twig', [
            'reminder_form' => $reminderForm->createView(),
            'case' => $case,
        ]);
    }

    /**
     * @Route("/reminder/complete/{id}", name="reminder_complete")
     */
    public function completeReminder(Reminder $reminder): Response
    {
        $this->entityManager->remove($reminder);
        $this->entityManager->flush();

        return $this->redirectToRoute('reminder_index');
    }

    /**
     * @Route("/reminder/edit/{id}", name="reminder_edit", methods={"GET", "POST"})
     */
    public function editReminder(Reminder $reminder, Request $request): Response
    {
        $reminderForm = $this->createForm(ReminderType::class, $reminder);

        $reminderForm->handleRequest($request);

        if ($reminderForm->isSubmitted() && $reminderForm->isValid()) {
            /** @var Reminder $reminder */
            $reminder = $reminderForm->getData();
            // TODO: What if it is still same date and just content edit
            $reminder->setStatus(ReminderStatus::Pending);

            $this->entityManager->flush();

            return $this->redirectToRoute('reminder_index');
        }

        return $this->render('reminder/_edit.html.twig', [
            'reminder_form' => $reminderForm->createView(),
            'reminder' => $reminder,
        ]);
    }
}