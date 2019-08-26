<?php


namespace App\Common\Auth;


use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\ValidationDataTest;

/**
 * 单例
 * Class JwtAuth
 * @package App\Common\Auth
 */
class JwtAuth
{
    /**
     * jwt token
     * @var 
     */
    private $token;

    private $iss = 'loaclhost/pano';
    private $aud = 'api_server_app';

    private $uid;

    /**
     * 客户端传过来的token
     * @var
     */
    private $decodeToken;

    private $secrect = 'sd@%chws32@#%nec%%3scjnsiuhc';
    /**
     * 单例模式 jwtAuth句柄
     * @var 
     */
    private static $instance;

    /**
     * 获取jwtAuth的句柄
     * @return JwtAuth
     */
    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self();
        }

        return new self();
    }

    /**
     * 私有化构造函数
     * JwtAuth constructor.
     */
    private function __construct()
    {
    }

    /**
     * 私有化clone函数
     */
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * 获取token
     * @return string
     */
    public function getToken()
    {
        return (string)$this->token;
    }

    /**
     * 设置token
     * @param $token
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    public function setUid($uid)
    {
        $this->uid = $uid;
        return $this;
    }
    /**
     * 编码jwt token
     * @return $this
     */
    public function jwt_encode()
    {
        $time = time();
        $this->token = (new Builder())
            ->issuedBy($this->iss)
            ->canOnlyBeUsedBy($this->aud)
            ->issuedAt($time)
            ->expiresAt($time+3600)
            ->with('uid',$this->uid)
            ->identifiedBy($this->secrect,true)
            ->getToken();

        return $this;
    }

    /**
     * 
     * @return \Lcobucci\JWT\Token
     */
    public function jwt_decode()
    {
        if(!$this->decodeToken)
        {
            $this->decodeToken = (new Parser())->parse((string)$this->token);
            $this->uid = $this->decodeToken->getClaim('uid');
        }
        return $this->decodeToken;
    }

    /**
     * verify token
     */
    public function verify()
    {
        $result = $this->jwt_decode()->verify(new Sha256(),$this->secrect);
        return $result;
    }
    /**
     * @return bool
     */
    public function validate()
    {
        $data = new ValidationDataTest();
        $data->setIssuerShouldChangeTheIssuer($this->iss);
        $data->setAudienceShouldChangeTheAudience($this->aud);
        return $this->jwt_decode()->validate($data);
    }

}