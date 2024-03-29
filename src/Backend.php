<?php

declare(strict_types=1);

namespace Dotclear\Plugin\dcLog;

use ArrayObject;
use Dotclear\App;
use Dotclear\Core\Process;
use Dotclear\Core\Backend\Favorites;

/**
 * @brief       dcLog backend class.
 * @ingroup     dcLog
 *
 * @author      Tomtom (author)
 * @author      Jean-Christian Denis (latest)
 * @copyright   GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
class Backend extends Process
{
    public static function init(): bool
    {
        return self::status(My::checkContext(My::BACKEND));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        My::addBackendMenuItem(App::backend()->menus()::MENU_SYSTEM);

        App::behavior()->addBehaviors([
            // backend user preference for logs list columns
            'adminColumnsListsV2' => function (ArrayObject $cols): void {
                $cols[My::BACKEND_LIST_ID] = [
                    My::name(),
                    [
                        'date' => [true, __('Date')],
                        //'msg'    => [true, __('Message')],
                        'blog'  => [true, __('Blog')],
                        'table' => [true, __('Component')],
                        'user'  => [true, __('User')],
                        'ip'    => [false, __('IP')],
                    ],
                ];
            },
            // backend filter for logs list sort
            'adminFiltersListsV2' => function (ArrayObject $sorts): void {
                $sorts[My::BACKEND_LIST_ID] = [
                    My::name(),
                    [
                        __('Date')      => 'log_dt',
                        __('Message')   => 'log_msg',
                        __('Blog')      => 'blog_id',
                        __('Component') => 'log_table',
                        __('User')      => 'user_id',
                        __('IP')        => 'log_ip',
                    ],
                    'log_dt',
                    'desc',
                    [__('Logs per page'), 30],
                ];
            },
            // backend user preference for dashboard icon
            'adminDashboardFavoritesV2' => function (Favorites $favs): void {
                $favs->register(My::BACKEND_LIST_ID, [
                    'title'      => My::name(),
                    'url'        => My::manageUrl(),
                    'small-icon' => My::icons(),
                    'large-icon' => My::icons(),
                    //'permissions' => null,
                ]);
            },
        ]);

        return true;
    }
}
