<?php

namespace App\Http\Controllers;


use App\Http\service\JwtService;

use App\Http\service\NoteService;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class LoginController extends Controller
{
    public function token(Request $request){
        $loginUrl=sprintf(config('wx.wxLoginurl'),config('wx.appID'),config('wx.appSc'),$request->get('code'));

        $result=(new JwtService())->curGet($loginUrl);
        if (isset($result['errcode'])){
            return error('微信内部服务错误！');
        }
        $userInfo=User::where('openid',$result['openid'])->first();

        if (!$userInfo){
            User::create($result);
        }else{
            $userInfo->session_key=$result['session_key'];
            $userInfo->updated_at=time();
            $userInfo->save();
        }
        $token=(new JwtService())->generateToken($userInfo['id']);
        return success('token生成成功',$token);
    }

    public function sendCode(Request $request){
        try {
            $phone=$request->get('phone');
            $time=time();
            $send_time=Cache::get('time_key');
            if (time()-$send_time<30){
                return error('每30秒只能发送一次');
            }
            if (empty($send_time)){
                Cache::set('time_key',$time,30);
            }
            $res=(new NoteService())->sendCode($phone);

            return success('验证码发送成功',$res);
        }catch (\Exception $exception){
            return error('验证码发送失败');
        }


    }

    public function login(Request $request){
        $uid=$request->uid;

        $param=$request->all();
        if (empty($param['phone']&&$param['code'])){
            return error('手机号不能为空或验证码不能为空');
        }
        $phone=$request->all('phone');

        $user=User::where('phone',$phone)->first();

        if (empty($user)){
            User::create($phone);

        }
        $code=Redis::get("code");
        if (empty($code)){
            return error('验证码过期请重新获取');
        }
        if ($code!=$param['code']){
            return error('验证码错误');
        }

        return success('登录成功');
    }

    public function Wxlogin(Request $request){
        $param=$request->all();

        $loginUrl=sprintf(config('wx.wxLoginurl'),config('wx.appID'),config('wx.appSc'),$request->get('code'));
        $result=(new JwtService())->curGet($loginUrl);
        if (isset($result['errcode'])){
            return error('微信内部服务错误！');
        }
        $userinfo = [
            'openid' => $result['openid'],
            'session_key' => $result['session_key'],
            'avatarUrl' => $param['avatarUrl'],
            'nickName' => $param['nickName'],
        ];

        $userInfo=User::where('openid',$result['openid'])->first();
        if ($userInfo){
            $userInfo->updated_at=time();
            $userInfo->save();
            return success('您已经登录过了');
        }else{
            User::create($userinfo);
        }
    }

    public function phone(Request $request){
        $code=$request->get('code');
        $accessToken=(new JwtService())->getAccessToken();
        $phoneUrl = sprintf( config('wx.getPhoneUrl'),$accessToken);
        $result=(new JwtService())->curlPost($phoneUrl,['code'=>$code]);

        if ($result['errcode']!=0){
            return error('获取手机号失败');
        }
        $uid=$request->uid;

        $phone = $result['phone_info']['phoneNumber'];
        $res=User::where('id',$uid)->update([
            'phone'=>$phone,

        ]);

        if ($res) {
            return success('手机号更新成功');
        }

        return error('手机号更新失败');

    }
}
