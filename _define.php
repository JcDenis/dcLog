<?php
/**
 * @file
 * @brief       The plugin dcLog definition
 * @ingroup     dcLog
 *
 * @defgroup    dcLog Plugin dcLog.
 *
 * Displays Dotclear logs.
 *
 * @author      Tomtom (author)
 * @author      Jean-Christian Denis (latest)
 * @copyright   GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

$this->registerModule(
    "Dotclear's logs",
    'Displays Dotclear logs',
    'Tomtom and Contributors',
    '1.8',
    [
        'requires'    => [['core', '2.36']],
        'permissions' => null,
        'type'        => 'plugin',
        'support'     => 'https://github.com/JcDenis/' . $this->id . '/issues',
        'details'     => 'https://github.com/JcDenis/' . $this->id . '/',
        'repository'  => 'https://raw.githubusercontent.com/JcDenis/' . $this->id . '/master/dcstore.xml',
        'date'        => '2025-08-20T12:14:42+00:00',
    ]
);
