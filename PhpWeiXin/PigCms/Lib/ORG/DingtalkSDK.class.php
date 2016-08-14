<?php

/**
 * DingtalkSDK short summary.
 *
 * DingtalkSDK description.
 *
 * @version 1.0
 * @author Administrator
 */
class DingtalkSDK
{    /**
     * ����ʵ��
     * @var object
     */
    protected static $instance;
    /**
     * �ӿڵ��õ�ַ
     * @var string
     */
    protected static $apiUrl = 'https://oapi.dingtalk.com/';
    /**
     * ����ͷ��Ϣ
     * @var array
     */
    protected static $headers = [
        'Content-Type' => 'application/json',
    ];
    /**
     * ������������Ϣ
     * @var array
     */
    protected static $config = [
        'agentid'    => '',
        'corpid'     => '',
        'corpsecret' => '',
        'ssosecret'  => '',
    ];

    /**
     * ʵ��������SDK
     * @param array $config ���ò���
     */
    public function __construct($config = [])
    {
        if (!empty($config) && is_array($config)) {
            self::$config = array_merge(self::$config, $config);
        }
    }
    /**
     * ʵ�����ľ�̬����
     * @param  array   $config ������Ϣ
     * @param  boolean $force  ǿ������ʵ����
     * @return \tools\DDing
     */
    public static function instance($config = [], $force = false)
    {
        if (is_null(self::$instance) || $force == true) {
            self::$instance = new static($config);
        }
        return self::$instance;
    }
    /**
     * ����/��ȡ ���ñ���
     * @param  string $key
     * @param  string $value
     * @return string
     */
    public static function config($key, $value = null)
    {
        if (is_null($value)) {
            return self::$config[$key];
        } else {
            self::$config[$key] = $value;
        }
    }
    /**
     * JS-APIȨ����֤��������
     * @return array
     */
    public static function ddConfig()
    {
        $randomStr = uniqid();
        $timestamp = time();
        $config    = [
            'agentId'   => self::$config['agentid'],
            'corpId'    => self::$config['corpid'],
            'timeStamp' => $timestamp,
            'nonceStr'  => $randomStr,
        ];
        $config['signature'] = self::sign($randomStr, $timestamp);
        return $config;
    }
    /**
     * ����ǩ���㷨
     * @param  string $jsapi_ticket
     * @param  string $noncestr
     * @param  string $timestamp
     * @param  string $url
     * @return string
     */
    private static function sign($noncestr, $timestamp)
    {
        $signArr = [
            'jsapi_ticket' => self::$config['jsapi_ticket'],
            'noncestr'     => $noncestr,
            'timestamp'    => $timestamp,
            'url'          => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], // ��ȡ��ǰҳ���ַ �д��Ż�
        ];
        ksort($signArr);
        $signStr = urldecode(http_build_query($signArr));
        return sha1($signStr);
    }
}