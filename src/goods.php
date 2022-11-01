<?php

/**
 * 商品 库存预热 将库存数量 存储到redis中
 */
function goods_count()
{
    //获取到 上架的商品
    $res=Goods::where('state','0')->get();
    //存储到redis队列中
    foreach ($res as $val);
    {
        //定义存储redis 的名称 前面是随便写的名称 后面是商品中的商品id
        //保持它的唯一性
        $name = 'goods'.$val['id'];
        //循环商品的 库存 将库存数量存储到 redis中
        for($i=0;$i<$val['count'];$i++)
        {
            Redis::Lpush($name,1);
        }
    }
    return ['code'=>200,'msg'=>'商品库存预热成功'];
}
/**
 * 生成订单
 */
function goods_order(\Illuminate\Http\Request $request)
{
    //用户id
    $user_id=$request->get('user_id');
    //获取到商品id
    $goods_id=$request->get('goods_id');
    //生成订单前 确定redis 中是否还有库存
    //设置redis 的名称找到该商品的库存
    $redis_name='goods'.$goods_id;
    /**
     * 库存超出
     */
    //查询该商品的数量
    $redis_count=Redis::llen($redis_name);
    //判断这个商品数量是否 小于 0
    if($redis_count<0)
    {
        return ['codo'=>500,'msg'=>'不好意思，这个商品已经出售完毕'];
    }
    /**
     * 订单限制，只能够下一次订单，等这个订单支付过后，才能够下发另一个订单
     */
    //定义一个唯一的 商品名称
    $unique_name='unique'.$goods_id;
    //相当于redis的监听事件 查看这个用户有没有购买过
    $unique_order=Redis::sismember($unique_name,$user_id);
    if($unique_order)
    {
        return ['code'=>200,'msg'=>'该商品你已经买过了，无需再次购买'];
    }
    //如果没有购买过就 存入redis中 给他标记上已经有用户购买过这个商品
    Redis::sadd($unique_name,$user_id);
    //库存充足可以购买商品
    //通过 前端购买的商品数量
    //应付金额
    //实付金额
    //用户id
    //收货地址
    //订单编号
    //支付状态 已支付 未支付

    //进行生成数据

    //启动事务 到redis队列里面减去购买商品的数量
    for ($i=0;$i<$number;$i++)
    {
        Redis::lpop($queueName);
    }
    //添加订单

}

/**
 * 订单提醒
 */
# 进入env配置中 配置邮件
# MAIL_HOST = smtp.qq.com
# MAIL_PORT = 465
# MAIL_USERNAME = QQ邮箱
# MAIL_PASSWORD = QQ邮箱的密钥
# MAIL_ENCRYPTION = ssl
# MAIL_FROM_ADDRESS = QQ邮箱
# MAIL_FROM_NAME = QQ邮箱的标题

function mail(Request $request)
{
    //获取qq邮箱
    $mail = $request->get('mail');
    Mail::raw('邮箱内容',function ($message)
    {
        $to = 'QQ邮箱';
        $message->to($to)->subject("$to");
    });

}

/**
 * 小程序防止抖动
 */
#  clearTimeout(this.time);
#  this.time=setTimeout(()=>{

#  },1000)



