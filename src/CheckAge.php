<?php
public function handle(Request $request, Closure $next)
{
    #在小程序端利用header传递过来token
    $token=$request->header('token');
    #判断token是否为空
    if(empty($token))
    {
        return ['code'=>500,'msg'=>'请先登录用户账号'];
    }
    #token不为空 将user_id解析出来
    $user_id=(new Token())->decode($token);
    #给$request赋值一个uid
    $request->uid=$user_id->id;
    return $next($request);
}