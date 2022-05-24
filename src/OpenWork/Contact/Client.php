<?php

namespace EasyWeChat\OpenWork\Contact;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Kernel\ServiceContainer;

/**
 * Contact Client
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
     * 异步通讯录id转译
     *
     * @param string      $authCorpId       授权企业corp_id
     * @param array       $mediaIdList      需要转译的文件的media_id列表，只支持后缀名为xls/xlsx，doc/docx，csv，txt的文件。
     * 不超过20个文件，获取方式使用{@see \EasyWeChat\OpenWork\Media\Client::uploadFile() 上传需要转译的文件}
     *
     * @param string|null $outputFileName   转译完打包的文件名，不需带后缀。企业微信后台会打包成zip压缩文件，并自动拼接上.zip后缀。
     * 若media_id_list中文件个数大于1，则该字段必填。若media_id_list中文件个数等于1，且未填该字段，则转译完不打包成压缩文件。支持id转译
     *
     * @param string|null $outputFileFormat 若不指定，则输出格式跟输入格式相同。若要转换输出格式，当前仅支持输出文件为pdf格式。
     * 若$mediaIdList中文件存在相同前缀名的文件，则输出文件命名规则为：文件前缀名_ 文件格式后缀.pdf，例如：20200901_ xlsx.pdf
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function idTranslate(string $authCorpId, array $mediaIdList, string $outputFileName = null, string $outputFileFormat = null)
    {
        /** @noinspection SpellCheckingInspection */
        return $this->httpPostJson('cgi-bin/service/contact/id_translate', [
            'auth_corpid' => $authCorpId,
            'media_id_list' => $mediaIdList,
            'output_file_name' => $outputFileName,
            'output_file_format' => $outputFileFormat
        ]);
    }

    /**
     * 获取异步任务结果
     *
     * @param string $jobId 异步任务id，最大长度为64字节
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function getResult(string $jobId)
    {
        /** @noinspection SpellCheckingInspection */
        return $this->httpGet('cgi-bin/service/batch/getresult', [
            'jobid' => $jobId
        ]);
    }

    /**
     * 通讯录userid排序
     *
     * @param string $authCorpId 查询的企业corp_id
     * @param array  $userIdList 要排序的user_id列表，最多支持1000个
     * @param int    $sortType   排序方式 0：根据姓名拼音升序排列，返回用户userid列表 1：根据姓名拼音降序排列，返回用户userid列表
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function sort(string $authCorpId, array $userIdList, int $sortType = 0)
    {
        /** @noinspection SpellCheckingInspection */
        return $this->httpPostJson('cgi-bin/service/contact/sort', [
            'auth_corpid' => $authCorpId,
            'sort_type' => $sortType,
            'useridlist' => $userIdList
        ]);
    }
}
