<?php

namespace Kirby\Cms\Auth;

use Kirby\Cms\User;
use Kirby\Toolkit\I18n;
use Kirby\Toolkit\Str;

/**
 * Creates and verifies one-time auth codes
 * that are sent via email
 *
 * @package   Kirby Cms
 * @author    Lukas Bestle <lukas@getkirby.com>
 * @link      https://getkirby.com
 * @copyright Bastian Allgeier GmbH
 * @license   https://getkirby.com/license
 */
class EmailChallenge extends Challenge
{
    /**
     * Generates a random one-time auth code and returns that code
     * for later verification
     *
     * @param \Kirby\Cms\User $user User to generate the code for
     * @param array $options Details of the challenge request:
     *                       - 'mode': Purpose of the code ('login' or 'reset')
     *                       - 'timeout': Number of seconds the code will be valid for
     * @return string The generated and sent code
     */
    public static function create(User $user, array $options): string
    {
        $code = Str::random(6, 'num');

        // insert a space in the middle for easier readability
        $formatted = substr($code, 0, 3) . ' ' . substr($code, 3, 3);

        $kirby = $user->kirby();
        $kirby->email([
            'from' => $kirby->option('panel.login.email.from', 'noreply@' . $kirby->request()->url()->host()),
            'fromName' => $kirby->option('panel.login.email.fromName', $kirby->site()->title()),
            'to' => $user,
            'subject' => $kirby->option(
                'panel.login.email.subject',
                I18n::translate('login.email.' . $options['mode'] . '.subject')
            ),
            'template' => 'auth.challenge.' . $options['mode'],
            'data' => [
                'user'    => $user,
                'code'    => $formatted,
                'timeout' => round($options['timeout'] / 60)
            ]
        ]);

        return $code;
    }
}
