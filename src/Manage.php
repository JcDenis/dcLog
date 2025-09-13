<?php

declare(strict_types=1);

namespace Dotclear\Plugin\dcLog;

use Dotclear\App;
use Dotclear\Helper\Process\TraitProcess;
use Dotclear\Core\Backend\Notices;
use Dotclear\Core\Backend\Page;
use Dotclear\Helper\Html\Form\Div;
use Dotclear\Helper\Html\Form\Form;
use Dotclear\Helper\Html\Form\Hidden;
use Dotclear\Helper\Html\Form\Para;
use Dotclear\Helper\Html\Form\Submit;
use Dotclear\Helper\Html\Form\Text;
use Exception;

/**
 * @brief       dcLog manage class.
 * @ingroup     dcLog
 *
 * @author      Tomtom (author)
 * @author      Jean-Christian Denis (latest)
 * @copyright   GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
class Manage
{
    use TraitProcess;

    public static function init(): bool
    {
        return self::status(My::checkContext(My::MANAGE));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        $current = ManageVars::init();

        // Delete logs
        if ($current->selected_logs && !empty($current->entries) || $current->all_logs) {
            try {
                if ($current->all_logs) {
                    App::log()->delAllLogs();
                } else {
                    App::log()->delLogs($current->entries);
                }
                Notices::addSuccessNotice(
                    $current->all_logs ?
                    __('All logs have been successfully deleted') :
                    __('Selected logs have been successfully deleted')
                );
                My::redirect();
            } catch (Exception $e) {
                App::error()->add($e->getMessage());
            }
        }

        return true;
    }

    public static function render(): void
    {
        if (!self::status()) {
            return;
        }

        $current = ManageVars::init();

        Page::openModule(
            My::name(),
            Page::jsJson('dcLog_msg', [
                'confirm_delete_selected_log' => __('Are you sure you want to delete selected logs?'),
                'confirm_delete_all_log'      => __('Are you sure you want to delete all logs?'),
            ]) .
            $current->filter->js((string) My::manageUrl([], '&')) .
            My::jsLoad('backend')
        );

        echo
        Page::breadcrumb(
            [
                __('System') => '',
                My::name()   => My::manageUrl(),
            ]
        ) .
        Notices::getNotices();

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
                        ->action(My::manageUrl())
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
                                    ... My::hiddenFields($current->filter->values()),
                                ]),
                        ])->render(),
                    $current->filter->show()
                );
            }
        }

        Page::closeModule();
    }
}
