<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace
{
    use EasyWeChat\Core\Http;
    use EasyWeChat\Material\Temporary;
    use Mockery\Mock;

    class MaterialTemporaryTest extends TestCase
    {
        /**
         * Return mock http.
         *
         * @return \Mockery\MockInterface
         */
        public function getHttp($methods)
        {
            $http = Mockery::mock(Http::class.'[$methods]');

            return $http;
        }

        public function getMockAccessToken()
        {
            $token = Mockery::mock('EasyWeChat\Core\AccessToken[getQueryFields]', ['foo', 'bar']);
            $token->shouldReceive('getQueryFields')->andReturn(['access_token' => 'foo']);

            return $token;
        }

        /**
         * Test download().
         *
         * @expectedException EasyWeChat\Core\Exceptions\InvalidArgumentException
         */
        public function testDownload()
        {
            $request = new \stdClass();
            $accessToken = Mockery::mock('EasyWeChat\Core\AccessToken');
            $accessToken->shouldReceive('getQueryFields')->andReturn(['access_token' => 'foo']);
            $temporary = Mockery::mock('EasyWeChat\Material\Temporary[getStream]', [$accessToken]);
            $temporary->shouldReceive('getStream')->andReturnUsing(function ($mediaId) use ($request) {
                $request->mediaId = $mediaId;

                return $request;
            });

            $response = $temporary->download('foo', __DIR__);

            $this->assertEquals('foo', $request->mediaId);
            $this->assertEquals('foo.jpg', $response);
            $this->assertEquals(__DIR__.'/foo.jpg', $GLOBALS['temporary_download_filename']);
            $this->assertEquals($request, $GLOBALS['temporary_download_content']);

            // exception path not exists.
            $response = $temporary->download('foo', '/this-is-are-non-exists-path');
        }

        /**
         * Test upload();.
         *
         * @expectedException EasyWeChat\Core\Exceptions\InvalidArgumentException
         */
        public function testUpload()
        {
            $accessToken = Mockery::mock('EasyWeChat\Core\AccessToken');
            $accessToken->shouldReceive('getQueryFields')->andReturn(['access_token' => 'foo']);
            $temporary = Mockery::mock('EasyWeChat\Material\Temporary[parseJSON]', [$accessToken]);
            $temporary->shouldReceive('parseJSON')->andReturnUsing(function () {
                return func_get_args()[1];
            });

            $result = $temporary->upload('image', __DIR__.'/stubs/image.jpg');

            $this->assertStringStartsWith(Temporary::API_UPLOAD, $result[0]);
            $this->assertEquals(['media' => __DIR__.'/stubs/image.jpg'], $result[1]);

            $temporary->upload('image', '/this-is-are-non-exists-path/foo.jpg'); // exception,invalid path
        }

        /**
         * Test download().
         *
         * @expectedException EasyWeChat\Core\Exceptions\InvalidArgumentException
         */
        public function testUploadWithInvalidType()
        {
            $temporary = new Temporary($this->getMockAccessToken());
            $temporary->upload('img', __DIR__.'/stubs/image.jpg'); // exception,invalid type
        }

        /**
         * Test __call();.
         */
        public function testProxyMethods()
        {
            $accessToken = Mockery::mock('EasyWeChat\Core\AccessToken');
            $accessToken->shouldReceive('getQueryFields')->andReturn(['access_token' => 'foo']);
            $temporary = Mockery::mock(Temporary::class.'[upload]', [$accessToken]);
            $temporary->shouldReceive('upload')->andReturnUsing(function ($type, $path) {
                return [$type, $path];
            });

            $this->assertEquals(['image', '/foobar'], $temporary->uploadImage('/foobar'));
            $this->assertEquals(['video', '/foobar'], $temporary->uploadVideo('/foobar'));
            $this->assertEquals(['voice', '/foobar'], $temporary->uploadVoice('/foobar'));
            $this->assertEquals(['thumb', '/foobar'], $temporary->uploadThumb('/foobar'));
        }
    }
}

namespace EasyWeChat\Support
{
    class File
    {
        public static function getStreamExt()
        {
            return '.jpg';
        }
    }
}

namespace EasyWeChat\Material
{
    function file_put_contents($filename, $content)
    {
        $GLOBALS['temporary_download_filename'] = $filename;
        $GLOBALS['temporary_download_content'] = $content;

        return true;
    }
}
