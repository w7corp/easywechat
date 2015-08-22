<?php

use EasyWeChat\Core\Http;
use EasyWeChat\Material\Material;

class MaterialMaterialTest extends TestCase
{
    /**
     * Return mock http.
     *
     * @return \Mockery\MockInterface
     */
    public function getHttp()
    {
        $http = Mockery::mock(Http::class);
        $http->shouldReceive('setExpectedException')->andReturn($http);

        return $http;
    }

    /**
     * Test for uploadImage().
     *
     * @expectedException EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public function testUploadImage()
    {
        $http = $this->getHttp();
        $http->shouldReceive('upload')->andReturnUsing(function ($url, $files, $form) {
            return [
                'url' => $url,
                'files' => $files,
                'form' => $form,
            ];
        });
        $material = new Material($http);

        $response = $material->uploadImage(__DIR__.'/stubs/image.jpg');

        $this->assertEquals(Material::API_UPLOAD, $response['url']);
        $this->assertContains('stubs/image.jpg', $response['files']['media']);

        // exception
        $response = $material->uploadImage(__DIR__.'/stubs/foo.jpg');
    }

    /**
     * Test for uploadVoice().
     */
    public function testUploadVoice()
    {
        $http = $this->getHttp();
        $http->shouldReceive('upload')->andReturnUsing(function ($url, $files, $form) {
            return [
                'url' => $url,
                'files' => $files,
                'form' => $form,
            ];
        });
        $material = new Material($http);

        $response = $material->uploadVoice(__DIR__.'/stubs/voice.wma');

        $this->assertEquals(Material::API_UPLOAD, $response['url']);
        $this->assertContains('stubs/voice.wma', $response['files']['media']);
    }

    /**
     * Test for uploadThumb().
     */
    public function testUploadThumb()
    {
        $http = $this->getHttp();
        $http->shouldReceive('upload')->andReturnUsing(function ($url, $files, $form) {
            return [
                'url' => $url,
                'files' => $files,
                'form' => $form,
            ];
        });
        $material = new Material($http);

        $response = $material->uploadThumb(__DIR__.'/stubs/thumb.png');

        $this->assertEquals(Material::API_UPLOAD, $response['url']);
        $this->assertContains('stubs/thumb.png', $response['files']['media']);
    }

    /**
     * Test for uploadVideo().
     */
    public function testUploadVideo()
    {
        $http = $this->getHttp();
        $http->shouldReceive('upload')->andReturnUsing(function ($url, $files, $form) {
            return [
                'url' => $url,
                'files' => $files,
                'form' => $form,
            ];
        });
        $material = new Material($http);

        $response = $material->uploadVideo(__DIR__.'/stubs/video.mp4', 'foo', 'a mp4 video.');

        $this->assertEquals(Material::API_UPLOAD, $response['url']);
        $this->assertContains('stubs/video.mp4', $response['files']['media']);
        $this->assertEquals(json_encode(['title' => 'foo', 'introduction' => 'a mp4 video.']), $response['form']['description']);
    }

    /**
     * Test for uploadArticle().
     */
    public function testUploadArticle()
    {
        $http = $this->getHttp();
        $http->shouldReceive('json')->andReturnUsing(function ($url, $form) {
            return [
                'media_id' => [
                    'url' => $url,
                    'form' => $form,
                ]
            ];
        });
        $material = new Material($http);

        $response = $material->uploadArticle(['foo' => 'bar']);

        $this->assertEquals(Material::API_NEWS_UPLOAD, $response['url']);
        $this->assertEquals(['articles' => ['foo' => 'bar']], $response['form']);
    }

    /**
     * Test for updateArticle().
     */
    public function testUpdateArticle()
    {
        $http = $this->getHttp();
        $http->shouldReceive('json')->andReturnUsing(function ($url, $form) {
            return [
                'url' => $url,
                'form' => $form,
            ];
        });
        $material = new Material($http);

        $response = $material->updateArticle('foo', ['title' => 'bar']);

        $this->assertEquals(Material::API_NEWS_UPDATE, $response['url']);
        $this->assertEquals('foo', $response['form']['media_id']);
        $this->assertEquals(0, $response['form']['index']);
        $this->assertEquals(['title' => 'bar'], $response['form']['articles']);

        // multi
        $response = $material->updateArticle('foo', [['title' => 'bar']]);
        $this->assertEquals(['title' => 'bar'], $response['form']['articles']);

        // invalid $article
        $response = $material->updateArticle('foo', ['abc' => 'bar']);
        $this->assertEquals([], $response['form']['articles']);
    }

    /**
     * Test for uploadArticleImage().
     */
    public function testUploadArticleImage()
    {
        $http = $this->getHttp();
        $http->shouldReceive('upload')->andReturnUsing(function ($url, $files, $form) {
            return [
                'url' => $url,
                'files' => $files,
                'form' => $form,
            ];
        });
        $material = new Material($http);

        $response = $material->uploadArticleImage(__DIR__.'/stubs/image.jpg');

        $this->assertEquals(Material::API_NEWS_IMAGE_UPLOAD, $response['url']);
        $this->assertContains('stubs/image.jpg', $response['files']['media']);
    }

    /**
     * Test for get().
     */
    public function testGet()
    {
        $http = $this->getHttp();
        $http->shouldReceive('json')->andReturnUsing(function ($url, $form) {
            return [
                    'url' => $url,
                    'form' => $form,
            ];
        });
        $material = new Material($http);

        $response = $material->get('foo');

        $this->assertEquals(Material::API_GET, $response['url']);
        $this->assertEquals(['media_id' => 'foo'], $response['form']);
    }

    /**
     * Test for delete().
     */
    public function testDelete()
    {
        $http = $this->getHttp();
        $http->shouldReceive('json')->andReturnUsing(function ($url, $form) {
            return [
                'url' => $url,
                'form' => $form,
            ];
        });
        $material = new Material($http);

        $response = $material->delete('foo');

        $this->assertEquals(Material::API_DELETE, $response['url']);
        $this->assertEquals(['media_id' => 'foo'], $response['form']);
    }

    /**
     * Test for lists().
     */
    public function testLists()
    {
        $http = $this->getHttp();
        $http->shouldReceive('json')->andReturnUsing(function ($url, $form) {
            return [
                'url' => $url,
                'form' => $form,
            ];
        });
        $material = new Material($http);

        // normal
        $response = $material->lists('image');

        $params = [
            'type' => 'image',
            'offset' => 0,
            'count' => 20,
        ];

        $this->assertEquals(Material::API_LISTS, $response['url']);
        $this->assertEquals($params, $response['form']);

        // out of range
        $response = $material->lists('image', 1, 21);

        $params = [
            'type' => 'image',
            'offset' => 1,
            'count' => 20, // 21 -> 20
        ];

        $this->assertEquals(Material::API_LISTS, $response['url']);
        $this->assertEquals($params, $response['form']);
    }

    /**
     * Test for stats().
     */
    public function testStats()
    {
        $http = $this->getHttp();
        $http->shouldReceive('get')->andReturnUsing(function($url){
           return [
               'url' => $url,
               'result' => ['image' => 10, 'video' => 6],
           ];
        });
        $material = new Material($http);

        $response = $material->stats();

        $this->assertEquals(Material::API_STATS, $response['url']);
        $this->assertEquals(['image' => 10, 'video' => 6], $response['result']);
    }


}
