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

use Dotclear\App;
use Dotclear\Module\MyPlugin;

class My extends MyPlugin
{
    /** @var    string  Admin list ID */
    public const BACKEND_LIST_ID = 'dcloglist';

    public static function checkCustomContext(int $context): ?bool
    {
        return App::auth()->isSuperAdmin();
    }
}
