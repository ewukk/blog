<?php
/**
 * User controller.
 */

namespace App\Controller;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use App\Form\Type\EditPasswordType;
use App\Form\Type\RegistrationType;
use App\Form\Type\UserType;
use App\Service\UserServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserController.
 */
#[Route('/user')]
class UserController extends AbstractController
{
    /**
     * User service.
     */
    private UserServiceInterface $userService;

    /**
     * Translator.
     *
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * Constructor.
     *
     * @param UserServiceInterface $postService Post service
     * @param TranslatorInterface  $translator  Translator
     */
    public function __construct(UserServiceInterface $postService, TranslatorInterface $translator)
    {
        $this->userService = $postService;
        $this->translator = $translator;
    }

    /**
     * Index action.
     *
     * @param Request $request HTTP Request
     *
     * @return Response HTTP response
     */
    #[Route(name: 'user_index', methods: 'GET')]
    #[IsGranted('MANAGE')]
    public function index(Request $request): Response
    {
        $pagination = $this->userService->createPaginatedList(
            $request->query->getInt('page', 1)
        );

        return $this->render('user/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param user $user user
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'user_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
//    #[IsGranted('MANAGE')]
    public function show(user $user): Response
    {
        return $this->render('user/show.html.twig', ['user' => $user]);
    }

    /**
     * Edit password action.
     *
     * @param Request $request HTTP request
     * @param User    $user    User entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/password', name: 'edit_password', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    public function passwordEdit(Request $request, User $user): Response
    {
            $form = $this->createForm(
                EditPasswordType::class,
                $user,
                [
                    'method' => 'PUT',
                    'action' => $this->generateUrl('edit_password', ['id' => $user->getId()]),
                ]
            );
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->userService->savePassword($user);

                $this->addFlash(
                    'success',
                    $this->translator->trans('message.edited_successfully')
                );

                return $this->redirectToRoute('post_index');
            }

            return $this->render(
                'user/password.html.twig',
                [
                    'form' => $form->createView(),
                    'user' => $user,
                ]
            );

    }

    /**
     * Edit action.
     *
     * @param Request  $request  HTTP request
     * @param User $user user entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'user_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
//    #[IsGranted('EDIT')]
    public function edit(Request $request, User $user): Response
    {

        $form = $this->createForm(
            UserType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('user_edit', ['id' => $user->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->save($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('post_index');
        }

        return $this->render(
            'user/edit.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

    /**
     * Register user action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/register', name: 'user_register', methods: 'GET|POST')]
    public function register(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(
            RegistrationType::class,
            $user,
            ['action' => $this->generateUrl('user_register')]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles([UserRole::ROLE_USER->value]);
            $this->userService->savePassword($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('post_index');
        }

        return $this->render(
            'user/register.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

}
