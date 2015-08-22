<?php

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
        public function getHttp()
        {
            $http = Mockery::mock(Http::class);
            $http->shouldReceive('setExpectedException')->andReturn($http);

            return $http;
        }

        /**
         * Test download()
         *
         * @expectedException EasyWeChat\Core\Exceptions\InvalidArgumentException
         */
        public function testDownload()
        {
            $http = $this->getHttp();
            $request = new \stdClass();
            $http->shouldReceive('get')->andReturnUsing(function($url, $params) use ($request) {
                $request->url = $url;
                $request->params = $params;
                return $request;
            });

            $temporary = new Temporary($http);

            $response = $temporary->download('foo', __DIR__);

            $this->assertEquals(Temporary::API_GET, $request->url);
            $this->assertEquals(['media_id' => 'foo'], $request->params);
            $this->assertEquals('foo.jpg', $response);
            $this->assertEquals(__DIR__.'/foo.jpg', $GLOBALS['temporary_download_filename']);
            $this->assertEquals($request, $GLOBALS['temporary_download_content']);

            // exception path not exists.
            $response = $temporary->download('foo', '/this-is-are-non-exists-path');
        }

        /**
         * Test upload();
         *
         * @expectedException EasyWeChat\Core\Exceptions\InvalidArgumentException
         */
        public function testUpload()
        {
            $http = $this->getHttp();
            $http->shouldReceive('upload')->andReturnUsing(function($url, $params) {
                return [
                    'url' => $url,
                    'params' => $params,
                ];
            });

            $temporary = new Temporary($http);

            $result = $temporary->upload('image', __DIR__.'/stubs/image.jpg');

            $this->assertEquals(Temporary::API_UPLOAD, $result['url']);
            $this->assertEquals(['media' => __DIR__.'/stubs/image.jpg'], $result['params']);


            $temporary->upload('image', '/this-is-are-non-exists-path/foo.jpg');// exception,invalid path
        }

        /**
         * Test download()
         *
         * @expectedException EasyWeChat\Core\Exceptions\InvalidArgumentException
         */
        public function testUploadWithInvalidType()
        {
            $temporary = new Temporary($this->getHttp());
            $temporary->upload('img', __DIR__.'/stubs/image.jpg');// exception,invalid type
        }

        /**
         * Test __call();
         */
        public function testProxyMethods()
        {
            $temporary = Mockery::mock(Temporary::class.'[upload]', [$this->getHttp()]);
            $temporary->shouldReceive('upload')->andReturnUsing(function($type, $path){
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
    class File {
        public static function getStreamExt()
        {
            return 'jpg';
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