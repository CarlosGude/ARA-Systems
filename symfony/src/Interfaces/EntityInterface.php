<?php

namespace App\Interfaces;

use App\Entity\User;

interface EntityInterface
{
    public function getId(): ?string;

    public function getUser(): ?User;

    public function setUser(User $user): EntityInterface;
}
