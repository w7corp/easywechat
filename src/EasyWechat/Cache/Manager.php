<?php
/**
 * Manager.php
 *
 * Part of EasyWeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace EasyWeChat\Cache;

use EasyWeChat\Cache\Adapters\AdapterInterface;
use EasyWeChat\Cache\Adapters\FileAdapter;


/**
 * Class Manager
 * 
 * @package EasyWeChat\Cache
 */
class Manager 
{
    /**
     * Cache adapter.
     *
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * App id.
     *
     * @var string
     */
    protected $appId;

    /**
     * Constructor.
     *
     * @param string $appId
     */
    public function __construct($appId)
    {
        $this->appId;
    }

    /**
     * Set cache adapter.
     *
     * @param AdapterInterface $adapter
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Return current cache adapter.
     *
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter ?  $this->adapter : $this->adapter = $this->makeDefaultAdapter();
    }

    /**
     * Return default cache adapter instance.
     *
     * @return FileAdapter
     */
    protected function makeDefaultAdapter()
    {
        $adapter = new FileAdapter();

        $adapter->setAppId($this->appId);

        return $adapter;
    }

    /**
     * Magic call
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array(array($this->getAdapter(), $method), $args);
    }
}//end class
