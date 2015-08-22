<?php

use EasyWeChat\Message\Article;
use EasyWeChat\Message\Articles;

class MessageArticlesTest extends TestCase
{
    /**
     * Test item()
     */
    public function testItem()
    {
        $articles = new Articles();

        $itemFoo = new Article(['title' => 'foo']);
        $articles->item($itemFoo);
        $this->assertEquals([$itemFoo], $articles->getItems());

        $itemBar = new Article(['title' => 'bar']);
        $articles->item($itemBar);
        $this->assertEquals([$itemFoo, $itemBar], $articles->getItems());
    }

    /**
     * Test items()
     */
    public function testItems()
    {
        $articles = new Articles();

        // array
        $arrayItems = [new Article(['title' => 'foo']), new Article(['title' => 'bar'])];
        $articles->items($arrayItems);
        $this->assertEquals($arrayItems, $articles->getItems());

        // closure
        $articles->clean();
        $articles->items(function() use ($arrayItems) {
            return $arrayItems;
        });
        $this->assertEquals($arrayItems, $articles->getItems());
    }
}