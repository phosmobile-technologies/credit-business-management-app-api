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

    const CAN_CREATE_LOAN_APPLICATIONS = "CAN_CREATE_LOAN_APPLICATIONS";

    const CAN_CREATE_LOANS = "CAN_CREATE_LOANS";
    const CAN_UPDATE_LOAN_STATUS = "CAN_UPDATE_LOAN_STATUS";
}
