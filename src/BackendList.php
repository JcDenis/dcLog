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
use adminGenericListV2;
use dcPager;
use dt;
use form;
use html;

class BackendList extends adminGenericListV2
{
    public function display(int $page, int $nb_per_page, string $enclose_block = '', bool $filter = false): void
    {
        if ($this->rs->isEmpty()) {
            echo $filter ?
                '<p><strong>' . __('No log matches the filter') . '</strong></p>' :
                '<p><strong>' . __('No log') . '</strong></p>';
        } else {
            $pager   = new dcPager($page, $this->rs_count, $nb_per_page, 10);
            $entries = [];
            if (isset($_REQUEST['entries'])) {
                foreach ($_REQUEST['entries'] as $v) {
                    $entries[(int) $v] = true;
                }
            }

            $cols = [
                'date'  => '<th colspan="2" class="first">' . __('Date') . '</th>',
                'msg'   => '<th scope="col">' . __('Message') . '</th>',
                'blog'  => '<th scope="col">' . __('Blog') . '</th>',
                'table' => '<th scope="col">' . __('Component') . '</th>',
                'user'  => '<th scope="col">' . __('User') . '</th>',
                'ip'    => '<th scope="col">' . __('IP') . '</th>',
            ];
            $cols = new ArrayObject($cols);
            $this->userColumns(My::BACKEND_LIST_ID, $cols);

            $html_block = '<div class="table-outer"><table><caption>' .
                (
                    $filter ?
                    sprintf(__('List of %s logs matching the filter.'), $this->rs_count) :
                    sprintf(__('List of logs. (%s)'), $this->rs_count)
                ) .
                '</caption><tr>' . implode(iterator_to_array($cols)) . '</tr>%s</table>%s</div>';

            if ($enclose_block) {
                $html_block = sprintf($enclose_block, $html_block);
            }

            $blocks = explode('%s', $html_block);

            echo $pager->getLinks() . $blocks[0];

            while ($this->rs->fetch()) {
                $this->logLine(isset($entries[$this->rs->log_id]));
            }

            echo $blocks[1] . $blocks[2] . $pager->getLinks();
        }
    }

    private function logLine(bool $checked): void
    {
        $cols = [
            'check' => '<td class="nowrap minimal">' .
                form::checkbox(['entries[]'], $this->rs->log_id, ['checked' => $checked]) .
                '</td>',
            'date' => '<td class="nowrap minimal">' .
                html::escapeHTML(dt::dt2str(
                    __('%Y-%m-%d %H:%M'),
                    $this->rs->log_dt
                )) .
                '</td>',
            'msg' => '<td class="maximal">' .
                nl2br(html::escapeHTML($this->rs->log_msg)) .
                '</td>',
            'blog' => '<td class="minimal nowrap">' .
                html::escapeHTML($this->rs->blog_id) .
                '</td>',
            'table' => '<td class="minimal nowrap">' .
                html::escapeHTML($this->rs->log_table) .
                '</td>',
            'user' => '<td class="minimal nowrap">' .
                html::escapeHTML($this->rs->getUserCN()) .
                '</td>',
            'ip' => '<td class="minimal nowrap">' .
                html::escapeHTML($this->rs->log_ip) .
                '</td>',
        ];
        $cols = new ArrayObject($cols);
        $this->userColumns(My::BACKEND_LIST_ID, $cols);

        echo
            '<tr class="line" id="p' . $this->rs->log_id . '">' .
            implode(iterator_to_array($cols)) .
            '</tr>';
    }
}
