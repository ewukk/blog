<?php
/**
 * User repository.
 */

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    /**
     * Pagination.
     */
    public const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Password hasher.
     */
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * Constructor.
     *
     * @param ManagerRegistry             $registry       Registry
     * @param UserPasswordHasherInterface $passwordHasher Password hasher
     */
    public function __construct(ManagerRegistry $registry, UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
        parent::__construct($registry, User::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select('partial user.{id, email, password}')
            ->orderBy('user.id', 'DESC');
    }

    /**
     * Save Password user.
     *
     * @param User $entity User
     */
    public function savePassword(User $entity): void
    {
        $hashedPassword = $this->passwordHasher->hashPassword($entity, $entity->getPassword());
        $entity->setPassword($hashedPassword);
        $this->getEntityManager()->persist($entity);

        $this->getEntityManager()->flush();
    }

    /**
     * Save user.
     *
     * @param User $entity User
     */
    public function save(User $entity): void
    {
        $this->_em->persist($entity);
        $this->_em->flush();
    }

    /**
     * Remove user.
     *
     * @param User $entity User
     * @param bool $flush  flush
     */
    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Edit user.
     *
     * @param User $entity User
     */
    public function edit(User $entity): void
    {
        $hashedPassword = $this->passwordHasher->hashPassword($entity, $entity->getPassword());
        $entity->setPassword($hashedPassword);
        $this->getEntityManager()->persist($entity);

        $this->getEntityManager()->flush();
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     *
     * @param PasswordAuthenticatedUserInterface $user              User
     * @param string                             $newHashedPassword New hashed password
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }

    /**
     * Get or create new query builder.
     *
     * @param QueryBuilder|null $queryBuilder Query builder
     *
     * @return QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('user');
    }
}
