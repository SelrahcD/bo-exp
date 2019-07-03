<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\ExperienceVersion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Registry;

/**
 * @Route("/experienceversion")
 */
final class ExperienceVersionController extends AbstractController
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
     * @Route("/{id}/request_feedback", name="experience_version_request_feedback", methods={"PUT"})
     */
    public function requestFeedback(ExperienceVersion $experienceVersion, Request $request): Response
    {
        if ($this->isCsrfTokenValid('request_feedback'.$experienceVersion->getId(), $request->request->get('_token'))) {
            $workflow = $this->workflows->get($experienceVersion);
            if ($workflow->can($experienceVersion, 'request_feedback')) {
                $workflow->apply($experienceVersion, 'request_feedback');
                $this->getDoctrine()->getManager()->flush();
            }
        }
        return $this->redirectToRoute('easyadmin', array(
            'action' => 'edit',
            'id' => $experienceVersion->getId(),
            'entity' => 'ExperienceVersion',
        ));
    }

    /**
     * @Route("/{id}/approve", name="experience_version_approve", methods={"PUT"})
     */
    public function approve(ExperienceVersion $experienceVersion, Request $request): Response
    {
        if ($this->isCsrfTokenValid('approve'.$experienceVersion->getId(), $request->request->get('_token'))) {
            $workflow = $this->workflows->get($experienceVersion);
            if ($workflow->can($experienceVersion, 'approve')) {
                $workflow->apply($experienceVersion, 'approve');
                $this->getDoctrine()->getManager()->flush();
            }
        }
        return $this->redirectToRoute('easyadmin', array(
            'action' => 'show',
            'id' => $experienceVersion->getId(),
            'entity' => 'ExperienceVersion',
        ));
    }

    /**
     * @Route("/{id}/reject", name="experience_version_reject", methods={"PUT"})
     */
    public function reject(ExperienceVersion $experienceVersion, Request $request): Response
    {
        if ($this->isCsrfTokenValid('reject'.$experienceVersion->getId(), $request->request->get('_token'))) {
            $workflow = $this->workflows->get($experienceVersion);
            if ($workflow->can($experienceVersion, 'reject')) {
                $workflow->apply($experienceVersion, 'reject');
                $this->getDoctrine()->getManager()->flush();
            }
        }
        return $this->redirectToRoute('easyadmin', array(
            'action' => 'edit',
            'id' => $experienceVersion->getId(),
            'entity' => 'ExperienceVersion',
        ));
    }

    /**
     * @Route("/{id}/startNewVersion", name="experience_start_new_version", methods={"POST"})
     */
    public function startFrom(ExperienceVersion $experienceVersion, Request $request): Response
    {
        if ($this->isCsrfTokenValid('startFrom'.$experienceVersion->getId(), $request->request->get('_token'))) {
            $newExperienceVersion = $experienceVersion->startNewVersion();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($newExperienceVersion);
            $entityManager->flush();
        }
        return $this->redirectToRoute('easyadmin', array(
            'action' => 'edit',
            'id' => $newExperienceVersion->getId(),
            'entity' => 'ExperienceVersion',
        ));

    }
}
