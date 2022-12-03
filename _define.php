<?php
/**
 * @brief dcLog, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugin
 *
 * @author Tomtom (http://blog.zenstyle.fr) and Contributors
 *
 * @copyright Jean-Christian Denis
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
if (!defined('DC_RC_PATH')) {
    return null;
}

$this->registerModule(
    'dcLog',
    'Displays Dotclear logs',
    'Tomtom (http://blog.zenstyle.fr) and Contributors',
    '1.1',
    [
        'requires'    => [['core', '2.24']],
        'permissions' => null,
        'type'        => 'plugin',
        'support'     => 'https://github.com/JcDenis/dcLog',
        'details'     => 'https://plugins.dotaddict.org/dc2/details/dcLog',
        'repository'  => 'https://raw.githubusercontent.com/JcDenis/dcLog/master/dcstore.xml',
    ]
);
