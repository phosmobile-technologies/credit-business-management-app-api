<?php


namespace App\Models;

/**
 * Class UserPermissions
 *
 * The various permissions that users can have
 *
 * @package App\Models
 */
abstract class UserPermissions
{
    const SAVE_MONEY = "SAVE_MONEY";
    const WITHDRAW_MONEY = "WITHDRAW_MONEY";

    // Admin User Permissions
    const CAN_CRUD_USERS = "CAN_CRUD_USERS";
}
