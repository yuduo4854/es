<?php
//这个是一个工厂的设计模式

# 1.先定义一个抽象类
interface payfactory
{
    # 2.抽象类里面定义静态方法 （方便调用）
    //支付类
    public static function pay($money);
}


# 3.根据自己的继承抽象类 定义自己的产品
//支付宝
class Alipay implements payfactory
{
    public static function pay($money)
    {
        echo "支付宝：" . $money;
    }
}

//微信
class WXpay implements payfactory
{
    public static function pay($money)
    {
        echo "微信" . $money;
    }
}

//微信
class Unionpay implements payfactory
{
    public static function pay($money)
    {
        echo "银联支付" . $money;
    }
}

class PayList
{
    public static function choose($payType,$money)
    {
        switch ($payType) {
            case "Alipay":
            {
                return Alipay::pay($money);
            }
            case "WXpay":
            {
                return WXPay::pay($money);
            }
            case "Unionpay":
            {
                return Unionpay::pay($money);
            }
            default:
            {
                return null;
            }
        }
    }
}