var app = getApp();

Page({
    data: {
        prices: [1, 5, 10, 15, 20, 25]
    },
    onLoad: function () {
        var that = this;
        //调用应用实例的方法获取全局数据
        app.getUserInfo(function (userInfo) {
            //更新数据
            that.setData({
                userInfo: userInfo
            })
        });
    },
    selectItem: function (event) { //选中赞赏金额
        var that = this;
        var total = event.currentTarget.dataset.item;
        that.setData({selected: total});

        app.checkSession();
        var openid = wx.getStorageSync('openid');
        wx.showNavigationBarLoading();
        wx.request({
            url: app.globalData.url,
            data: {
                total: total,
                openid : openid,
                type: 'pay'
            },
            method: 'POST',
            header: {
                'content-type': 'application/x-www-form-urlencoded'
            },
            complete: function () {
                wx.hideNavigationBarLoading();
            },
            success: function (res) {
                if (res.data.resultCode != 0) {
                    app.showInfo(res.data.errMsg);
                    return;
                }
                that.wxpay(res.data.params);
            },
            fail: function (res) {
                app.showInfo(res.errMsg);
                that.setData({selected: 0});//取消选中
            }
        });
    },
    wxpay : function (pay){
        var that = this;
        wx.requestPayment({
            timeStamp: '' + pay.timestamp,
            nonceStr: pay.nonce_str,
            package: pay.package,
            signType: pay.sign_type,
            paySign: pay.pay_sign,
            success: function (res) {
                app.data.rankLoaded = false;//通知排行榜重新加载
                wx.showToast({
                    title: '支付成功,感谢',
                    icon: 'success'
                });
            },
            fail: function (res) {
                wx.showToast({
                    title: '已取消支付',
                    icon: 'success'
                });
            },
            complete: function () {
                that.setData({selected: 0});//取消选中
            }
        });
    },
    onShareAppMessage: function () { //分享
        return {
            title: '赞赏',
            desc: '点滴支持,是我继续坚持的动力',
            path: '/pages/index/index'
        }
    }
});
