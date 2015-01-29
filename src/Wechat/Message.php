<?php namespace Overtrue\Wechat;

use Closure;
use InvalidArgumentException;

class Message {

    const TEXT  = 'text';
    const IMAGE = 'image';
    const VOICE = 'voice';
    const VIDEO = 'video';
    const MUSIC = 'music';
    const NEWS  = 'news';

    protected $attributes = array();

    /**
     * 添加图文消息内容
     *
     * @return void
     */
    public function item()
    {
        $args    = func_get_args();
        $argsLen = func_num_args();

        if ($argsLen && $args[0] instanceof Closure) {
            return $args($this);
        }

        if ($argsLen < 3) {
            throw new InvalidArgumentException("item方法要求至少3个参数：标题，描述，图片");
        }

        list($title, $description, $image, $url = '') = $args;

        $item = array(
            'Title'       => $title,
            'Description' => $description,
            'PicUrl'      => $image,
            'Url'         => $url,
        );

        !empty($this->attributes['items']) || $this->attributes['items'] = array();

        array_push($this->attributes['items'], $item);
    }

    /**
     * 调用不存在的方法
     *
     * @param string $method
     * @param array  $args
     *
     * @return void
     */
    public function __call($method, $args)
    {
        $value = array_shift($args);

        if (!is_scalar($value)) {
            throw new InvalidArgumentException("属性值只能为标量");
        }

        $this->attributes[$method] = $value;

        return $this;
    }

    /**
     * 设置接收者
     *
     * @param string|array $openId
     *
     * @return Overtrue\Wechat\Message
     */
    public function to($openId)
    {
        # code...
    }

    public function toGroup($groupId)
    {
        # code...
    }

    public function toAll()
    {
        # code...
    }

    public function build()
    {
        return $this->attributes;
    }
}