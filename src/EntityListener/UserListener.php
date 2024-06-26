<?php
namespace App\EntityListener;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

class UserListener
{   
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function prePersist(User $user)
    {
        $this->encodePassword($user);
    }
    public function preUpdate(User $user)
    {
        $this->encodePassword($user);
    }
/**
 * Encode password base on plain password
 * @param User $user
 * @return void
 */

    public function encodePassword(User $user)
    {
        if($user->getPlainPassword() === null){
            return;
        }
        $user->setPassword($this->hasher->hashPassword(
            $user,
            $user->getPlainPassword()
        ));

        $user->setPlainPassword("");
    }
}