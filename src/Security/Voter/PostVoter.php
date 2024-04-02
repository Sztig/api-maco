<?php

namespace App\Security\Voter;

use App\Entity\Post;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class PostVoter extends Voter
{
    public const EDIT = 'Edit';
    public const CREATE = 'Create';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if($attribute === self::CREATE){
            return true;
        }

        return in_array($attribute, [self::EDIT, self::CREATE])
            && $subject instanceof Post;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                if($user->getRoles()['isAdmin'] == true){
                    return true;
                }
                if($subject->getAuthor() === $user){
                    return true;
                }
                break;
            case self::CREATE:
                if($user->getRoles()['isAdmin'] == true){
                    return true;
                }
                if($user->getRoles()['name'] === 'ROLE_USER'){
                    return true;
                }
                break;
        }

        return false;
    }
}
