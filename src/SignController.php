<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Integrals;
use App\Models\Sign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SignController extends Controller
{
    //签到
    /**
     * @author 侯翔宇
     * @date 2022/11/19  8:31
     * @notes 签到日期
     */
    public function index()
    {
        $data=Sign::get()->toArray();
        return response()->json([
           'error_code'=>0,
           'msg'=>'签到',
           'data'=>$data
        ]);
    }

    /**
     * @author 侯翔宇
     * @date 2022/11/19  8:59
     * @notes 签到方法并获取相应的积分
     */
    public function getSign(Request $request)
    {
        //接值
        $signId=$request->post('signid');

        $res=Sign::where('id',$signId)->first();
        //这条数据是获取最近有没有进行过签到
        $data=Integrals::where('uid',$request->uid)->orderBy('uid','desc')->first();
        //当前剩余时间
        $date=strtotime(date('Y-m-d'))+24*3600-time();
        $result=Cache::store('redis')->get('sign'.$request->uid);
        if (isset($result))
        {
            return response()->json([
                'error_code'=>0,
                'msg'=>'今日已签到，无法再次签到'
            ]);
        }
        //为空则添加
        if (empty($data))
        {
            //为空则说明第一次添加
            Integrals::create([
                'uid'=>$request->uid,
                'sign_id'=>$signId,
                'status'=>1,
                'Integral'=>$res['points'],
                'date_time'=>time(),
                'number'=>1
            ]);

            Cache::store('redis')->set('sign'.$request->uid,$request->uid,$date);
        }else{
            //当天的时间和上传签到的时间做比较
            $time=time()-$data['date_time'];
            //不为空则判断是否断签
            if ($time>=24*3600 && $time<48*3600)
            {
                //连续签到
                //说明我们仍在有效期内可以续上签到天数
                $number=Integrals::select('number')->where('uid',$request->uid)->first();

                Integrals::where('uid',$request->uid)->update([
                    'sign_id'=>$signId,
                    'status'=>1,
                    'Integral'=>$data['Integral']+$res['points'],
                    'date_time'=>time(),
                    'number'=>$number['number']+1,
                ]);

//                Integrals::create([
//                    'uid'=>$request->uid,
//                    'sign_id'=>$signId,
//                    'status'=>1,
//                    'Integral'=>$data['Integral']+$res['points'],
//                    'date_time'=>time(),
//                    'number'=>$number['number']+1
//                ]);

                if ($data['number']==7)
                {
                    Integrals::where('uid',$request->uid)->update([
                        'sign_id'=>$signId,
                        'status'=>1,
                        'Integral'=>$data['number']+50,
                        'date_time'=>time(),
                        'number'=>0
                    ]);
                }
            }else{
                //断签
                //断签重新生成一条数据并把原本的数据的积分给添加上去
                Integrals::create([
                    'uid'=>$request->uid,
                    'sign_id'=>$signId,
                    'status'=>1,
                    'Integral'=>$data['Integral']+10,
                    'date_time'=>time(),
                    'number'=>1
                ]);
            }
            return response()->json([
                'code'=>0,
               'msg'=>'签到成功'
            ]);
        }
    }
    //
}
