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
use dcRecord;
use Exception;

class ManageVars
{
    /**
     * @var ManageVars self instance
     */
    private static $container;

    public readonly adminGenericFilterV2 $filter;
    public readonly ?dcRecord $logs;
    public readonly ?BackendList $list;
    public readonly array $entries;
    public readonly bool $selected_logs;
    public readonly bool $all_logs;

    protected function __construct()
    {
        $this->entries       = !empty($_POST['entries']) && is_array($_POST['entries']) ? $_POST['entries'] : [];
        $this->all_logs      = isset($_POST['all_logs']);
        $this->selected_logs = isset($_POST['selected_logs']);

        $this->filter = new adminGenericFilterV2('dcloglist');
        $this->filter->add(dcAdminFilters::getPageFilter());
        $this->filter->add(dcAdminFilters::getInputFilter('blog_id', __('Blog:')));
        $this->filter->add(dcAdminFilters::getInputFilter('user_id', __('User:')));
        $this->filter->add(dcAdminFilters::getInputFilter('log_table', __('Component:')));
        $this->filter->add(dcAdminFilters::getInputFilter('log_ip', __('IP:')));
        $params = $this->filter->params();

        try {
            $this->logs = dcCore::app()->log->getLogs($params);
            $count      = (int) dcCore::app()->log->getLogs($params, true)->f(0);
            $this->list = new BackendList($this->logs, $count);
        } catch (Exception $e) {
            dcCore::app()->error->add($e->getMessage());
        }
    }

    public static function init(): ManageVars
    {
        if (!(self::$container instanceof self)) {
            self::$container = new self();
        }

        return self::$container;
    }
}
