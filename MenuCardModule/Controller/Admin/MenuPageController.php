<?php

declare(strict_types=1);

namespace HetBonteHert\Module\MenuCard\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use HetBonteHert\Module\MenuCard\Entity\MenuPage;
use HetBonteHert\Module\MenuCard\Form\MenuPageType;
use HetBonteHert\Module\MenuCard\Repository\MenuPageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tidi\Cms\Module\Core\Entity\Structure;
use Tidi\Cms\Module\Core\FlashBag\MessageFactory;
use Tidi\Cms\Module\Core\Helpers\StructureBreadcrumbHelper;
use Tidi\Cms\Module\Core\Repository\StructureRepository;
use Tidi\Cms\Module\Core\Security\Authorization\Voter\StructureVoter;

/**
 * @Route("/menu/page/{structureId}")
 */
final class MenuPageController extends AbstractController
{
    /**
     * @var MenuPageRepository
     */
    private $menuPageRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var StructureBreadcrumbHelper
     */
    private $breadcrumb;

    /**
     * @var StructureRepository
     */
    private $structureRepository;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * PageAdminController constructor.
     *
     * @param StructureRepository       $structureRepository
     * @param MenuPageRepository        $menuPageRepository
     * @param EntityManagerInterface    $entityManager
     * @param StructureBreadcrumbHelper $breadcrumb
     * @param MessageFactory            $messageFactory
     * @param TranslatorInterface       $translator
     */
    public function __construct(
        StructureRepository $structureRepository,
        MenuPageRepository $menuPageRepository,
        EntityManagerInterface $entityManager,
        StructureBreadcrumbHelper $breadcrumb,
        MessageFactory $messageFactory,
        TranslatorInterface $translator
    ) {
        $this->structureRepository = $structureRepository;
        $this->menuPageRepository  = $menuPageRepository;
        $this->entityManager       = $entityManager;
        $this->breadcrumb          = $breadcrumb;
        $this->messageFactory      = $messageFactory;
        $this->translator          = $translator;
    }

    /**
     * Lists all MenuPage entities.
     *
     * @param int $structureId
     *
     * @return RedirectResponse
     *
     * @Route("/", name="_menu_card.admin.menu_page_index", methods={"GET"})
     */
    public function index(int $structureId): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_EDITOR');

        $structure =  $this->structureRepository->find($structureId);
        if (!$structure instanceof Structure) {
            throw new NotFoundHttpException();
        }

        $this->denyAccessUnlessGranted(StructureVoter::WRITE, $structure);

        $menuPage =  $this->menuPageRepository->findOneByStructure($structure);

        if (!$menuPage instanceof MenuPage) {
            $menuPage = new MenuPage($structure);

            $this->entityManager->persist($menuPage);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute(
            '_menu_card.admin.menu_page_edit',
            ['structureId' => $menuPage->getStructureId(), 'id' => $menuPage->getId()]
        );
    }

    /**
     * Displays a form to edit an existing MenuPage entity.
     *
     * @param Request  $request
     * @param int      $structureId
     * @param MenuPage $menuPage
     *
     * @return Response
     * @Route("/{id}/edit", name="_menu_card.admin.menu_page_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, int $structureId, MenuPage $menuPage): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EDITOR');

        $structure = $this->structureRepository->find($structureId);
        $this->denyAccessUnlessGranted(StructureVoter::WRITE, $structure);

        if ($structure !== $menuPage->getStructure()) {
            throw new NotFoundHttpException();
        }

        $parentId = $menuPage->getStructure()->getParentId();
        $siteId   = $menuPage->getStructure()->getSiteId();

        $editForm = $this->createEditForm($menuPage);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->entityManager->persist($menuPage);
            $this->entityManager->flush();

            $this->messageFactory->createUpdatedMessage('admin.menuPage');

            if ($editForm->get('save_and_edit')->isClicked()) {
                return $this->redirectToRoute('_menu_card.admin.menu_page_edit', [
                    'structureId' => $menuPage->getStructureId(),
                    'id'          => $menuPage->getId(),
                ]);
            }

            return $this->redirectToRoute('_admin_core_structure_index', [
                'siteId'      => $siteId,
                'structureId' => $parentId,
            ]);
        }

        //Breadcrumbs
        $breadcrumbs = $this->breadcrumb->fillBreadcrumb($structure, $structure->getSite());

        $breadcrumbs->add(
            $this->generateUrl(
                '_menu_card.admin.menu_page_edit',
                ['structureId' => $menuPage->getStructureId(), 'id' => $menuPage->getId()]
            ),
            $this->translator->trans('admin.item.edit', ['%item%' => $structure->getName()])
        );

        return $this->render(
            '@MenuCard/Admin/MenuPage/edit.html.twig',
            [
                'menuPage'       => $menuPage,
                'structure'      => $menuPage->getStructure(),
                'form'           => $editForm->createView(),
                'structureId'    => $structureId,
                'imagePrefix'    => $menuPage->getImagePrefix(),
            ]
        );
    }

    /**
     * Creates a form to edit a MenuPage entity.
     *
     * @param MenuPage $entity The entity
     *
     * @return FormInterface The form
     */
    private function createEditForm(MenuPage $entity): FormInterface
    {
        $form = $this->createForm(MenuPageType::class, $entity, [
            'action' => $this->generateUrl(
                '_menu_card.admin.menu_page_edit',
                [
                    'structureId' => $entity->getStructureId(),
                    'id'          => $entity->getId(),
                ]
            ),
            'attr'   => ['class' => 'js-unsaved-warning'],
            'method' => 'POST',
        ]);

        $form
            ->add('save', SubmitType::class, [
                'label' => 'admin.saveChanges',
                'attr'  => [
                    'class' => 'btn btn-success',
                    'icon'  => 'fa fa-check',
                ],
            ])
            ->add('save_and_edit', SubmitType::class, [
                'label' => 'admin.edit.saveChanges',
                'attr'  => [
                    'class' => 'btn btn-default',
                ],
            ]);

        return $form;
    }
}
