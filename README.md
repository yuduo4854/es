# es
fisrt composer



## 安装

* 推荐使用 `composer` 进行安装。可以使用 composer.json 声明依赖，或者运行下面的命令。SDK 包已经放到这里 [`qiniu/php-sdk`][install-packagist] 。
```bash
$ composer require qiniu/php-sdk
```
* 直接下载安装，SDK 没有依赖其他第三方库，但需要参照 composer 的 autoloader，增加一个自己的 autoloader 程序。

## 运行环境

| Qiniu SDK版本 | PHP 版本 |
|:--------------------:|:---------------------------:|
|          7.x         |  cURL extension,   5.3 - 5.6,7.0 |
|          6.x         |  cURL extension,   5.2 - 5.6 |

## 使用方法

### 上传
```php
use Qiniu\Storage\UploadManager;
use Qiniu\Auth;
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


## 测试

let image=res.detail.all;
    let imageAll=this.data.file;
    image.map(itme=>{
      wx.uploadFile({
        filePath: itme,
        name: 'file',
        url: '',
        success:res=>{
          console.log(res);
        }
      })
    })

## 常见问题