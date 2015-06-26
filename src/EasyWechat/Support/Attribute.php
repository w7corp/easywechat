<?php

/**
 *Attributes.php.
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

namespace EasyWeChat\Support;

use InvalidArgumentException;

/**
 * Class Attributes.
 */
abstract class Attribute extends Collection
{
    /**
     * Constructor.
     *
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * Set attribute.
     *
     * @param string $attribute
     * @param string $value
     *
     * @return Attributes
     */
    public function setAttribute($attribute, $value)
    {
        return $this->add($attribute, $value);
    }

    /**
     * Set attribute.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return Attributes
     */
    public function with($attribute, $value)
    {
        $attribute = Str::snake($attribute);

        if (!$this->validate($attribute, $value)) {
            throw new InvalidArgumentException("Invalid attribute '{$attribute}'.");
        }

        $this->set($attribute, $value);

        return $this;
    }

    /**
     * Attribute validation.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    protected function validate($attribute, $value)
    {
        return true;
    }

    /**
     * Magic call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return Attributes
     */
    public function __call($method, $args)
    {
        if (stripos($method, 'with') === 0) {
            $method = substr($method, 4);
        }

        return $this->with($method, array_shift($args));
    }

    /**
     * Magic set.
     *
     * @param string $property
     * @param mixed  $value
     *
     * @return Attributes
     */
    public function __set($property, $value)
    {
        return $this->with($property, $value);
    }
}//end class

