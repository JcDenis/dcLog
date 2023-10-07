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

use Dotclear\App;
use Dotclear\Core\Backend\Filter\{
    Filters,
    FiltersLibrary
};
use Dotclear\Database\MetaRecord;
use Exception;

/**
 * Backend logs manage page vars container.
 */
class ManageVars
{
    /** @var    ManageVars  $container  self instance */
    private static $container;

    /** @var    Filters     $filter     The filter instance */
    public readonly Filters $filter;

    /** @var    null|MetaRecord     $logs   The current records */
    public readonly ?MetaRecord $logs;

    /** @var    null|BackendList    $list   The records list form instance */
    public readonly ?BackendList $list;

    /** @var    array<int,string>   $entries    The post form selected entries */
    public readonly array $entries;

    /** @var    bool    $selected_logs  The post form action */
    public readonly bool $selected_logs;

    /** @var    bool    $all_logs  The post form action */
    public readonly bool $all_logs;

    /**
     * Constructor grabs post form value and sets properties.
     */
    protected function __construct()
    {
        $this->entries       = !empty($_POST['entries']) && is_array($_POST['entries']) ? $_POST['entries'] : [];
        $this->all_logs      = isset($_POST['all_logs']);
        $this->selected_logs = isset($_POST['selected_logs']);

        $this->filter = new Filters('dcloglist');
        $this->filter->add(FiltersLibrary::getPageFilter());
        $this->filter->add(FiltersLibrary::getInputFilter('blog_id', __('Blog:')));
        $this->filter->add(FiltersLibrary::getInputFilter('user_id', __('User:')));
        $this->filter->add(FiltersLibrary::getInputFilter('log_table', __('Component:')));
        $this->filter->add(FiltersLibrary::getInputFilter('log_ip', __('IP:')));
        $params = $this->filter->params();

        try {
            $this->logs = App::log()->getLogs($params);
            $count      = App::log()->getLogs($params, true)->f(0);
            $count      = is_numeric($count) ? (int) $count : 0;
            $this->list = new BackendList($this->logs, $count);
        } catch (Exception $e) {
            App::error()->add($e->getMessage());
        }
    }

    /**
     * Get instance.
     *
     * @return  ManageVars  The instance
     */
    public static function init(): ManageVars
    {
        if (!(self::$container instanceof self)) {
            self::$container = new self();
        }

        return self::$container;
    }
}
