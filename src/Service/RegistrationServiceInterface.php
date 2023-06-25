<?php
/**
 * Registration service interface.
 */

namespace App\Service;

use App\Entity\User;

/**
 * Interface PostServiceInterface.
 */
interface RegistrationServiceInterface
{
    /**
     * Register user.
     *
     * @param      $data
     * @param User $user User entity
     */
    public function register($data, User $user);

    /**
     * Save entity.
     *
     * @param User $user User entity
     */
    public function save(User $user): void;
}
