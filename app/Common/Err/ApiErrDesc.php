<?php

namespace App\Common\Err;

class ApiErrDesc
{

    /**
     * API 通用错误码
     * error_code <10
     */
    const UPLOAD_SUCCESS = [0, 'Success'];
    const UPLOAD_UNKOWN_ERR = [1, '未知错误'];
    const UPLOAD_ERROR = [2, '上传失败'];
    const ERROR_URL = [3, '访问的接口不存在'];
    const NO_IMG_UPLOAD = [4, '没有图片上传'];
    const ERR_IMG_UPLOAD = [4, '图片上传不全'];
    const NO_METHOD_POST = [5, '不是post上传'];
    const UPLOAD_IMG_SUCCESS = [6, '图片上传成功'];

    //const ERR_PARAMS = [100,'参数错误'];

    /**
     * krpano 通用提示码
     * code 10-20
     */
    const SUCCESS_KRPANO = [200, '切图成功'];
    const ERR_KRPANO_ENV = [11, '配置项错误'];
    const ERR_KRPANO_PARAMS = [12, '参数异常'];


    /**
     * error_code 1001-1100  用户登录相关的错误
     */
    const ERR_TOKEN_EXPIRE = [1003, '登录过期'];
    const ERR_LOGIN_PASSWORD = [1001, '密码错误'];
    const ERR_USER_NOT_EXIST = [1002, '用户不存在'];

    const PANO_ARR_REPLACE = [
        "parlour" => "客厅",
        "bedroom" => "卧室",
        "cookroom" => "厨房",
        "toilet" => "卫生间",
        "exterior" => "外景房",
    ];

    public function seachArrVal()
    {

    }
}
