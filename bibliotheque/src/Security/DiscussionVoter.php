<?php

namespace App\Security;

use App\Entity\Discussion;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class DiscussionVoter extends Voter
{
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::EDIT, self::DELETE])) {
            return false;
        }

        if (!$subject instanceof Discussion) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Discussion $discussion */
        $discussion = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($discussion, $user);
            case self::DELETE:
                return $this->canDelete($discussion, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canEdit(Discussion $discussion, User $user): bool
    {
        return $user === $discussion->getAuteur();
    }

    private function canDelete(Discussion $discussion, User $user): bool
    {
        return $user === $discussion->getAuteur();
    }
}