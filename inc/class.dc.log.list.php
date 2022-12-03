<?php
/**
 * @brief dcLog, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugin
 *
 * @author Tomtom (http://blog.zenstyle.fr) and Contributors
 *
 * @copyright Jean-Christian Denis
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
if (!defined('DC_RC_PATH')) {
    return null;
}

class dcLogList extends adminGenericListV2
{
    public function display($page, $nb_per_page, $enclose_block = '', $filter = false)
    {
        if ($this->rs->isEmpty()) {
            if ($filter) {
                echo '<p><strong>' . __('No log matches the filter') . '</strong></p>';
            } else {
                echo '<p><strong>' . __('No log') . '</strong></p>';
            }
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
            $this->userColumns('dcloglist', $cols);

            $html_block = '<div class="table-outer">' .
                '<table>';

            if ($filter) {
                $html_block .= '<caption>' . sprintf(__('List of %s logs matching the filter.'), $this->rs_count) . '</caption>';
            }

            $html_block .= '<tr>' . implode(iterator_to_array($cols)) . '</tr>%s</table>%s</div>';
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

    private function logLine($checked)
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
        $this->userColumns('dcloglist', $cols);

        echo
            '<tr class="line" id="p' . $this->rs->log_id . '">' .
            implode(iterator_to_array($cols)) .
            '</tr>';
    }
}
