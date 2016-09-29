<?php

namespace common\helpers;

class Helper
{
    const JSON_OPTION = 320;

    /**
     * getSalt() 获取随机的加密盐值
     * @param  int $intNum 密钥长度
     * @return string
     */
    public static function getSalt($intNum = 6)
    {
        $str = str_shuffle('0123456789abcdefghigklmnopqrstuvwsyzABCDEFEHIGKLMNOPQRSTUVWSYZ');
        return substr($str, 0, $intNum);
    }

    /**
     * getIpAddress() 获取IP地址
     * @return string 返回字符串
     */
    public static function getIpAddress()
    {
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $strIpAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $strIpAddress = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $strIpAddress = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $strIpAddress = getenv('HTTP_X_FORWARDED_FOR');
            } else if (getenv('HTTP_CLIENT_IP')) {
                $strIpAddress = getenv('HTTP_CLIENT_IP');
            } else {
                $strIpAddress = getenv('REMOTE_ADDR') ? getenv('REMOTE_ADDR') : '';
            }
        }

        return $strIpAddress;
    }

    /**
     * encode() json_encode() 处理数据
     * @param  mixed $params 处理的数据
     * @return string
     */
    public static function encode($params)
    {
        return json_encode($params, self::JSON_OPTION);
    }

    /**
     * create() 根据类名和命名空间创建对象
     * @param $strClassName
     * @param string $namespace
     * @return null
     */
    public static function create($strClassName, $namespace = 'payments')
    {
        $objReturn    = null;
        $strClassName = __NAMESPACE__.'\\'.$namespace.'\\'.ucfirst($strClassName);
        if (class_exists($strClassName)) $objReturn = new $strClassName;
        return $objReturn;
    }
}