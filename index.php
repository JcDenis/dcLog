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
if (!defined('DC_CONTEXT_ADMIN')) {
    return null;
}

if (!dcCore::app()->auth->isSuperAdmin()) {
    return null;
}

$entries     = $_POST['entries'] ?? null;
$del_all_log = isset($_POST['del_all_logs']) ? true : false;

#  Delete logs
if (isset($_POST['del_logs']) || isset($_POST['del_all_logs'])) {
    try {
        dcCore::app()->log->delLogs($entries, $del_all_log);
        dcAdminNotices::addSuccessNotice(
            $del_all_log ?
            __('All logs have been successfully deleted') :
            __('Selected logs have been successfully deleted')
        );
        dcCore::app()->adminurl->redirect('admin.plugin.' . basename(__DIR__));
    } catch (Exception $e) {
        dcCore::app()->error->add($e->getMessage());
    }
}

$filter = new adminGenericFilterV2('dcloglist');
$filter->add(dcAdminFilters::getPageFilter());
$filter->add(dcAdminFilters::getInputFilter('blog_id', __('Blog:')));
$filter->add(dcAdminFilters::getInputFilter('user_id', __('User:')));
$filter->add(dcAdminFilters::getInputFilter('log_table', __('Component:')));
$filter->add(dcAdminFilters::getInputFilter('log_ip', __('IP:')));
$params = $filter->params();

try {
    $logs         = dcCore::app()->log->getLogs($params);
    $logs_counter = $logs->count();
    $logs_list    = new dcLogList($logs, $logs_counter);
} catch (Exception $e) {
    dcCore::app()->error->add($e->getMessage());
}

echo
'<html><head><title>' . __('Log') . '</title>' .
dcPage::jsJson('dclog_list', [
    'confirm_delete_selected_log' => __('Are you sure you want to delete selected logs?'),
    'confirm_delete_all_log'      => __('Are you sure you want to delete all logs?'),
]) .
$filter->js(dcCore::app()->adminurl->get('admin.plugin.' . basename(__DIR__))) .
dcPage::jsLoad(dcPage::getPF(basename(__DIR__) . '/js/dclog.js')) .
'</head><body>' .
dcPage::breadcrumb([
    __('System') => '',
    __('Log')    => dcCore::app()->adminurl->get('admin.plugin.' . basename(__DIR__)),
]) .
dcPage::notices();

if (isset($logs) && isset($logs_list)) {
    if ($logs->isEmpty() && !$filter->show()) {
        echo '<p>' . __('There are no logs') . '</p>';
    } else {
        $filter->display(
            'admin.plugin.' . basename(__DIR__),
            form::hidden('p', basename(__DIR__))
        );
        $logs_list->display(
            $filter->__get('page'),
            $filter->__get('nb'),
            '<form action="' . dcCore::app()->adminurl->get('admin.plugin.' . basename(__DIR__)) . '" method="post" id="form-entries">' .

            '%s' .

            '<div class="two-cols">' .
            '<p class="col checkboxes-helpers"></p>' .

            '<p class="col right">' .
            '<input type="submit" value="' . __('Delete selected logs') . '" name="del_logs" />&nbsp;' .
            '<input type="submit" value="' . __('Delete all logs') . '" name="del_all_logs" />' .
            '</p>' .

            dcCore::app()->adminurl->getHiddenFormFields('admin.plugin.' . basename(__DIR__), $filter->values()) .
            dcCore::app()->formNonce() .
            '</div>' .
            '</form>',
            $filter->show()
        );
    }
}

echo '</body></html>';
