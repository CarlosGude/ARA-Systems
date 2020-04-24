<?php


namespace App\Security;


abstract class AbstractUserRoles
{
    public const ROLE_GOD = 'ROLE_GOD';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    public const ROLE_LIST_PROVIDER = 'ROLE_LIST_PROVIDER';
    public const ROLE_CREATE_PROVIDER = 'ROLE_CREATE_PROVIDER';
    public const ROLE_EDIT_PROVIDER = 'ROLE_EDIT_PROVIDER';
    public const ROLE_DELETE_PROVIDER = 'ROLE_DELETE_PROVIDER';
}