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
use Dotclear\Core\Backend\Listing\{
    Listing,
    Pager
};
use Dotclear\Helper\Date;
use Dotclear\Helper\Html\Html;
use Dotclear\Helper\Html\Form\{
    Component,
    Div,
    Checkbox,
    Para,
    Text
};

/**
 * Backend logs list helper.
 */
class BackendList extends Listing
{
    /**
     * Display logs record.
     *
     * @param   int     $page           The current list page
     * @param   int     $nb_per_page    The record per page number
     * @param   string  $enclose_block  The enclose block
     * @param   bool    $filter         Filter is applied
     */
    public function display(int $page, int $nb_per_page, string $enclose_block = '%s', bool $filter = false): void
    {
        if ($this->rs->isEmpty()) {
            echo
            (new Text('p', $filter ? __('No log matches the filter') : __('No log')))
                ->class('info')
                ->render();

            return;
        }

        $pager = new Pager($page, $this->rs_count, $nb_per_page, 10);

        $cols = new ArrayObject([
            'date' => (new Text('th', __('Date')))
                ->class('first')
                ->extra('colspan="2"'),
            'msg' => (new Text('th', __('Message')))
                ->extra('scope="col"'),
            'blog' => (new Text('th', __('Blog')))
                ->extra('scope="col"'),
            'table' => (new Text('th', __('Component')))
                ->extra('scope="col"'),
            'user' => (new Text('th', __('User')))
                ->extra('scope="col"'),
            'ip' => (new Text('th', __('IP')))
                ->extra('scope="col"'),
        ]);
        $this->userColumns(My::BACKEND_LIST_ID, $cols);

        $lines = [];
        while ($this->rs->fetch()) {
            $lines[] = $this->line(isset($_POST['entries']) && in_array($this->rs->log_id, $_POST['entries']));
        }

        echo
        $pager->getLinks() .
        sprintf(
            $enclose_block,
            (new Div())
                ->class('table-outer')
                ->items([
                    (new Para(null, 'table'))
                        ->items([
                            (new Text(
                                'caption',
                                $filter ?
                                sprintf(__('List of %s logs matching the filter.'), $this->rs_count) :
                                sprintf(__('List of logs. (%s)'), $this->rs_count)
                            )),
                            (new Para(null, 'tr'))
                                ->items(iterator_to_array($cols)),
                            (new Para(null, 'tbody'))
                                ->items($lines),
                        ]),
                ])
                ->render()
        ) .
        $pager->getLinks();
    }

    /**
     * Get a records line.
     *
     * @param   bool    $checked    Selected line
     */
    private function line(bool $checked): Component
    {
        $cols = new ArrayObject([
            'check' => (new Para(null, 'td'))
                ->class('nowrap minimal')
                ->items([
                    (new Checkbox(['entries[]'], $checked))
                        ->value($this->rs->log_id),
                ]),
            'date' => (new Text('td', Html::escapeHTML(Date::dt2str(__('%Y-%m-%d %H:%M'), $this->rs->log_dt))))
                ->class('nowrap minimal'),
            'msg' => (new Text('td', nl2br(Html::escapeHTML($this->rs->log_msg))))
                ->class('maximal'),
            'blog' => (new Text('td', Html::escapeHTML($this->rs->blog_id)))
                ->class('nowrap minimal'),
            'table' => (new Text('td', Html::escapeHTML($this->rs->log_table)))
                ->class('nowrap minimal'),
            'user' => (new Text('td', Html::escapeHTML($this->rs->getUserCN())))
                ->class('nowrap minimal'),
            'ip' => (new Text('td', Html::escapeHTML($this->rs->log_ip)))
                ->class('nowrap minimal'),
        ]);
        $this->userColumns(My::BACKEND_LIST_ID, $cols);

        return
        (new Para('p' . $this->rs->log_id, 'tr'))
            ->class('line')
            ->items(iterator_to_array($cols));
    }
}
