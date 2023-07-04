<?php
/**
 * User service.
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserService.
 */
class UserService implements UserServiceInterface
{
    /**
     * User repository.
     */
    private UserRepository $userRepository;

    /**
     * Paginator.
     */
    private PaginatorInterface $paginator;

    /**
     * Comment service.
     */
    private CommentServiceInterface $commentService;

    /**
     * Password hasher.
     */
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * UserService constructor.
     *
     * @param UserPasswordHasherInterface $passwordHasher Password hasher
     * @param CommentServiceInterface     $commentService Comment service
     * @param UserRepository              $userRepository User repository
     * @param PaginatorInterface          $paginator      Paginator
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher, CommentServiceInterface $commentService, UserRepository $userRepository, PaginatorInterface $paginator)
    {
        $this->passwordHasher = $passwordHasher;
        $this->commentService = $commentService;
        $this->userRepository = $userRepository;
        $this->paginator = $paginator;
    }

    /**
     * Create paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface Paginated list
     */
    public function createPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->userRepository->queryAll(),
            $page,
            UserRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save user.
     *
     * @param User $user User entity
     */
    public function save(User $user): void
    {
        $this->userRepository->save($user);
    }

    /**
     * Save entity with hashed password.
     *
     * @param User $user User entity
     */
    public function savePassword(User $user): void
    {
        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            )
        );
        $this->userRepository->save($user);
    }

    /**
     * Edit user.
     *
     * @param User $user User entity
     */
    public function edit(User $user): void
    {
        $this->userRepository->edit($user);
    }

    /**
     * Remove user.
     *
     * @param User $user User entity
     */
    public function remove(User $user): void
    {
        $this->userRepository->remove($user);
    }

    /**
     * Find user.
     *
     * @param string $email Email
     *
     * @return User|null $email Email
     */
    public function findOneBy(string $email): ?User
    {
        return $this->userRepository->findOneBy(['email' => $email]);
    }
}
