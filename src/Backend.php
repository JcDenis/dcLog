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

use ArrayObject;
use dcAdmin;
use dcCore;
use dcFavorites;
use dcMenu;
use dcNsProcess;
use dcPage;

class Backend extends dcNsProcess
{
    public static function init(): bool
    {
        static::$init = defined('DC_CONTEXT_ADMIN')
            && My::phpCompliant()
            && !is_null(dcCore::app()->auth)
            && dcCore::app()->auth->isSuperAdmin();

        return static::$init;
    }

    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        // backend sidebar menu icon
        if (!is_null(dcCore::app()->auth)
            && !is_null(dcCore::app()->adminurl)
            && (dcCore::app()->menu[dcAdmin::MENU_SYSTEM] instanceof dcMenu)
        ) {
            dcCore::app()->menu[dcAdmin::MENU_SYSTEM]->addItem(
                My::name(),
                dcCore::app()->adminurl->get('admin.plugin.' . My::id()),
                dcPage::getPF(My::id() . '/icon.svg'),
                preg_match('/' . preg_quote((string) dcCore::app()->adminurl->get('admin.plugin.' . My::id())) . '(&.*)?$/', $_SERVER['REQUEST_URI']),
                dcCore::app()->auth->isSuperAdmin()
            );
        }

        dcCore::app()->addBehaviors([
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
            'adminDashboardFavoritesV2' => function (dcFavorites $favs): void {
                $favs->register(My::BACKEND_LIST_ID, [
                    'title'      => My::name(),
                    'url'        => dcCore::app()->adminurl?->get('admin.plugin.' . My::id()),
                    'small-icon' => dcPage::getPF(My::id() . '/icon.svg'),
                    'large-icon' => dcPage::getPF(My::id() . '/icon.svg'),
                    //'permissions' => null,
                ]);
            },
        ]);

        return true;
    }
}
