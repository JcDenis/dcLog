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
 * This module definition.
 */
class My
{
    /** @var    string  Admin list ID */
    public const BACKEND_LIST_ID = 'dcloglist';

    /** @var    string  This module required php version */
    public const PHP_MIN = '8.1';

    /**
     * This module id.
     */
    public static function id(): string
    {
        return basename(dirname(__DIR__));
    }

    /**
     * This module name.
     */
    public static function name(): string
    {
        $name = dcCore::app()->plugins->moduleInfo(self::id(), 'name');

        return __(is_string($name) ? $name : self::id());
    }

    /**
     * This module path.
     */
    public static function path(): string
    {
        return dirname(__DIR__);
    }

    /**
     * Check this module PHP version compliant.
     */
    public static function phpCompliant(): bool
    {
        return version_compare(phpversion(), self::PHP_MIN, '>=');
    }
}
