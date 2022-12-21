<?php

declare(strict_types=1);

namespace HetBonteHert\Module\MenuCard\Controller;

use HetBonteHert\Module\MenuCard\Entity\MenuPage;
use HetBonteHert\Module\MenuCard\Module\MenuPageModule;
use HetBonteHert\Module\MenuCard\Repository\MenuPageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tidi\Cms\Module\Core\Entity\Structure;
use Tidi\Cms\Module\Core\Exception\InvalidModuleException;
use Tidi\Cms\Module\Core\Security\Authorization\Voter\StructureVoter;

final class MenuPageController extends AbstractController
{
    /**
     * @var MenuPageRepository
     */
    private $menuPageRepository;

    /**
     * PageController constructor.
     *
     * @param MenuPageRepository $menuPageRepository
     */
    public function __construct(
        MenuPageRepository $menuPageRepository
    ) {
        $this->menuPageRepository = $menuPageRepository;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function detail(Request $request): Response
    {
        $structure = $request->attributes->get('structure');

        if (!$structure instanceof Structure) {
            throw new NotFoundHttpException();
        }

        // check if valid module
        $isValidModule = (MenuPageModule::MODULE_SCRIPT === $structure->getModule()->getScript());
        if (!$isValidModule) {
            throw new InvalidModuleException(InvalidModuleException::INVALID_MODULE);
        }

        $this->denyAccessUnlessGranted(StructureVoter::READ, $structure);

        /** @var MenuPage $menuPage */
        $menuPage = $this->menuPageRepository->findOneBy(['structure' => $structure]);
        if (!$menuPage instanceof MenuPage) {
            throw new NotFoundHttpException();
        }

        $response = new Response();
        //$response->setLastModified($menuPage->getModifiedAt());
        // if ($structure->canBeCached()) {
        //     $response->setPublic();
        //     $response->setMaxAge(300);
        //     $response->setSharedMaxAge(600);
        //     $response->headers->addCacheControlDirective('must-revalidate', true);
        // }

        //if ($response->isNotModified($request)) {
        //    return $response;
        //}

        $request->query->set('id', $menuPage->getId());

        return $this->render('@MenuCard/MenuPage/index.html.twig', [
            'menuPage' => $menuPage,
        ], $response);
    }
}
