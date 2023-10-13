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
    '1.7.2',
    [
        'requires'    => [['core', '2.28']],
        'permissions' => null,
        'type'        => 'plugin',
        'support'     => 'https://git.dotclear.watch/JcDenis/' . basename(__DIR__) . '/issues',
        'details'     => 'https://git.dotclear.watch/JcDenis/' . basename(__DIR__) . '/src/branch/master/README.md',
        'repository'  => 'https://git.dotclear.watch/JcDenis/' . basename(__DIR__) . '/raw/branch/master/dcstore.xml',
    ]
);
