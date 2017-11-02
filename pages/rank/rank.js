var app = getApp();

Page({
    data: {
        dataList: []
    },
    onLoad: function () {
    },
    onShow: function () {
        if (!app.data.rankLoaded) {//未加载数据则加载
            this.get_rank_list();
        }
    },
    onPullDownRefresh: function () { //下拉刷新
        wx.stopPullDownRefresh();
        this.get_rank_list();
    },
    get_rank_list: function(){
        var that = this;
        // app.checkSession();
        // var openid = wx.getStorageSync('openid');
        wx.showNavigationBarLoading();
        wx.request({
            url: app.globalData.url,
            data:{
                type : 'rank'/*,
                openid : openid*/
            },
            method: 'POST',
            header: {
                'content-type': 'application/x-www-form-urlencoded'
            },
            complete: function () {
                wx.hideNavigationBarLoading();
            },
            success: function (res) {
                that.setData({
                    dataList: res.data.list
                });
                app.data.rankLoaded = true;//加载完成
            },
            fail: function (res) {
                app.showInfo(res.errMsg);
            }
        });
    },
    onShareAppMessage: function () {
        return {
            title: '赞赏排行榜',
            desc: '点滴支持,是我继续坚持的动力',
            path: '/pages/rank/rank'
        }
    }
})
