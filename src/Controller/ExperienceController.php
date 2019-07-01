<?php

namespace App\Controller;

use App\Entity\Experience;
use App\Form\ExperienceType;
use App\Repository\ExperienceRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Registry;

/**
 * @Route("/experience")
 */
class ExperienceController extends EasyAdminController
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
