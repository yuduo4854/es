# es
fisrt composer

## 七牛云composer安装
composer require qiniu/php-sdk

### 多文件上传
```php
// 引入鉴权类
use Qiniu\Auth;
// 引入上传类
use Qiniu\Storage\UploadManager;
...
    //获取到多条图片的本地路径
        $file=$request->file('file')->getPathname();
        //云存储名称   唯一性
        $name=md5(rand(111,999)).time();
        // 需要填写你的 Access Key 和 Secret Key
        $accessKey = 'accessKey';
        $secretKey = 'secretKey';
        $auth = new Auth($accessKey, $secretKey);
        //七牛云的桶名
        $bucket = 'bucket';
        // 生成上传 Token
        $token = $auth->uploadToken($bucket);
        // 要上传文件的本地路径
        $filePath = $file;
        // 上传到存储后保存的文件名
        $key = $name;
        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new UploadManager();
        // 调用 UploadManager 的 putFile 方法进行文件的上传。
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath, null, 'application/octet-stream', true, null, 'v2');

        if ($err !== null) {
            var_dump($err);
        } else {
            //成功后拼接 可以访问此链接
            return '七牛云外链'.$ret['key'];
        }
...
```


## 小程序端 文件上传
```php 
...
      //获取到要上传的文件
      let image=res.detail.all;
      //通过image.map
      image.map(itme=>{
        wx.uploadFile({
          filePath: itme,
          name: 'file',
          url: '', //自己的域名 方法
          success:res=>{
            console.log(res);
          }
        })
      })
...
```


## 定时任务 
php artisan make:command （文件名称）

## 进入command文件中
```php
//在handle类中记录日志
Log::info('当前时间'.date('Y-m-d H:i:s'));
//进入Kernel.php文件调用生成的文件
$schedule->command('command:name')->everyMinute(); //这个是每分钟执行一次  可以在laravel8 官方手册中查询 任务调度 Shell 调度命令中查看命令进行更改
//通过php artisan schedule:work命令执行（一直执行，手动停止）
//php artisan schedule:run两个命令不同（执行一次）