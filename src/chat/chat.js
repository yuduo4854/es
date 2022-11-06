// pages/chat/chat.js
Page({

  /**
   * 页面的初始数据
   */
  data: {
   data:[],
   records:[]
  },
  sendTime(time = 3000, status = true) {
    if (status == true) {
      let timer = setInterval(function () {
        console.log('心跳已连接');
        wx.sendSocketMessage({
          data: JSON.stringify({
            type: 'type'
          }),
          fail: () => {
            console.log('心跳连接失败');
            wx.closeSocket();
            wx.showToast({
              title: '关闭心跳连接',
              icon: 'none',
              duration: 1500,
            });
            clearInterval(timer)
          }
        });
      }, time)
    }
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
     this.sendTime();
    let that=this
    // 建立链接
    wx.connectSocket({
      url: 'ws://1.116.177.47:9503',
      success:(res)=>{
        // 监听连接数
        wx.onSocketOpen((result) => {
          let msg={
            'to':'miao',
            'my':'yuchen',
            'type':'open',
          }
          // 像服务器发送信息转为json
          that.send(JSON.stringify(msg));
          // 接收服务器返回值
          that.serverMsg();
        })
      }
    })
  },

//发送信息
send:function(res){
  //调用向服务器发送信息函数
  wx.sendSocketMessage({
      data : res
  })
},
//获取输入框信息并发送
sendMyMsg:function(option){
  let myMsg = option.detail.value.text;
  console.log(myMsg);
  let msg = {
      'to' : 'miao',    //发送给谁
      'my' : 'yuchen',    //我是谁
      'type' : 'send',    //当前状态
      'data' : myMsg    //发送信息
  }
  this.send(JSON.stringify(msg));    //调用发送方法，向服务器发送数据
  let data = this.data.records;    //将页面data内的数组records进行调用
  //将用户名称及发送信息追加到数组中
  data.push({
      user : msg.my,
      msg : myMsg
  })
  //进行数据传递
  this.setData({
      records : data
  })
},

//接收服务器返回消息
serverMsg:function(){
  let that = this;
  //调用接收函数，接收服务器返回数据
  wx.onSocketMessage((result)=>{
      //因为在数据库中返回的数据为json数据格式，因此我们需要在前台进行转义
      let parseMsg = JSON.parse(result.data);
      console.log(parseMsg);
      let data = that.data.records;    //调用页面data内的数组records
      console.log(data);
      //接收到的数据追加到数组中
      data.push({
          user : parseMsg.user,
          msg : parseMsg.msg
      })
      //数据传递
      that.setData({
          records : data
      })
  })
}
})