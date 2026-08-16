<?php

/**
 * Длина пароля считается в байтах, а не в символах: bcrypt (PASSWORD_DEFAULT)
 * молча обрезает всё после 72 байт, и 64 кириллических символа — это 128 байт,
 * половина из которых в хеш не попадёт.
 */

const AUTH_PASSWORD_MIN_BYTES = 6;
const AUTH_PASSWORD_MAX_BYTES = 72;

function auth_validate_email(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function auth_validate_password(string $password): bool
{
    $length = strlen($password);
    return $length >= AUTH_PASSWORD_MIN_BYTES && $length <= AUTH_PASSWORD_MAX_BYTES;
}

function auth_normalize_email(string $email): string
{
    return mb_strtolower(trim($email));
}
