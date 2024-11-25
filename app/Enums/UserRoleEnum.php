<?php

namespace App\Enums;

enum UserRoleEnum: string {
    case Admin = "Administrateur";
    case User = "Utilisateur";
}