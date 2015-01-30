<?php namespace Overtrue\Wechat\Messages;

use Overtrue\Wechat\Media;

class News extends AbstractMessage implements MessageInterface {

    protected $properties = array('items');

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

    public function formatToClient() {

    }

    public function formatToServer() {

    }

}