<?php

namespace App\Controller;

use App\Entity\Pony;
use App\Form\PonyType;
use App\Repository\PonyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/pony")
 */
class PonyController extends AbstractController
{
    /**
     * @Route("/", name="pony_index", methods={"GET"})
     */
    public function index(PonyRepository $ponyRepository): Response
    {
        return $this->render('pony/index.html.twig', [
            'ponies' => $ponyRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="pony_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $pony = new Pony();
        $form = $this->createForm(PonyType::class, $pony);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pony);
            $entityManager->flush();

            return $this->redirectToRoute('pony_index');
        }

        return $this->render('pony/new.html.twig', [
            'pony' => $pony,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="pony_show", methods={"GET"})
     */
    public function show(Pony $pony): Response
    {
        return $this->render('pony/show.html.twig', [
            'pony' => $pony,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="pony_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Pony $pony): Response
    {
        $form = $this->createForm(PonyType::class, $pony);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('pony_index', [
                'id' => $pony->getId(),
            ]);
        }

        return $this->render('pony/edit.html.twig', [
            'pony' => $pony,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="pony_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Pony $pony): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pony->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($pony);
            $entityManager->flush();
        }

        return $this->redirectToRoute('pony_index');
    }
}
