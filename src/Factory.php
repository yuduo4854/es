<?php
//这个是一个工厂的设计模式

//新建一个 php Class
# Factory是类名
class Factorys
{
    //创建一个方法 方法里面的参数 一个是类型 一个是要传的值
    public static function choose($type,$data)
    {
        switch ($type) {
            case "0":
            {
                return Email::vaccine($data);
            }
            case "1":
            {
                return phone::vaccine($data);
            }
            default:
            {
                return null;
            }
        }
    }
}

//创建一个抽象类
interface choose
{
    #这个是要被继承的方法
    public static function  vaccine($data);
}

//自定义一个类 继承抽象类
class Email implements choose
{
    public static function vaccine($data)
    {
        //获取邮箱
        $email=$data['email'];
        Mail::raw($data['nickName'].'你已经预约成功',function($message)use ($email)
        {
            $to="$email";
            $message->to($to)->subject("$to");
        });
    }
}


//自定义一个类 继承抽象类
class phone implements choose
{
    public static function vaccine($data)
    {
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
        $user = "***"; //短信平台帐号
        $pass = md5("****"); //短信平台密码
        $content="短信内容";//要发送的短信内容
        $phone = "*****";//要发送短信的手机号码
        $sendurl = $smsapi."sms?u=".$user."&p=".$pass."&m=".$phone."&c=".urlencode($content);
        $result =file_get_contents($sendurl) ;
        echo $statusStr[$result];
    }
}