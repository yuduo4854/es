<?php
let page=1;
let last_page;
Page({
  /**
   * 页面的初始数据
   */
  data: {
    list:[],
  },
  textData()
  {
  let that=this
    wx.request({
      url: 'http://week.com/api/list',
      data:{
    page
      },
      method:'GET',
      dataType:'json',
      success:res=>{
    let list=that.data.list
        let data=res.data.data.data
        last_page=res.data.data.last_page
        list=list.concat(data)
        //赋值给data里面
        this.setData({
          list:list
        })
        page++
      }
    })
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad(options) {
  page=1
    //获取列表分页需要的数据
    //将两个数据传递给后端
    this.textData(page)
  },

    /**
     * 页面上拉触底事件的处理函数
     */
  onReachBottom() {
      //判断给出提示 是否展示的数据大于总数量
    if(page>last_page)
    {
        wx.showToast({
        title: '别扒拉了，没数据了',
        icon:'error',
      })
      return false;
    }
    this.textData(page);
  },