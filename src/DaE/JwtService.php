<?php
namespace App\Http\service;

use App\Models\Relation;
use app\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Redis;

class JwtService
{
    protected $salt;
    public function __construct()
    {
        $this->salt=md5('dae');
    }

    public function generateToken($userID)
    {
        //从配置信息这种或取唯一字符串
        $currentTime = time();
        $payload = [
            'iss' => 'http://2005a.com',//签发者 可以为空
            'aud' => 'http://example.com',//面象的用户，可以为空
            'iat' => $currentTime,//签发时间
            'nbf' => $currentTime,//立马生效
            "exp" => $currentTime + 7200, //token 过期时间 两小时
            'data' => [
                'uid' => $userID
            ]
        ];
        $token = JWT::encode($payload, $this->salt, 'HS256');
        return $token;
    }


    public function checkToken($token)
    {
        $decoded = JWT::decode($token, new Key($this->salt, 'HS256'));
        return $decoded;
    }



    function curGet($url){
        $headerArray = array("Content-type:application/json;", "Accept:application/json");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
        $output = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($output, true);
        return $output;
    }

    function curlPost($url, $data)
    {
        $data = json_encode($data);
        $headerArray = array("Content-type:application/json;charset='utf-8'", "Accept:application/json");
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headerArray);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return json_decode($output,true);
    }

    public function getAccessToken(){
        try {
            $accessToken =Redis::get('wx_access_token');
        }catch (\Exception $exception){
            return error('redis服务器异常');
        }

        if (empty($accessToken)){
            $accessUrl=sprintf(config('wx.accessTokenUrl'), config('wx.appID'), config('wx.appSc'));
            $result = (new JwtService())->curGet($accessUrl);

            if (isset($result['errorcode'])){
                return error('获取access_token失败');
            }
            $accessToken = $result['access_token'];

            Redis::set('wx_access_token',$accessToken,7100);
        }

        return $accessToken;
    }

    public function paGet(){
        $data =\Illuminate\Support\Facades\Request::all();
        $page = isset($data['page'])?$data['page']:1;
        $limit= (isset($data['limit'])?$data['limit']:10)>100?100:(isset($data['limit'])?$data['limit']:10);

        $offset= ($page-1)*$limit;

        return [
            intval( $offset),
            intval($limit)
        ];



    }

    public function sendCode($phone){
        $code=rand(1000,9999);
        Redis::setex("code",60,$code);
        $statusStr = array(
            "0" => "短信发送成功",
            "-1" => "参数不全",
            "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
            "30" => "密码错误",
            "40" => "账号不存在",
            "41" => "余额不足",
            "42" => "帐户已过期",
            "43" => "IP地址限制",
            "50" => "内容含有敏感词"
        );
        $smsapi = "http://api.smsbao.com/";
        $user = "luobofish"; //短信平台帐号
        $pass = md5("wxb20021103"); //短信平台密码
        $content="【大鹅】您的验证码为".$code."，在20分钟内有效";//要发送的短信内容
        $phone = "$phone";//要发送短信的手机号码
        $sendurl = $smsapi."sms?u=".$user."&p=".$pass."&m=".$phone."&c=".urlencode($content);
        $result =file_get_contents($sendurl) ;

        return error($statusStr[$result]);

    }

}
