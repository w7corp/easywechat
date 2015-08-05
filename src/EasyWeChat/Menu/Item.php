<?php

/**
 * Item.php.
 *
 * Part of EasyWeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace EasyWeChat\Menu;

use EasyWeChat\Core\Exceptions\InvalidArgumentException;
use EasyWeChat\Support\Attribute;
use Closure;

/**
 * Class Item.
 */
class Item extends Attribute
{
    /**
     * Constructor.
     *
     * @param string $name
     * @param string $type
     * @param string $property
     */
    public function __construct($name, $type = null, $property = null)
    {
        $this->with('name', $name);

        $type !== null && $this->with('type', $type);

        if ($property !== null) {
            switch ($type) {
                case 'view':
                    $key = 'url';
                    break;
                case 'media_id':
                case 'view_limited':
                    $key = 'media_id';
                    break;
                default:
                    $key = 'key';
            }

            $this->with($key, $property);
        }
    }

    /**
     * Set sub buttons.
     *
     * @param array $buttons
     *
     * @return Item
     *
     * @throws InvalidArgumentException
     */
    public function buttons($buttons)
    {
        if ($buttons instanceof Closure) {
            $buttons = $buttons($this);
        }

        foreach ((array) $buttons as $button) {
            $this->button($button);
        }

        return $this;
    }

    /**
     * Add a sub button.
     *
     * @param Item|array $button
     *
     * @throws InvalidArgumentException
     */
    public function button($button)
    {
        $subButtons = $this->get('sub_button', []);

        if ((!is_array($button) && !$button instanceof static) || empty($button)) {
            throw new InvalidArgumentException('button must be an Item or an array.');
        }

        if (is_array($button)) {
            $button = array_pad($button, 3, null);
            $button = new static($button[0], $button[1], $button[2]);
        }

        $subButtons[] = $button->all();

        $this->with('sub_button', $subButtons);
    }
}
