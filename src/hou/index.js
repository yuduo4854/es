// index.js
// 获取应用实例
const app = getApp()

Page({
  data: {
   phone:"",
   code:"",
   buttonStatus:false,
   text:"发送验证码"
   
  },

  onLoad() {
  
  },
  //手机号
  phone(res)
  {
    let phone=res.detail.value
    this.setData({
      phone
    })
  },
  //验证码
  code(res)
  {
    let code=res.detail.value
    this.setData({
      code
    })
  },
  //发送验证码
  buttClick(res)
  {
    let phone=this.data.phone
    let code=this.data.code
    let _that=this
    if (phone=="" ||!/^1[3456789][0-9]{9}$/.test(phone) ) {
      wx.showToast({
        title: '手机号格式输入有误',
      })
      return
    }
    let time=60
    this.setData({
      buttonStatus:true
    })
    let timer=setInterval(function(){
      if (time==0) {
        _that.setData({
          buttonStatus:false,
          text:"点击重新发送",
        })
        clearInterval(timer);return
      }
      _that.setData({
        text:"剩余时间"+time+"秒",
      })
      time--
    },1000)
    wx.request({
      url: 'http://xy.com/api/index',
      method:"POST",
      data:{
        phone
      },
      success:res=>{
        console.log(res)
      }
    })
  },
  //登录
  buttonClick()
  {
    let phone=this.data.phone
    let code=this.data.code
    wx.request({
      url: 'http://xy.com/api/login',
      method:"POST",
      data:{
        phone,
        code
      },
      success:res=>{
        let token=res.data.data
        if (res.data.error_code==0) {
          wx.setStorageSync('token', token)
          wx.navigateTo({
            url: '/pages/article/article',
          })
        }else{
          wx.showToast({
            title: '登录失败',
          })
        }
      }
    })
  }
})
