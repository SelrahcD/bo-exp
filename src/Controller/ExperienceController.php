<?php

namespace App\Controller;

use App\Entity\Experience;
use App\Form\ExperienceType;
use App\Repository\ExperienceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Registry;

/**
 * @Route("/experience")
 */
class ExperienceController extends AbstractController
{
    /**
     * @var Registry
     */
    private $workflows;

    public function __construct(Registry $workflows)
    {
        $this->workflows = $workflows;
    }

    /**
     * @Route("/", name="experience_index", methods={"GET"})
     */
    public function index(ExperienceRepository $experienceRepository): Response
    {
        return $this->render('experience/index.html.twig', [
            'experiences' => $experienceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="experience_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $experience = new Experience();
        $form = $this->createForm(ExperienceType::class, $experience);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($experience);
            $entityManager->flush();

            return $this->redirectToRoute('experience_index');
        }

        return $this->render('experience/new.html.twig', [
            'experience' => $experience,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="experience_show", methods={"GET"})
     */
    public function show(Experience $experience): Response
    {
        return $this->render('experience/show.html.twig', [
            'experience' => $experience,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="experience_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Experience $experience): Response
    {
        $form = $this->createForm(ExperienceType::class, $experience);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('experience_index', [
                'id' => $experience->getId(),
            ]);
        }

        return $this->render('experience/edit.html.twig', [
            'experience' => $experience,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="experience_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Experience $experience): Response
    {
        if ($this->isCsrfTokenValid('delete'.$experience->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($experience);
            $entityManager->flush();
        }

        return $this->redirectToRoute('experience_index');
    }

    /**
     * @Route("/{id}/ask_feedback", name="experience_ask_feedback", methods={"GET"})
     */
    public function askFeedback(Experience $experience): Response
    {
        $workflow = $this->workflows->get($experience);

        if ($workflow->can($experience, 'feedback')) {
            $workflow->apply($experience, 'feedback');
            $this->getDoctrine()->getManager()->flush();
        }


        return $this->redirectToRoute('experience_index', [
            'id' => $experience->getId(),
        ]);
    }

    /**
     * @Route("/{id}/approve", name="experience_approve", methods={"GET"})
     */
    public function approve(Experience $experience): Response
    {
        $workflow = $this->workflows->get($experience);

        if ($workflow->can($experience, 'approve')) {
            $workflow->apply($experience, 'approve');
            $this->getDoctrine()->getManager()->flush();
        }


        return $this->redirectToRoute('experience_index', [
            'id' => $experience->getId(),
        ]);
    }

    /**
     * @Route("/{id}/publish", name="experience_publish", methods={"GET"})
     */
    public function publish(Experience $experience): Response
    {
        $workflow = $this->workflows->get($experience);

        if ($workflow->can($experience, 'publish')) {
            $workflow->apply($experience, 'publish');
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->redirectToRoute('experience_index', [
            'id' => $experience->getId(),
        ]);
    }
}
