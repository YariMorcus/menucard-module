<?php

declare(strict_types=1);

namespace HetBonteHert\Module\MenuCard\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use HetBonteHert\Module\MenuCard\Entity\Category;
use HetBonteHert\Module\MenuCard\Entity\Dish;
use HetBonteHert\Module\MenuCard\Form\DishType;
use HetBonteHert\Module\MenuCard\Repository\CategoryRepository;
use HetBonteHert\Module\MenuCard\Repository\DishRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
 * @Route("/dish")
 */
final class DishController extends AbstractController
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
     * @var DishRepository
     */
    private $dishRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * DishController constructor.
     *
     * @param DishRepository         $dishRepository
     * @param TranslatorInterface    $translator
     * @param EntityManagerInterface $entityManager
     * @param MessageFactory         $messageFactory
     * @param Breadcrumb             $breadcrumb
     * @param PaginatorInterface     $paginator
     */
    public function __construct(
        DishRepository $dishRepository,
        TranslatorInterface $translator,
        EntityManagerInterface $entityManager,
        MessageFactory $messageFactory,
        Breadcrumb $breadcrumb,
        PaginatorInterface $paginator,
        CategoryRepository $categoryRepository
    ) {
        $this->dishRepository     = $dishRepository;
        $this->translator         = $translator;
        $this->entityManager      = $entityManager;
        $this->messageFactory     = $messageFactory;
        $this->breadcrumb         = $breadcrumb;
        $this->paginator          = $paginator;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Route("/", name="_menu_card.admin.dish_index", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EDITOR');

        $dishes = $this->dishRepository->findBy([], ['id' => 'DESC']);

        $categories = $this->categoryRepository->findAll();

        $pagination = $this->paginator->paginate(
            $dishes,
            $request->query->getInt('page', 1),
            20
        );

        $this->breadcrumb
            ->add(
                $this->generateUrl('_menu_card.admin.dish_index'),
                $this->translator->trans('menu_card.dish', ['%count%' => 10])
            );

        return $this->render('@MenuCard/Admin/Dish/index.html.twig', [
            'pagination' => $pagination,
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/{categoryId}/new", name="_menu_card.admin.dish_new_in_category", methods={"GET", "POST"})
     * @Route("/new", name="_menu_card.admin.dish_new", methods={"GET", "POST"})
     * @ParamConverter("category", options={"id": "categoryId"})
     */
    public function new(Request $request, ?Category $category): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EDITOR');

        $dish = new Dish();

        if (null !== $category) {
            $dish->setCategory($category);
        }

        $form = $this->createForm(DishType::class, $dish, [
            'action' => $this->generateUrl('_menu_card.admin.dish_new'),
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
            $entityManager->persist($dish);
            $entityManager->flush();

            $this->messageFactory->createCreatedMessage($this->translator->trans('menu_card.dish', ['%count%' => 1]));

            if ($form->get('save_and_edit')->isClicked()) {
                return $this->redirectToRoute(
                    '_menu_card.admin.dish_edit',
                    ['id' => $dish->getId()]
                );
            }

            return $this->redirectToRoute('_menu_card.admin.dish_index');
        }

        $this->breadcrumb
            ->add(
                $this->generateUrl('_menu_card.admin.dish_index'),
                $this->translator->trans('menu_card.dish', ['%count%' => 10])
            )
            ->add(
                $this->generateUrl('_menu_card.admin.dish_new', ['id' => $dish->getId()]),
                $this->translator->trans('menu_card.dish', ['%count%' => 1]).' '.$this->translator->trans('admin.add')
            );

        return $this->render('@MenuCard/Admin/Dish/new.html.twig', [
            'dish'        => $dish,
            'form'        => $form->createView(),
            'imagePrefix' => $dish->getImagePrefix(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="_menu_card.admin.dish_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Dish $dish): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EDITOR');

        $form = $this->createForm(DishType::class, $dish, [
            'action' => $this->generateUrl('_menu_card.admin.dish_edit', ['id' => $dish->getId()]),
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
            $this->messageFactory->createUpdatedMessage($this->translator->trans('menu_card.dish', ['%count%' => 1]));

            if ($form->get('save_and_edit')->isClicked()) {
                return $this->redirectToRoute(
                    '_menu_card.admin.dish_edit',
                    ['id' => $dish->getId()]
                );
            }

            return $this->redirectToRoute('_menu_card.admin.dish_index');
        }

        $this->breadcrumb
            ->add(
                $this->generateUrl('_menu_card.admin.dish_index'),
                $this->translator->trans('menu_card.dish', ['%count%' => 10])
            )
            ->add(
                $this->generateUrl('_menu_card.admin.dish_edit', ['id' => $dish->getId()]),
                $this->translator->trans('menu_card.dish', ['%count%' => 1]).' '.$this->translator->trans('admin.edit')
            );

        return $this->render('@MenuCard/Admin/Dish/edit.html.twig', [
            'dish'        => $dish,
            'form'        => $form->createView(),
            'imagePrefix' => $dish->getImagePrefix(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="_menu_card.admin.dish_delete", methods={"GET", "POST"})
     */
    public function delete(Request $request, Dish $dish): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EDITOR');

        $form = $this->createDeleteForm($dish);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$form->get('cancel')->isClicked()) {
                $this->entityManager->remove($dish);
                $this->entityManager->flush();

                $this->messageFactory->createDeletedMessage($this->translator->trans('menu_card.dish', ['%count%' => 1]));
            }

            return $this->redirectToRoute('_menu_card.admin.dish_index');
        }

        $this->breadcrumb
            ->add(
                $this->generateUrl('_menu_card.admin.dish_index'),
                $this->translator->trans('menu_card.dish', ['%count%' => 10])
            )
            ->add(
                $this->generateUrl('_menu_card.admin.dish_delete', ['id' => $dish->getId()]),
                $this->translator->trans('menu_card.dish', ['%count%' => 1]).' '.$this->translator->trans('admin.delete')
            );

        return $this->render('@MenuCard/Admin/Dish/delete.html.twig', [
            'dish'   => $dish,
            'form'   => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/active", name="_menu_card.admin.dish_toggle_active", methods={"GET"})
     */
    public function toggleActiveAction(Dish $dish): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $dish->setActive(!$dish->isActive());
        $this->entityManager->persist($dish);
        $this->entityManager->flush();

        $this->messageFactory->createToggleMessage($dish->isActive(), 'menu_card.dish');

        return $this->redirectToRoute('_menu_card.admin.dish_index');
    }

    /**
     * Creates a form to delete an entity.
     *
     * @param Dish $dish
     *
     * @return FormInterface The form
     */
    private function createDeleteForm(Dish $dish)
    {
        return $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    '_menu_card.admin.dish_delete',
                    [
                        'id' => $dish->getId(),
                    ]
                )
            )
            ->setMethod('POST')
            ->add('submit', SubmitType::class, [
                'label' => $this->translator->trans(
                    'admin.item.delete.agreed',
                    ['%item%' => $this->translator->trans('menu_card.dish', ['%count%' => 1])]
                ),
                'attr'  => ['class' => 'btn-default'],
            ])
            ->add('cancel', SubmitType::class, ['label' => 'admin.cancel', 'attr' => ['class' => 'btn-danger']])
            ->getForm()
            ;
    }
}
