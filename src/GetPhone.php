<?php

# 1.小程序端代码示例
# 微信开发者文档地址：https://developers.weixin.qq.com/miniprogram/dev/framework/open-ability/getPhoneNumber.html

//<button open-type="getPhoneNumber" bindgetphonenumber="getPhoneNumber"></button>
//
//Page({
//  getPhoneNumber (e) {
//    console.log(e.detail.code)
//  }
//})

# 2.获取access_token
# 微信开发者文档地址：https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/access-token/auth.getAccessToken.html
    //获取手机号code
    $code=$request->post('code');
    
    $Appid="Appid";
    $secret="secret";
    $getUrl="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$Appid&secret=$secret";
    //进行解码
    $fileGet=file_get_contents($getUrl);
    $jsonRes=json_decode($fileGet,true);
    //获取access_token
    $access_token=$jsonRes['access_token'];

# 3.获取手机号
# 微信开发者文档地址：https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/phonenumber/phonenumber.getPhoneNumber.html
    //POST地址
    $postUrl="https://api.weixin.qq.com/wxa/business/getuserphonenumber?access_token=$access_token";
    //将code转化为数组
    $code=['code'=>$code];

    //post地址 进行解码
    # 注：这个post可以封装到服务层 进行调用
    # $url 是 $postUrl $code 是 $data
    function posturl($url,$data){
        $data = json_encode($data);
        $headerArray =array("Content-type:application/json;charset='utf-8'","Accept:application/json");
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl,CURLOPT_HTTPHEADER,$headerArray);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return json_decode($output,true);
        }

    //这个方法return出来的是$res
    $phone=$res['phone_info']['phoneNumber'];
