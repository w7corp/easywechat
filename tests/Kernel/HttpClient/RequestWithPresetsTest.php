<?php

namespace EasyWeChat\Tests\Kernel\HttpClient;

use EasyWeChat\Kernel\HttpClient\RequestWithPresets;
use PHPUnit\Framework\TestCase;

class RequestWithPresetsTest extends TestCase
{
    public function test_it_can_with_key_value()
    {
        $client = new DummyClassForRequestWithPresetsTest();

        $client->with('foo', 'bar')
            ->with('bar')
            ->with(['appid' => 'wx123456', 'secret'])
            ->with([
                'age' => 12,
                'name' => 'overtrue',
            ])
            ->with('items', ['foo', 'bar']);

        $this->assertSame([
            'foo' => 'bar',
            'bar' => null,
            'appid' => 'wx123456',
            'secret' => null,
            'age' => 12,
            'name' => 'overtrue',
            'items' => ['foo', 'bar'],
        ], $client->getPrependsParts());

        // update
        $client->with('foo', 'baz');
        $this->assertSame([
            'foo' => 'baz',
            'bar' => null,
            'appid' => 'wx123456',
            'secret' => null,
            'age' => 12,
            'name' => 'overtrue',
            'items' => ['foo', 'bar']
        ], $client->getPrependsParts());
    }

    public function test_it_can_with_key_of_presets()
    {
        $client = new DummyClassForRequestWithPresetsTest();

        $client->setPresets([
            'appid' => 'wx123456',
            'secret' => 'helloworld',
            'bar' => 'baz',
        ]);

        $client->with('foo', 'bar')
            ->with('bar')
            ->with(['appid', 'secret']);

        $this->assertSame([
            'foo' => 'bar',
            'bar' => 'baz',
            'appid' => 'wx123456',
            'secret' => 'helloworld',
        ], $client->getPrependsParts());
    }

    public function test_it_can_with_use_magic_call()
    {
        $client = new DummyClassForRequestWithPresetsTest();

        $client->setPresets([
            'appid' => 'wx123456',
            'secret' => 'helloworld',
            'bar' => 'balabala',
            'name' => 'w7corp',
        ]);

        $client->with('foo', 'bar')
            ->with('bar')
            ->withAppid()
            ->withSecret()
            ->withBarAs('baz')
            ->withName('overtrue');

        $this->assertSame([
            'foo' => 'bar',
            'bar' => 'balabala',
            'appid' => 'wx123456',
            'secret' => 'helloworld',
            'baz' => 'balabala',
            'name' => 'overtrue',
        ], $client->getPrependsParts());
    }

    public function test_it_can_merge_to_options()
    {
        $client = new DummyClassForRequestWithPresetsTest();

        $client->setPresets([
            'appid' => 'wx123456',
            'secret' => 'helloworld',
            'bar' => 'balabala',
            'name' => 'w7corp',
        ]);

        // empty
        $this->assertSame([], $client->mergeThenResetPrepends([]));

        // GET/HEAD/DELETE
        $client->withAppid()->withSecret();
        $this->assertSame(['query' => ['appid' => 'wx123456', 'secret' => 'helloworld']], $client->mergeThenResetPrepends([]));
        $client->withAppid()->withSecret();
        $this->assertSame(['query' => ['appid' => 'wx123456', 'secret' => 'helloworld']], $client->mergeThenResetPrepends([], 'HEAD'));
        $client->withAppid()->withSecret();
        $this->assertSame(['query' => ['appid' => 'wx123456', 'secret' => 'helloworld']], $client->mergeThenResetPrepends([], 'DELETE'));

        // POST/PUT/PATCH
        $client->withAppid()->withSecret();
        $this->assertSame(['body' => ['appid' => 'wx123456', 'secret' => 'helloworld']], $client->mergeThenResetPrepends([], 'POST'));
        $client->withAppid()->withSecret();
        $this->assertSame(['body' => ['appid' => 'wx123456', 'secret' => 'helloworld']], $client->mergeThenResetPrepends([], 'PUT'));
        $client->withAppid()->withSecret();
        $this->assertSame(['body' => ['appid' => 'wx123456', 'secret' => 'helloworld']], $client->mergeThenResetPrepends([], 'PATCH'));

        // merge
        $client->withAppid();
        $this->assertSame(
            ['query' => ['appid' => 'wx123456', 'name' => '1234']],
            $client->mergeThenResetPrepends(['query' => ['name' => '1234']])
        );

        $client->withAppid();
        $this->assertSame(
            ['body' => ['appid' => 'wx123456', 'name' => '1234']],
            $client->mergeThenResetPrepends(['body' => ['name' => '1234']], 'POST')
        );

        // XML
        // 1. !empty xml
        $client->withAppid();
        $this->assertSame(
            ['xml' => ['appid' => 'wx123456', 'name' => '1234']],
            $client->mergeThenResetPrepends(['xml' => ['name' => '1234']], 'POST')
        );
        // 2. content-type is xml
        $client->withAppid();
        $this->assertSame(
            ['headers' => ['content-type' => 'text/xml'], 'xml' => ['appid' => 'wx123456']],
            $client->mergeThenResetPrepends(['headers' => ['content-type' => 'text/xml']], 'POST')
        );

        // JSON
        // 1. !empty json
        $client->withAppid();
        $this->assertSame(
            ['json' => ['appid' => 'wx123456', 'name' => '1234']],
            $client->mergeThenResetPrepends(['json' => ['name' => '1234']], 'POST')
        );
        // 2. content-type is json
        $client->withAppid();
        $this->assertSame(
            ['headers' => ['content-type' => 'application/json'], 'json' => ['appid' => 'wx123456']],
            $client->mergeThenResetPrepends(['headers' => ['content-type' => 'application/json']], 'POST')
        );

        // HEADERS
        $client->withAppid()->withHeader('X-foo', 'bar');
        $this->assertSame(
            ['headers' => ['X-foo' => 'bar', 'content-type' => 'application/json'], 'json' => ['appid' => 'wx123456']],
            $client->mergeThenResetPrepends(['headers' => ['content-type' => 'application/json']], 'POST')
        );

        // 带下划线
        $client->setPresets([
            'app_id' => 'wx123456',
            'secret' => 'helloworld',
            'bar' => 'balabala',
            'name' => 'w7corp',
        ]);

        // GET/HEAD/DELETE
        $client->withAppId()->withSecret();
        $this->assertSame(['query' => ['app_id' => 'wx123456', 'secret' => 'helloworld']], $client->mergeThenResetPrepends([]));
        $client->withAppId()->withSecret();
        $this->assertSame(['query' => ['app_id' => 'wx123456', 'secret' => 'helloworld']], $client->mergeThenResetPrepends([], 'HEAD'));
        $client->withAppId()->withSecret();
        $this->assertSame(['query' => ['app_id' => 'wx123456', 'secret' => 'helloworld']], $client->mergeThenResetPrepends([], 'DELETE'));

        // POST/PUT/PATCH
        $client->withAppId()->withSecret();
        $this->assertSame(['body' => ['app_id' => 'wx123456', 'secret' => 'helloworld']], $client->mergeThenResetPrepends([], 'POST'));
        $client->withAppId()->withSecret();
        $this->assertSame(['body' => ['app_id' => 'wx123456', 'secret' => 'helloworld']], $client->mergeThenResetPrepends([], 'PUT'));
        $client->withAppId()->withSecret();
        $this->assertSame(['body' => ['app_id' => 'wx123456', 'secret' => 'helloworld']], $client->mergeThenResetPrepends([], 'PATCH'));

        // test As
        $client->withAppIdAs('test')->withSecret();
        $this->assertSame(['query' => ['test' => 'wx123456', 'secret' => 'helloworld']], $client->mergeThenResetPrepends([]));

        // test key/value
        $client->withAppId('test')->withSecret();
        $this->assertSame(['query' => ['app_id' => 'test', 'secret' => 'helloworld']], $client->mergeThenResetPrepends([]));
    }

    public function test_it_can_with_headers()
    {
        $client = new DummyClassForRequestWithPresetsTest();

        $client->withHeaders(['content-type' => 'application/xml'])->withHeader('accept', 'application/json');

        $this->assertSame(
            ['content-type' => 'application/xml', 'accept' => 'application/json'],
            $client->getPrependsHeaders()
        );

        // update
        $client->withHeaders(['content-type' => 'text/xml']);
        $this->assertSame(
            ['content-type' => 'text/xml', 'accept' => 'application/json'],
            $client->getPrependsHeaders()
        );
    }
}

class DummyClassForRequestWithPresetsTest
{
    use RequestWithPresets;

    public function getPrependsParts(): array
    {
        return $this->prependParts;
    }

    public function getPrependsHeaders(): array
    {
        return $this->prependHeaders;
    }

    public function __call(string $name, array $arguments)
    {
        if (\str_starts_with($name, 'with')) {
            return $this->handleMagicWithCall($name, $arguments[0] ?? null);
        }

        throw new \BadMethodCallException('Call to undefined method ' . __CLASS__ . '::' . $name . '()');
    }
}
