<?php

namespace App\Controller;

use App\Entity\Experience;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Registry;


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
     * @Route("experience/{id}/ask_feedback", name="experience_ask_feedback", methods={"POST"})
     */
    public function askFeedback(Experience $experience, Request $request): Response
    {
        if ($this->isCsrfTokenValid('ask_feedback'.$experience->getId(), $request->request->get('_token'))) {

            $workflow = $this->workflows->get($experience);

            if ($workflow->can($experience, 'feedback')) {
                $workflow->apply($experience, 'feedback');
                $this->getDoctrine()->getManager()->flush();
            }
        }

        return $this->redirectToRoute('easyadmin', array(
            'action' => 'edit',
            'id' => $experience->getId(),
            'entity' => 'Experience',
        ));
    }

    /**
     * @Route("experience/{id}/approve", name="experience_approve", methods={"POST"})
     */
    public function approve(Experience $experience, Request $request): Response
    {
        if ($this->isCsrfTokenValid('approve'.$experience->getId(), $request->request->get('_token'))) {

            $workflow = $this->workflows->get($experience);

            if ($workflow->can($experience, 'approve')) {
                $workflow->apply($experience, 'approve');
                $this->getDoctrine()->getManager()->flush();
            }
        }

        return $this->redirectToRoute('easyadmin', array(
            'action' => 'edit',
            'id' => $experience->getId(),
            'entity' => 'Experience',
        ));
    }

    /**
     * @Route("experience/{id}/publish", name="experience_publish", methods={"POST"})
     */
    public function publish(Experience $experience, Request $request): Response
    {
        if ($this->isCsrfTokenValid('publish'.$experience->getId(), $request->request->get('_token'))) {
            $workflow = $this->workflows->get($experience);

            if ($workflow->can($experience, 'publish')) {
                $workflow->apply($experience, 'publish');
                $this->getDoctrine()->getManager()->flush();
            }
        }

        return $this->redirectToRoute('easyadmin', array(
            'action' => 'edit',
            'id' => $experience->getId(),
            'entity' => 'Experience',
        ));

    }
}
