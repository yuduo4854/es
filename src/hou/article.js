// pages/article/article.js
let page=1;
let limit=0;
Page({

  /**
   * 页面的初始数据
   */
  data: {
    dataList:[]
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad(options) {
    this.getList();
  },

  
  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom() {
    if (page>limit) {
      wx.showToast({
        title: '人家也是有底线的',
      })
      return
    }
    this.getList();
  },
  getList()
  {
    let that=this
    wx.request({
      url: 'http://xy.com/api/list',
      method:"GET",
      data:{
        page:page
      },
      success:res=>{
        let data=that.data.dataList.concat(res.data.data.data)
        limit=res.data.data.last_page
        page++;
        this.setData({
          dataList:data
        })
      }
    })
  }
})