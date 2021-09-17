<?php

namespace App\Controller;

use App\Entity\CaseEntity;
use App\Entity\Note;
use App\Entity\User;
use App\Form\NoteType;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/case/{id}/notes")
 */
class NoteController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("", name="note_index", methods={"GET", "POST"})
     */
    public function index(CaseEntity $case, PaginatorInterface $paginator, NoteRepository $repository, Request $request): Response
    {
        $noteQuery = $repository->getNotesQueryByCase($case);

        $pagination = $paginator->paginate(
            $noteQuery, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );
        $pagination->setCustomParameters(['align' => 'center']);

        $note = new Note();

        $form = $this->createForm(NoteType::class, $note);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $note->setCaseEntity($case);

            /** @var User $user */
            $user = $this->getUser();

            $note->setCreatedBy($user);
            $this->entityManager->persist($note);
            $this->entityManager->flush();

            return $this->redirectToRoute('note_index', ['id' => $case->getId()]);
        }

        return $this->render('notes/index.html.twig', [
            'note_form' => $form->createView(),
            'pagination' => $pagination,
            'case' => $case,
        ]);
    }

    /**
     * @Route("/{note_id}/edit", name="note_edit", methods={"GET", "POST"})
     * @Entity("note", expr="repository.find(note_id)")
     * @Entity("case", expr="repository.find(id)")
     */
    public function edit(CaseEntity $case, Note $note, Request $request): Response
    {
        $form = $this->createForm(NoteType::class, $note);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $note->setCaseEntity($case);
            $this->entityManager->flush();

            return $this->redirectToRoute('note_index', [
                'id' => $case->getId(),
                'noteShown' => $note->getId()->__toString(),
            ]);
        }

        return $this->render('notes/edit.html.twig', [
            'note_form' => $form->createView(),
            'case' => $case,
            'note' => $note,
        ]);
    }

    /**
     * @Route("/{note_id}/delete", name="note_delete", methods={"DELETE"})
     * @Entity("note", expr="repository.find(note_id)")
     * @Entity("case", expr="repository.find(id)")
     */
    public function delete(CaseEntity $case, Note $note, Request $request): Response
    {
        // Check that CSRF token is valid
        if ($this->isCsrfTokenValid('delete'.$note->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($note);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('note_index', ['id' => $case->getId()]);
    }
}
