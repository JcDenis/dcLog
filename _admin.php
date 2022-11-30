<?php
/**
 * @brief dcLog, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugin
 *
 * @author Tomtom (http://blog.zenstyle.fr) and Contributors
 *
 * @copyright Jean-Crhistian Denis
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
if (!defined('DC_CONTEXT_ADMIN')) {
    return null;
}

dcCore::app()->menu[dcAdmin::MENU_SYSTEM]->addItem(
    __('Log'),
    dcCore::app()->adminurl->get('admin.plugin.dcLog'),
    dcPage::getPF('dcLog/icon.svg'),
    preg_match('/' . preg_quote(dcCore::app()->adminurl->get('admin.plugin.dcLog')) . '(&.*)?$/', $_SERVER['REQUEST_URI']),
    dcCore::app()->auth->isSuperAdmin()
);

dcCore::app()->addBehavior('adminColumnsListsV2', function (ArrayObject $cols) {
    $cols['dcloglist'] = [
        __('Log'),
        [
            'date' => [true, __('Date')],
            //'msg'    => [true, __('Message')],
            'blog'  => [true, __('Blog')],
            'table' => [true, __('Component')],
            'user'  => [true, __('User')],
            'ip'    => [false, __('IP')],
        ],
    ];
});

dcCore::app()->addBehavior('adminFiltersListsV2', function (ArrayObject $sorts) {
    $sorts['dcloglist'] = [
        __('Log'),
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
});

dcCore::app()->addBehavior('adminDashboardFavoritesV2', function (dcFavorites $favs) {
    $favs->register('dcloglist', [
        'title'       => __('Log'),
        'url'         => dcCore::app()->adminurl->get('admin.plugin.dcLog'),
        'small-icon'  => dcPage::getPF('dcLog/icon.svg'),
        'large-icon'  => dcPage::getPF('dcLog/icon.svg'),
        //'permissions' => null,
    ]);
});
