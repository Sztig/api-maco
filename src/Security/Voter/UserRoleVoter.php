<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserRoleVoter extends Voter
{
    public const EDIT = 'Edit';
    public const CREATE = 'Create';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if($attribute === self::CREATE){
            return true;
        }

        return in_array($attribute, [self::EDIT, self::CREATE])
            && $subject instanceof \App\Entity\UserRole;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::CREATE:
            case self::EDIT:
                if($user->getRoles()['isAdmin'] == true){
                    return true;
                }
                break;
        }

        return false;
    }
}
