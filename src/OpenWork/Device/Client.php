<?php

namespace EasyWeChat\OpenWork\Device;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Kernel\ServiceContainer;

/**
 * Device Client
 *
 * @author moniang <me@imoniang.com>
 */
class Client extends BaseClient
{
    public function __construct(ServiceContainer $app)
    {
        parent::__construct($app, $app['provider_access_token']);
    }


    /**
     * 添加设备实例
     *
     * 该API用于添加一个设备的实例
     *
     * @param string $modelId  设备的型号id，在服务商管理端添加设备型号之后，可以查看型号id
     * @param string $deviceSn 硬件序列号，只能包含数字和大小写字母，长度最大为128字节，不可与之前已导入的相同
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function add(string $modelId, string $deviceSn)
    {
        return $this->httpPostJson('cgi-bin/service/add_device', [
            'model_id' => $modelId,
            'device_sn' => $deviceSn
        ]);
    }

    /**
     * 查询设备绑定信息
     *
     * 该API用于查询设备绑定的企业信息
     *
     * @param string $deviceSn 硬件序列号，只能包含数字和大小写字母，长度最大为128字节
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function get(string $deviceSn)
    {
        return $this->httpPostJson('cgi-bin/service/get_device_auth_info', [
            'device_sn' => $deviceSn
        ]);
    }

    /**
     * 重置设备SecretNo
     *
     * 该API用于重置所有类型设备的SecretNo，主要针对同一批次生成相同secretNo(seedSecretNo)的设备，
     * 可将SecretNo的状态转换为未设置状态，使设备可以重新调用get_secret_no获取新的sercretNo，
     * 如果对存量设备调用此接口，那么设备固件必须支持通过seed_secret_no获取secretNo。
     *
     * @param string $deviceSn 硬件序列号，只能包含数字和大小写字母，长度最大为128字节
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function reset(string $deviceSn)
    {
        return $this->httpPostJson('cgi-bin/service/reset_secret_no', [
            'device_sn' => $deviceSn
        ]);
    }

    /**
     * 获取设备列表
     *
     * 硬件服务商可以通过本接口获取已登记的设备信息
     *
     * @param int $offset 用于分页拉取数据，表示偏移量
     * @param int $limit  用于分页拉取数据，表示请求的数据条数
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function list(int $offset, int $limit)
    {
        return $this->httpPostJson('cgi-bin/service/list_device', [
            'offset' => $offset,
            'limit' => $limit
        ]);
    }

    /**
     * 上传设备日志
     *
     * 该API用于异步拉取某个设备的日志文件
     *
     * @param string $deviceSn 硬件序列号，只能包含数字和大小写字母，长度最大为128字节
     * @param string $hint     提示参数，企业微信后台会将此参数透传给设备，设备可根据此参数来决定要上传哪部分日志，服务商可根据实际业务需求来使用此参数，最长为128字节
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function uploadLog(string $deviceSn, string $hint)
    {
        return $this->httpPostJson('cgi-bin/service/fetch_device_log', [
            'device_sn' => $deviceSn,
            'hint' => $hint
        ]);
    }

    /**
     * 获取设备自定义参数
     *
     * @param string $deviceSn 硬件序列号，只能包含数字和大小写字母，长度最大为128字节
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function getFeature(string $deviceSn)
    {
        return $this->httpPostJson('cgi-bin/hardware/get_device_feature', [
            'device_sn' => $deviceSn
        ]);
    }

    /**
     * 删除设备实例
     *
     * 该API用于删除一个设备的实例
     *
     * @param string $deviceSn 硬件序列号，只能包含数字和大小写字母，长度最大为128字节
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function delete(string $deviceSn)
    {
        return $this->httpPostJson('cgi-bin/service/del_device', [
            'device_sn' => $deviceSn
        ]);
    }

    /**
     * 设置打印机支持状态
     *
     * 该API用于设置打印盒子是否支持打印机的信息
     *
     * @param string $deviceSn  硬件序列号，只能包含数字和大小写字母，长度最大为128字节
     * @param bool   $supported 是否支持打印机
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function setPrinterSupportState(string $deviceSn, bool $supported)
    {
        return $this->httpPostJson('cgi-bin/service/del_device', [
            'device_sn' => $deviceSn,
            'not_supported_printer' => (int)!$supported
        ]);
    }
}
