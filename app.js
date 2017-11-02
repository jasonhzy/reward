App({
    data: {
        userInfo: null,
        rankLoaded: false //排行榜已加载
    },
    onLaunch: function () {
        var that = this;
        var openid = wx.getStorageSync('openid') || '';
        if (!openid) {
            wx.login({
                success: function (res) { //登陆获取code
                    that.getOpenId(res.code);
                }
            });
        }
    },
    checkSession : function (){
        var that = this;
        wx.checkSession({
            success: function(){
                //session 未过期，并且在本生命周期一直有效
            },
            fail: function(){
                //登录态过期
                var openid = wx.getStorageSync('openid') || '';
                if (!openid) {
                    wx.login({
                        success: function (res) { //登陆获取code
                            that.getOpenId(res.code);
                        }
                    });
                }
            }
        });
    },
    getOpenId: function (code) {
        var that = this;
        wx.request({
            url: that.globalData.url,
            data: {
                code: code,
                type: 'openid'
            },
            header: {
                'content-type': 'application/x-www-form-urlencoded'
            },
            method: 'POST',
            success: function (res) {
                if (res.data.resultCode != 0) {
                    that.showInfo(res.data.errMsg);
                    return;
                }
                wx.setStorageSync('openid', res.data.openid);
            },
            fail: function () {
                // fail
            },
            complete: function (openid) {
                // complete
            }
        });
    },
    showInfo: function (msg) {
        wx.showModal({
            title: '提示',
            showCancel : false,
            content: msg
        });
    },
    getUserInfo: function (cb) {
        var that = this;
        if (this.globalData.userInfo) {
            typeof cb == "function" && cb(this.globalData.userInfo)
        } else {
            //调用登录接口
            wx.getUserInfo({
                withCredentials: false,
                success: function (res) {
                    that.globalData.userInfo = res.userInfo;
                    typeof cb == "function" && cb(that.globalData.userInfo)
                }
            });
        }
    },
    globalData: {
        userInfo: null,
        url: 'http://localhost/project/reward/data/wxmini.php'
    }
})