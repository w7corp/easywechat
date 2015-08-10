<?php

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
        $http = Mockery::mock('EasyWeChat\Core\Http');
        $http->shouldReceive('setExpectedException')->andReturn($http);

        return $http;
    }

    /**
     * Test for uploadImage()
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
        $material = new EasyWeChat\Material\Material($http);

        $response = $material->uploadImage(__DIR__.'/stubs/image.jpg');

        $this->assertEquals(Material::API_UPLOAD, $response['url']);
        $this->assertContains('stubs/image.jpg', $response['files']['media']);
    }

    /**
     * Test for uploadVoice()
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
        $material = new EasyWeChat\Material\Material($http);

        $response = $material->uploadVoice(__DIR__.'/stubs/voice.wma');

        $this->assertEquals(Material::API_UPLOAD, $response['url']);
        $this->assertContains('stubs/voice.wma', $response['files']['media']);
    }

    /**
     * Test for uploadThumb()
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
        $material = new EasyWeChat\Material\Material($http);

        $response = $material->uploadVoice(__DIR__.'/stubs/thumb.png');

        $this->assertEquals(Material::API_UPLOAD, $response['url']);
        $this->assertContains('stubs/thumb.png', $response['files']['media']);
    }

    /**
     * Test for uploadVideo()
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
        $material = new EasyWeChat\Material\Material($http);

        $response = $material->uploadVideo(__DIR__.'/stubs/video.mp4', 'foo', 'a mp4 video.');

        $this->assertEquals(Material::API_UPLOAD, $response['url']);
        $this->assertContains('stubs/video.mp4', $response['files']['media']);
        $this->assertEquals(json_encode(['title' => 'foo', 'introduction' => 'a mp4 video.']), $response['form']['description']);
    }
}