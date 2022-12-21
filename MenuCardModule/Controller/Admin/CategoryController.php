<?php

declare(strict_types=1);

namespace HetBonteHert\Module\MenuCard\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use HetBonteHert\Module\MenuCard\Entity\Category;
use HetBonteHert\Module\MenuCard\Exception\CategoryInUseException;
use HetBonteHert\Module\MenuCard\Form\CategoryType;
use HetBonteHert\Module\MenuCard\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tidi\Cms\Module\Core\FlashBag\MessageFactory;
use Tidi\Cms\Module\Core\Helpers\Breadcrumb;

/**
 * @Route("/category")
 */
final class CategoryController extends AbstractController
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var Breadcrumb
     */
    private $breadcrumb;

    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * CategoryController constructor.
     *
     * @param CategoryRepository     $categoryRepository
     * @param TranslatorInterface    $translator
     * @param EntityManagerInterface $entityManager
     * @param MessageFactory         $messageFactory
     * @param Breadcrumb             $breadcrumb
     * @param PaginatorInterface     $paginator
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        TranslatorInterface $translator,
        EntityManagerInterface $entityManager,
        MessageFactory $messageFactory,
        Breadcrumb $breadcrumb,
        PaginatorInterface $paginator
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->translator         = $translator;
        $this->entityManager      = $entityManager;
        $this->messageFactory     = $messageFactory;
        $this->breadcrumb         = $breadcrumb;
        $this->paginator          = $paginator;
    }

    /**
     * @Route("/", name="_menu_card.admin.category_index", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EDITOR');

        $categories = $this->categoryRepository->findBy([], ['id' => 'DESC']);

        $pagination = $this->paginator->paginate(
            $categories,
            $request->query->getInt('page', 1),
            20
        );

        $this->breadcrumb
            ->add(
                $this->generateUrl('_menu_card.admin.category_index'),
                $this->translator->trans('menu_card.category', ['%count%' => 10])
            );

        return $this->render('@MenuCard/Admin/Category/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="_menu_card.admin.category_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EDITOR');

        $category = new Category();
        $form     = $this->createForm(CategoryType::class, $category, [
            'action' => $this->generateUrl('_menu_card.admin.category_new'),
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
                'label' => 'admin.add.saveChanges',
                'attr'  => [
                    'class' => 'btn btn-default',
                ],
            ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            $this->messageFactory->createCreatedMessage($this->translator->trans('menu_card.category', ['%count%' => 1]));

            if ($form->get('save_and_edit')->isClicked()) {
                return $this->redirectToRoute(
                    '_menu_card.admin.category_edit',
                    ['id' => $category->getId()]
                );
            }

            return $this->redirectToRoute('_menu_card.admin.category_index');
        }

        $this->breadcrumb
            ->add(
                $this->generateUrl('_menu_card.admin.category_index'),
                $this->translator->trans('menu_card.category', ['%count%' => 10])
            )
            ->add(
                $this->generateUrl('_menu_card.admin.category_new', ['id' => $category->getId()]),
                $this->translator->trans('menu_card.category', ['%count%' => 1]).' '.$this->translator->trans('admin.add')
            );

        return $this->render('@MenuCard/Admin/Category/new.html.twig', [
            'category'    => $category,
            'form'        => $form->createView(),
            'imagePrefix' => $category->getImagePrefix(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="_menu_card.admin.category_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Category $category): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EDITOR');

        $form = $this->createForm(CategoryType::class, $category, [
            'action' => $this->generateUrl('_menu_card.admin.category_edit', ['id' => $category->getId()]),
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
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->messageFactory->createUpdatedMessage($this->translator->trans('menu_card.category', ['%count%' => 1]));

            if ($form->get('save_and_edit')->isClicked()) {
                return $this->redirectToRoute(
                    '_menu_card.admin.category_edit',
                    ['id' => $category->getId()]
                );
            }

            return $this->redirectToRoute('_menu_card.admin.category_index');
        }

        $this->breadcrumb
            ->add(
                $this->generateUrl('_menu_card.admin.category_index'),
                $this->translator->trans('menu_card.category', ['%count%' => 10])
            )
            ->add(
                $this->generateUrl('_menu_card.admin.category_edit', ['id' => $category->getId()]),
                $this->translator->trans('menu_card.category', ['%count%' => 1]).' '.$this->translator->trans('admin.edit')
            );

        return $this->render('@MenuCard/Admin/Category/edit.html.twig', [
            'category'    => $category,
            'form'        => $form->createView(),
            'imagePrefix' => $category->getImagePrefix(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="_menu_card.admin.category_delete", methods={"GET", "POST"})
     */
    public function delete(Request $request, Category $category): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EDITOR');

        $form = $this->createDeleteForm($category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if (!$form->get('cancel')->isClicked()) {
                    $this->entityManager->remove($category);
                    $this->entityManager->flush();

                    $this->messageFactory->createDeletedMessage($this->translator->trans('menu_card.category', ['%count%' => 1]));
                }

                return $this->redirectToRoute('_menu_card.admin.category_index');
            } catch (CategoryInUseException $exception) {
                $this->addFlash('error', $exception->getMessage());
            }
        }

        $this->breadcrumb
            ->add(
                $this->generateUrl('_menu_card.admin.category_index'),
                $this->translator->trans('menu_card.category', ['%count%' => 10])
            )
            ->add(
                $this->generateUrl('_menu_card.admin.category_delete', ['id' => $category->getId()]),
                $this->translator->trans('menu_card.category', ['%count%' => 1]).' '.$this->translator->trans('admin.delete')
            );

        return $this->render('@MenuCard/Admin/Category/delete.html.twig', [
            'category' => $category,
            'form'     => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/active", name="_menu_card.admin.category_toggle_active", methods={"GET"})
     */
    public function toggleActiveAction(Category $category): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $category->setActive(!$category->isActive());
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $this->messageFactory->createToggleMessage($category->isActive(), 'menu_card.category');

        return $this->redirectToRoute('_menu_card.admin.category_index');
    }

    /**
     * Creates a form to delete an entity.
     *
     * @param Category $category
     *
     * @return FormInterface The form
     */
    private function createDeleteForm(Category $category)
    {
        return $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    '_menu_card.admin.category_delete',
                    [
                        'id' => $category->getId(),
                    ]
                )
            )
            ->setMethod('POST')
            ->add('submit', SubmitType::class, [
                'label' => $this->translator->trans(
                    'admin.item.delete.agreed',
                    ['%item%' => $this->translator->trans('menu_card.category', ['%count%' => 1])]
                ),
                'attr'  => ['class' => 'btn-default'],
            ])
            ->add('cancel', SubmitType::class, ['label' => 'admin.cancel', 'attr' => ['class' => 'btn-danger']])
            ->getForm()
            ;
    }
}
