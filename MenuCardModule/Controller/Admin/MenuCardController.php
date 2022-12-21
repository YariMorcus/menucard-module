<?php

declare(strict_types=1);

namespace HetBonteHert\Module\MenuCard\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use HetBonteHert\Module\MenuCard\Entity\MenuCard;
use HetBonteHert\Module\MenuCard\Form\MenuCardType;
use HetBonteHert\Module\MenuCard\Repository\MenuCardRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tidi\Cms\Module\Core\FlashBag\MessageFactory;
use Tidi\Cms\Module\Core\Helpers\Breadcrumb;

/**
 * @Route("/menu/card")
 */
final class MenuCardController extends AbstractController
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
     * @var MenuCardRepository
     */
    private $menuCardRepository;

    /**
     * MenuCardController constructor.
     *
     * @param MenuCardRepository     $menuCardRepository
     * @param TranslatorInterface    $translator
     * @param EntityManagerInterface $entityManager
     * @param MessageFactory         $messageFactory
     * @param Breadcrumb             $breadcrumb
     * @param PaginatorInterface     $paginator
     */
    public function __construct(
        MenuCardRepository $menuCardRepository,
        TranslatorInterface $translator,
        EntityManagerInterface $entityManager,
        MessageFactory $messageFactory,
        Breadcrumb $breadcrumb,
        PaginatorInterface $paginator
    ) {
        $this->menuCardRepository = $menuCardRepository;
        $this->translator         = $translator;
        $this->entityManager      = $entityManager;
        $this->messageFactory     = $messageFactory;
        $this->breadcrumb         = $breadcrumb;
        $this->paginator          = $paginator;
    }

    /**
     * @Route("/", name="_menu_card.admin.menu_card_index", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EDITOR');

        $menuCards = $this->menuCardRepository->findBy([], ['id' => 'DESC']);

        $pagination = $this->paginator->paginate(
            $menuCards,
            $request->query->getInt('page', 1),
            20
        );

        $this->breadcrumb
            ->add(
                $this->generateUrl('_menu_card.admin.menu_card_index'),
                $this->translator->trans('menu_card.menu_card', ['%count%' => 10])
            );

        return $this->render('@MenuCard/Admin/MenuCard/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="_menu_card.admin.menu_card_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EDITOR');

        $menuCard = new MenuCard();
        $form     = $this->createForm(MenuCardType::class, $menuCard, [
            'action' => $this->generateUrl('_menu_card.admin.menu_card_new'),
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
            $entityManager->persist($menuCard);
            $entityManager->flush();

            $this->messageFactory->createCreatedMessage($this->translator->trans('menu_card.menu_card', ['%count%' => 1]));

            if ($form->get('save_and_edit')->isClicked()) {
                return $this->redirectToRoute(
                    '_menu_card.admin.menu_card_edit',
                    ['id' => $menuCard->getId()]
                );
            }

            return $this->redirectToRoute('_menu_card.admin.menu_card_index');
        }

        $this->breadcrumb
            ->add(
                $this->generateUrl('_menu_card.admin.menu_card_index'),
                $this->translator->trans('menu_card.menu_card', ['%count%' => 10])
            )
            ->add(
                $this->generateUrl('_menu_card.admin.menu_card_new', ['id' => $menuCard->getId()]),
                $this->translator->trans('menu_card.menu_card', ['%count%' => 1]).' '.$this->translator->trans('admin.add')
            );

        return $this->render('@MenuCard/Admin/MenuCard/new.html.twig', [
            'menu_card'   => $menuCard,
            'form'        => $form->createView(),
            'imagePrefix' => $menuCard->getImagePrefix(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="_menu_card.admin.menu_card_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, MenuCard $menuCard): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EDITOR');

        $form = $this->createForm(MenuCardType::class, $menuCard, [
            'action' => $this->generateUrl('_menu_card.admin.menu_card_edit', ['id' => $menuCard->getId()]),
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
            $this->messageFactory->createUpdatedMessage($this->translator->trans('menu_card.menu_card', ['%count%' => 1]));

            if ($form->get('save_and_edit')->isClicked()) {
                return $this->redirectToRoute(
                    '_menu_card.admin.menu_card_edit',
                    ['id' => $menuCard->getId()]
                );
            }

            return $this->redirectToRoute('_menu_card.admin.menu_card_index');
        }

        $this->breadcrumb
            ->add(
                $this->generateUrl('_menu_card.admin.menu_card_index'),
                $this->translator->trans('menu_card.menu_card', ['%count%' => 10])
            )
            ->add(
                $this->generateUrl('_menu_card.admin.menu_card_edit', ['id' => $menuCard->getId()]),
                $this->translator->trans('menu_card.menu_card', ['%count%' => 1]).' '.$this->translator->trans('admin.edit')
            );

        return $this->render('@MenuCard/Admin/MenuCard/edit.html.twig', [
            'menu_card'   => $menuCard,
            'form'        => $form->createView(),
            'imagePrefix' => $menuCard->getImagePrefix(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="_menu_card.admin.menu_card_delete", methods={"GET", "POST"})
     */
    public function delete(Request $request, MenuCard $menuCard): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EDITOR');

        $form = $this->createDeleteForm($menuCard);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$form->get('cancel')->isClicked()) {
                $this->entityManager->remove($menuCard);
                $this->entityManager->flush();

                $this->messageFactory->createDeletedMessage($this->translator->trans('menu_card.menu_card', ['%count%' => 1]));
            }

            return $this->redirectToRoute('_menu_card.admin.menu_card_index');
        }

        $this->breadcrumb
            ->add(
                $this->generateUrl('_menu_card.admin.menu_card_index'),
                $this->translator->trans('menu_card.menu_card', ['%count%' => 10])
            )
            ->add(
                $this->generateUrl('_menu_card.admin.menu_card_delete', ['id' => $menuCard->getId()]),
                $this->translator->trans('menu_card.menu_card', ['%count%' => 1]).' '.$this->translator->trans('admin.delete')
            );

        return $this->render('@MenuCard/Admin/MenuCard/delete.html.twig', [
            'menuCard' => $menuCard,
            'form'     => $form->createView(),
        ]);
    }

    /**
     * Creates a form to delete an entity.
     *
     * @param MenuCard $menuCard
     *
     * @return FormInterface The form
     */
    private function createDeleteForm(MenuCard $menuCard)
    {
        return $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    '_menu_card.admin.menu_card_delete',
                    [
                        'id' => $menuCard->getId(),
                    ]
                )
            )
            ->setMethod('POST')
            ->add('submit', SubmitType::class, [
                'label' => $this->translator->trans(
                    'admin.item.delete.agreed',
                    ['%item%' => $this->translator->trans('menu_card.menu_card', ['%count%' => 1])]
                ),
                'attr'  => ['class' => 'btn-default'],
            ])
            ->add('cancel', SubmitType::class, ['label' => 'admin.cancel', 'attr' => ['class' => 'btn-danger']])
            ->getForm()
            ;
    }
}
