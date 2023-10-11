<?php

declare(strict_types=1);

namespace Dotclear\Plugin\dcLog;

use Dotclear\App;
use Dotclear\Module\MyPlugin;

/**
 * @brief   dcLog My helper.
 * @ingroup dcLog
 *
 * @author      Tomtom (author)
 * @author      Jean-Christian Denis (latest)
 * @copyright   GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
class My extends MyPlugin
{
    /**
     * Admin list ID.
     *
     * @var     string  BACKEND_LIST_ID
     */
    public const BACKEND_LIST_ID = 'dcloglist';

    public static function checkCustomContext(int $context): ?bool
    {
        // Limit module to super admin
        return App::auth()->isSuperAdmin();
    }
}
