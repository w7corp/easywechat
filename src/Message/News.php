<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * News.php.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\Message;

/**
 * Class News.
 */
class News extends AbstractMessage
{
    /**
     * Message type.
     *
     * @var string
     */
    protected $type = 'news';

    /**
     * Properties.
     *
     * @var array
     */
    protected $properties = [
                                'title',
                                'description',
                                'url',
                                'image',
                            ];
    /**
     * Aliases of attribute.
     *
     * @var array
     */
    protected $aliases = [
        'image' => 'pic_url',
    ];
    
    /**
     * News constructor.
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        if(!empty($items)){
            if(!is_array(current($items))){
                //single item
                parent::__construct($items);
            }else{
                //multiple items
                foreach($items as $item){
                    if(!empty($item)){
                        array_push($this->items,new News($item));
                    }
                }
            }
        }
    }
}
