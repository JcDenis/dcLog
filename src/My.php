<?php
/**
 * @brief dcLog, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugin
 *
 * @author Tomtom and Contributors
 *
 * @copyright Jean-Christian Denis
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

namespace Dotclear\Plugin\dcLog;

use dcCore;

/**
 * Module definition shortcut.
 */
class My
{
    /**
     * @var string PHP min version
     */
    public const PHP_MIN = '8.1';

    /**
     * @var string Admin list ID
     */
    public const BACKEND_LIST_ID = 'dcloglist';

    /**
     * This module id.
     *
     * @return  string  The module id
     */
    public static function id(): string
    {
        return basename(dirname(__DIR__));
    }

    /**
     * This module name.
     *
     * @return  string  The module translated name
     */
    public static function name(): string
    {
        $name = dcCore::app()->plugins->moduleInfo(self::id(), 'name');

        return __(is_string($name) ? $name : 'Undefined');
    }

    /**
     * Check php version.
     *
     * @return  bool    True on supported PHP version
     */
    public static function phpCompliant(): bool
    {
        return version_compare(phpversion(), self::PHP_MIN, '>=');
    }
}
