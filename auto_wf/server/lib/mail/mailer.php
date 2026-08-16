<?php

require_once __DIR__ . '/config.php';

/**
 * Отправка через mail(). В OpenServer письма никуда не уходят, а складываются в
 * C:\OpenServer\userdata\temp\email — локально этого хватает для проверки.
 */

function mail_send_html(string $to, string $subject, string $html): array
{
    $encodedName = '=?UTF-8?B?' . base64_encode(mail_from_name()) . '?=';
    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . $encodedName . ' <' . mail_from_email() . '>',
        'Reply-To: ' . mail_from_email(),
    ];

    $sent = @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $html, implode("\r\n", $headers));

    return ['sent' => (bool)$sent];
}

function mail_link_html(string $url): string
{
    $safe = htmlspecialchars($url, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    return '<p style="margin: 0 0 16px;"><a href="' . $safe . '">' . $safe . '</a></p>';
}

function mail_send_email_verification(string $email, string $token): array
{
    // Оба действия по ссылкам открываются отдельными страницами: приложение со
    // сценой ради одного запроса грузить незачем.
    $url = mail_app_base_url() . '/verify-email.html?token=' . rawurlencode($token);

    return mail_send_html($email, 'Подтверждение email', ''
        . '<h2 style="margin: 0 0 16px;">Подтвердите email</h2>'
        . '<p style="margin: 0 0 12px;">Вы зарегистрировались в приложении «Тёплый пол».</p>'
        . '<p style="margin: 0 0 16px;">Чтобы завершить регистрацию, перейдите по ссылке:</p>'
        . mail_link_html($url)
        . '<p style="margin: 0; color: #666;">Ссылка действует 24 часа.</p>');
}

function mail_send_password_reset(string $email, string $token): array
{
    $url = mail_app_base_url() . '/reset-password.html?token=' . rawurlencode($token);

    return mail_send_html($email, 'Сброс пароля', ''
        . '<h2 style="margin: 0 0 16px;">Сброс пароля</h2>'
        . '<p style="margin: 0 0 12px;">Получен запрос на обновление пароля.</p>'
        . '<p style="margin: 0 0 16px;">Чтобы задать новый пароль, перейдите по ссылке:</p>'
        . mail_link_html($url)
        . '<p style="margin: 0; color: #666;">Ссылка действует 60 минут.'
        . ' Если это были не вы, просто проигнорируйте письмо.</p>');
}
