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

use adminGenericFilterV2;
use dcAdminFilters;
use dcCore;
use dcNsProcess;
use dcPage;
use Exception;
use form;

/**
 * Manage logs list
 */
class Manage extends dcNsProcess
{
    public static function init(): bool
    {
        static::$init = defined('DC_CONTEXT_ADMIN')
            && dcCore::app()->auth?->isSuperAdmin()
            && My::phpCompliant();

        return static::$init;
    }

    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        dcCore::app()->admin->logs = dcCore::app()->admin->logs_list = dcCore::app()->admin->logs_filter = null;

        $entries     = $_POST['entries'] ?? null;
        $del_all_log = isset($_POST['del_all_logs']) ? true : false;

        #  Delete logs
        if (isset($_POST['del_logs']) || isset($_POST['del_all_logs'])) {
            try {
                dcCore::app()->log->delLogs($entries, $del_all_log);
                dcPage::addSuccessNotice(
                    $del_all_log ?
                    __('All logs have been successfully deleted') :
                    __('Selected logs have been successfully deleted')
                );
                dcCore::app()->adminurl->redirect('admin.plugin.' . My::id());
            } catch (Exception $e) {
                dcCore::app()->error->add($e->getMessage());
            }
        }

        dcCore::app()->admin->logs_filter = new adminGenericFilterV2('dcloglist');
        dcCore::app()->admin->logs_filter->add(dcAdminFilters::getPageFilter());
        dcCore::app()->admin->logs_filter->add(dcAdminFilters::getInputFilter('blog_id', __('Blog:')));
        dcCore::app()->admin->logs_filter->add(dcAdminFilters::getInputFilter('user_id', __('User:')));
        dcCore::app()->admin->logs_filter->add(dcAdminFilters::getInputFilter('log_table', __('Component:')));
        dcCore::app()->admin->logs_filter->add(dcAdminFilters::getInputFilter('log_ip', __('IP:')));
        $params = dcCore::app()->admin->logs_filter->params();

        try {
            dcCore::app()->admin->logs      = dcCore::app()->log->getLogs($params);
            dcCore::app()->admin->logs_list = new BackendList(dcCore::app()->admin->logs, dcCore::app()->admin->logs->count());
        } catch (Exception $e) {
            dcCore::app()->error->add($e->getMessage());
        }

        return true;
    }

    public static function render(): void
    {
        if (!static::$init) {
            return;
        }

        dcPage::openModule(
            __('Pings'),
            dcPage::jsJson('dclog_list', [
                'confirm_delete_selected_log' => __('Are you sure you want to delete selected logs?'),
                'confirm_delete_all_log'      => __('Are you sure you want to delete all logs?'),
            ]) .
            dcCore::app()->admin->logs_filter->js(dcCore::app()->adminurl->get('admin.plugin.' . My::id())) .
            dcPage::jsLoad(dcPage::getPF(My::id() . '/js/backend.js'))
        );

        echo
        dcPage::breadcrumb(
            [
                __('System') => '',
                My::name()   => dcCore::app()->adminurl->get('admin.plugin.' . My::id()),
            ]
        ) .
        dcPage::notices();

        if (isset(dcCore::app()->admin->logs) && isset(dcCore::app()->admin->logs_list)) {
            if (dcCore::app()->admin->logs->isEmpty() && !dcCore::app()->admin->logs_filter->show()) {
                echo '<p>' . __('There are no logs') . '</p>';
            } else {
                dcCore::app()->admin->logs_filter->display(
                    'admin.plugin.' . My::id(),
                    form::hidden('p', My::id())
                );
                dcCore::app()->admin->logs_list->display(
                    dcCore::app()->admin->logs_filter->__get('page'),
                    dcCore::app()->admin->logs_filter->__get('nb'),
                    '<form action="' . dcCore::app()->adminurl->get('admin.plugin.' . My::id()) . '" method="post" id="form-entries">' .

                    '%s' .

                    '<div class="two-cols">' .
                    '<p class="col checkboxes-helpers"></p>' .

                    '<p class="col right">' .
                    '<input type="submit" value="' . __('Delete selected logs') . '" name="del_logs" />&nbsp;' .
                    '<input type="submit" value="' . __('Delete all logs') . '" name="del_all_logs" />' .
                    '</p>' .

                    dcCore::app()->adminurl->getHiddenFormFields('admin.plugin.' . My::id(), dcCore::app()->admin->logs_filter->values()) .
                    dcCore::app()->formNonce() .
                    '</div>' .
                    '</form>',
                    dcCore::app()->admin->logs_filter->show()
                );
            }
        }

        dcPage::closeModule();
    }
}
