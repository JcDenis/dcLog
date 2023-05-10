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
use dcNsProcess;
use dcPage;
use Dotclear\Helper\Html\Form\{
    Div,
    Form,
    Hidden,
    Para,
    Submit,
    Text
};
use Exception;

/**
 * Manage logs list
 */
class Manage extends dcNsProcess
{
    public static function init(): bool
    {
        static::$init = defined('DC_CONTEXT_ADMIN')
            && !is_null(dcCore::app()->auth)
            && dcCore::app()->auth->isSuperAdmin();

        return static::$init;
    }

    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        $current = ManageVars::init();

        // Delete logs
        if ($current->selected_logs && !empty($current->entries) || $current->all_logs) {
            try {
                dcCore::app()->log->delLogs($current->entries, $current->all_logs);
                dcPage::addSuccessNotice(
                    $current->all_logs ?
                    __('All logs have been successfully deleted') :
                    __('Selected logs have been successfully deleted')
                );
                dcCore::app()->adminurl?->redirect('admin.plugin.' . My::id());
            } catch (Exception $e) {
                dcCore::app()->error->add($e->getMessage());
            }
        }

        return true;
    }

    public static function render(): void
    {
        if (!static::$init) {
            return;
        }

        $current = ManageVars::init();

        dcPage::openModule(
            My::name(),
            dcPage::jsJson('dcLog_msg', [
                'confirm_delete_selected_log' => __('Are you sure you want to delete selected logs?'),
                'confirm_delete_all_log'      => __('Are you sure you want to delete all logs?'),
            ]) .
            $current->filter->js((string) dcCore::app()->adminurl?->get('admin.plugin.' . My::id())) .
            dcPage::jsLoad(dcPage::getPF(My::id() . '/js/backend.js'))
        );

        echo
        dcPage::breadcrumb(
            [
                __('System') => '',
                My::name()   => dcCore::app()->adminurl?->get('admin.plugin.' . My::id()),
            ]
        ) .
        dcPage::notices();

        if ($current->logs !== null && $current->list != null) {
            if ($current->logs->isEmpty() && !$current->filter->show()) {
                echo
                (new Text('p', __('There are no logs')))
                    ->render();
            } else {
                $current->filter->display(
                    'admin.plugin.' . My::id(),
                    (new Hidden(['p'], My::id()))
                        ->render()
                );
                $current->list->display(
                    is_numeric($current->filter->__get('page')) ? (int) $current->filter->__get('page') : 1,
                    is_numeric($current->filter->__get('nb')) ? (int) $current->filter->__get('nb') : 10,
                    (new Form('dcLog_form'))
                        ->action(dcCore::app()->adminurl?->get('admin.plugin.' . My::id()))
                        ->method('post')
                        ->fields([
                            (new Text('', '%s')),
                            (new Div())
                                ->class('two-cols')
                                ->items([
                                    (new Para())
                                        ->class('col checkboxes-helpers'),
                                    (new Para())
                                        ->class('col right')
                                        ->separator('&nbsp;')
                                        ->items([
                                            (new Submit('selected_logs'))
                                                ->class('delete')
                                                ->value(__('Delete selected logs')),
                                            (new Submit('all_logs'))
                                                ->class('delete')
                                                ->value(__('Delete all logs')),
                                        ]),
                                        (new Text(
                                            '',
                                            dcCore::app()->adminurl?->getHiddenFormFields('admin.plugin.' . My::id(), $current->filter->values()) .
                                            dcCore::app()->formNonce()
                                        )),
                                ]),
                        ])->render(),
                    $current->filter->show()
                );
            }
        }

        dcPage::closeModule();
    }
}
