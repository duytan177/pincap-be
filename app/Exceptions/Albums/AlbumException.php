<?php

namespace App\Exceptions\Albums;

use App\Exceptions\BaseException;

class AlbumException extends BaseException
{
    public static function forbidden(string $message = 'Forbidden')
    {
        return self::code($message, [], 403);
    }

    public static function notOwner()
    {
        return self::code('You are not the album owner', [], 403);
    }

    public static function deleteFailed()
    {
        return self::code('Deleted album failed', [], 400);
    }

    public static function alreadyInvited()
    {
        return self::code('User already invited', [], 409);
    }

    public static function alreadyMember()
    {
        return self::code('User already a member', [], 409);
    }

    public static function invitationNotFound()
    {
        return self::code('Invitation not found', [], 404);
    }

    public static function invitationAlreadyRejected()
    {
        return self::code('Invitation already rejected', [], 409);
    }
}


