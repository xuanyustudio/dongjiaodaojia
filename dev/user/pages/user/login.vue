<template>
	<view class="login">
		<u-navbar title="" :autoBack="true">
		</u-navbar>
		<view class="o"></view>
		<view class="o1"></view>
		<view class="h1">
			预约到家
		</view>
		<view class="p">未注册过的手机将自动创建账号</view>

		<view class="ipt">
			<input type="number" v-model="phone" placeholder="输入手机号" maxlength="11"
				placeholder-style="color:rgba(153, 153, 153, 1)" />
		</view>

		<view class="ipt">
			<input type="number" v-model="code" placeholder="输入验证码" maxlength="6" />
			<text @click="sendMsg">{{ msg }}</text>
		</view>

		<view class="btn" @click="submit">
			登录
		</view>

		<!-- <view class="other">
			<view v-if="false">	</view>
			<image  v-if="false" class="wx" src="../../static/icon/wx.png" mode=""></image>
			<label @click="moutcl" :class="gouxSta?'cuIcon-squarecheckfill':'cuIcon-square'"></label>
			<view>点击登录，即表示同意<text @click="topath('privacy')">《隐私政策》</text>和<text  @click="topath('service')" >《服务条款》</text></view>
		</view> -->
		<view class="logo_xieyi">
			<label @click="moutcl" :class="gouxSta?'cuIcon-squarecheckfill':'cuIcon-square'"></label>
			<view class="logo_text">请勾选并阅读<text @click="topath('privacy')">《隐私政策》</text>和<text
					@click="topath('service')">《服务条款》</text></view>
		</view>
	</view>
</template>
<script>
	var clear;
	import {
		mapState,
		mapMutations
	} from 'vuex';
	export default {
		data() {
			return {
				phone: '',
				code: '',
				wxcode: '',
				msg: '获取验证码',
				gouxSta: false,
				checked: false,
			}
		},
		onLoad() {
			this.isWeixin = this.isWechat()
			if (this.isWeixin) {
				this.checkWeChatCode() //通过微信官方接口获取code之后，会重新刷新设置的回调地址【redirect_uri】
			}
		},
		methods: {
			/*微信登录相关  start*/
			//方法：用来判断是否是微信内置的浏览器
			isWechat() {
				return String(navigator.userAgent.toLowerCase().match(/MicroMessenger/i)) === "micromessenger";
			},
			//方法:用来提取code
			getUrlCode(name) {
				return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.href) ||
					[, ''
					])[1]
					.replace(/\+/g, '%20')) || null
			},
			//检查浏览器地址栏中微信接口返回的code
			checkWeChatCode() {
				let code = this.getUrlCode('code')
				// uni.showToast({
				// 	title: `微信code=${code}`
				// })
				if (code) {
					this.wxcode = code;
					//this.getOpenidAndUserinfo()
				} else {
					this.getWeChatCode();
				}
			},
			//请求微信接口，用来获取code
			getWeChatCode() {
				// let local = encodeURIComponent(window.location.href); //获取当前页面地址作为回调地址
				// let appid = 'wxf1d7492b1d649594'


				// //通过微信官方接口获取code之后，会重新刷新设置的回调地址【redirect_uri】
				// window.location.href =
				// 	"https://open.weixin.qq.com/connect/oauth2/authorize?appid=" +
				// 	appid +
				// 	"&redirect_uri=" +
				// 	local +
				// 	"&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
				var that = this;

				var http = window.location.protocol;
				var url = window.location.host;
				console.log('网址',http + '//' + url + '/h5/#/pages/user/login')
				let local = encodeURIComponent(http + '//' + url + '/h5/#/pages/user/login'); //获取当前页面地址作为回调地址
				let appid = 'wxf1d7492b1d649594'


				window.location.href =
					"https://open.weixin.qq.com/connect/oauth2/authorize?appid=" +
					appid +
					"&redirect_uri=" +
					local +
					"&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
			},
			//把code传递给后台接口，静默登录
			getOpenidAndUserinfo() {
				var that = this;

				that.$us.loginToH5(this, function(res) {
					console.log('请求登陆接口', res)
					if (res.statusCode == 1) {
						that.$us.setUserInfo(res.data);

						uni.reLaunch({
							url: '/pages/index/index'
						})

					}
				}, this)
			},
			moutcl() {
				if (this.gouxSta == false) {
					this.gouxSta = true
				} else {
					this.gouxSta = false
				}
			},
			...mapMutations(['setUserInfo']),
			// 发送验证码
			topath(e) {
				if (e == "privacy") {
					uni.navigateTo({
						url: "/bundle/pages/web/web?title=privacy"
					})
				} else {
					uni.navigateTo({
						url: "/bundle/pages/web/web?title=service"
					})
				}

			},
			sendMsg() {
				if (this.readonly) {
					return;
				}
				if (!this.phone) {
					uni.showToast({
						title: '请输入手机号',
						icon: 'none'
					});
					return;
				}
				if (!this.$base.phoneRegular.test(this.phone)) {
					uni.showToast({
						title: '手机号格式不正确',
						icon: 'none'
					});
					return;
				}
				this.$http
					.post('api/user/smsCode', {
						mobile: this.phone,
					})
					.then(res => {
						this.getCodeState();
					});
			},
			//验证码按钮文字状态
			getCodeState() {
				clear && clearInterval(clear);
				const _this = this;
				this.readonly = true;
				this.msg = '60S';
				var s = 60;
				clear = setInterval(() => {
					s--;
					_this.msg = s + 'S';
					if (s <= 0) {
						clearInterval(clear);
						_this.msg = '获取验证码';
						_this.readonly = false;
					}
				}, 1000);
			},
			submit() {

				if (!this.phone) {
					uni.showToast({
						title: '请输入手机号',
						icon: 'none'
					});
					return;
				}
				if (!this.$base.phoneRegular.test(this.phone)) {
					uni.showToast({
						title: '手机号格式不正确',
						icon: 'none'
					});
					return;
				}
				if (!this.code) {
					uni.showToast({
						title: '请输入验证码',
						icon: 'none'
					});
					return;
				}
				if (this.gouxSta == false) {
					uni.showToast({
						"title": "请阅读并勾选用户协议",
						"icon": 'none'
					})
					return;
				}

				// #ifdef H5
				if (this.isWeixin && !this.wxcode) {
					uni.showToast({
						"title": "微信授权失败,请关闭页面重试",
						"icon": 'none'
					})
					return;
				}
				// #endif

				// #ifdef H5
				this.$http
					.post('api/user/login', {
						username: this.phone,
						sms_code: this.code,
						code: this.wxcode,
						type: 2
					})
					.then(token => {
						if (!token) {
							uni.showToast({
								title: 'token获取失败',
								duration: 2000
							});
							return;
						}
						uni.setStorageSync('userInfo', {
							token: token
						})
						uni.showToast({
							title: '登录成功',
							duration: 2000,
							success: () => {
								setTimeout(() => {
									uni.reLaunch({
										url: '/pages/index/indexs'
									});
								}, 200);
							}
						});
					});
				// #endif

				// #ifdef APP-PLUS
				this.$http
					.post('api/user/login', {
						username: this.phone,
						sms_code: this.code,
						code: this.wxcode,
						type: 2
					})
					.then(res => {
						console.log(res)
						uni.setStorageSync('userInfo', {
							token: res
						})
						uni.showToast({
							title: '登录成功',
							duration: 2000,
							success: () => {
								setTimeout(() => {
									uni.reLaunch({

										url: '/pages/index/indexs'
									});
								}, 200);
							}
						});
					});
				// #endif
			},
		}
	}
</script>
<style lang="scss" scoped>
	.login_img {
		width: 100%;
		height: auto;
		margin-top: 90upx;
	}

	.login_img image {
		width: 170upx;
		height: 170upx;
		display: block;
		margin: 0px auto;
		border-radius: 50%;
	}

	.login_from {
		width: 100%;
		height: auto;
		box-sizing: border-box;
		padding: 80rpx 8%;
	}

	.login_from_input {
		width: 100%;
		height: auto;
		display: flex;
		flex-direction: row;
		justify-content: space-between;
		align-items: center;
		border-bottom: 2px #ff696c solid;
		padding: 35rpx 0px;
		margin: 0px auto;
	}

	.login_from_name {
		width: 11%;
	}

	.login_from_fun {
		width: 90%;
		display: flex;
		flex-direction: row;
		justify-content: flex-end;
		align-items: center;
	}

	.login_from_fun input {
		width: 100%;
		text-align: left;
		font-size: 14px;
	}


	.login_from_dl {
		width: 88%;
		height: 50px;
		line-height: 50px;
		margin-top: 100rpx;
		margin-left: 45rpx;
	}

	.login_from_dl button {
		width: 100%;
		height: 50px;
		line-height: 50px;
		background: #ff7b6d;
		color: #fff;
		border-radius: 50px;
	}

	.logo_xieyi {
		width: 100%;
		height: 40px;
		line-height: 40px;
		display: flex;
		flex-direction: row;
		margin-top: 100rpx;
		align-items: center;
		color: #444;
		font-size: 14px;
		justify-content: center;
		position: fixed;
		bottom: 74rpx;
	}

	.logo_xieyi label {
		font-size: 18px;
		margin-right: 1%;
		display: block;
		width: 15px;
		height: 15px;
	}

	.cuIcon-squarecheckfill {
		color: #FF3B00;
	}

	.logo_text text {
		color: rgba(24, 181, 103, 1);
	}

	.getyzm {
		width: 60%;
		display: flex;
		flex-direction: row;
		justify-content: flex-end;
		color: #FF3B00;
	}

	.cuicon {
		font-size: 18px;
	}

	.regFrom_tom_yzlabel {
		width: 60%;
		text-align: right;
	}

	.cuIcon-squarecheckfill {
		background: rgba(24, 181, 103, 1);
		position: relative;
		border: 2px #ccc solid;
		box-sizing: border-box;
		border-radius: 3px;
	}

	.cuIcon-square {
		background: #fff;
		border: 2px #ccc solid;
		box-sizing: border-box;
		border-radius: 3px;
	}






	.login {
		height: 100vh;
		background-color: white;
		display: flex;
		flex-direction: column;
		padding: 56rpx 32rpx;

		.o {
			position: fixed;
			width: 692rpx;
			height: 692rpx;
			background-color: rgba(24, 181, 103, .1);
			top: 0rpx;
			right: -346rpx;
			z-index: 999;
			border-radius: 692rpx;
			margin-top: -346rpx;
		}

		.o1 {
			position: fixed;
			width: 880rpx;
			height: 880rpx;
			background-color: rgba(24, 181, 103, 0.05);
			top: 0;
			right: -440rpx;
			z-index: 999;
			border-radius: 50%;
			margin-top: -440rpx;
		}

		.h1 {
			color: rgba(41, 41, 41, 1);
			font-size: 40rpx;
			font-weight: bold;
			margin-top: 150rpx;
			position: relative;
			z-index: 999;
		}

		.p {
			color: rgba(153, 153, 153, 1);
			font-size: 24rpx;
			margin-bottom: 130rpx;
		}

		.ipt {
			display: flex;
			align-items: center;
			padding: 24rpx 0;
			border-bottom: 1rpx solid rgba(0, 0, 0, 0.06);
			margin-bottom: 30rpx;
			position: relative;

			input {
				font-size: 28rpx;
			}

			text {
				position: absolute;
				width: 5em;
				text-align: center;
				top: 50%;
				right: 0;
				transform: translateY(-50%);
				color: rgba(24, 181, 103, 1);
				font-size: 28rpx;
			}
		}

		.btn {
			margin: 94rpx 0 0;
			height: 88rpx;
			background-color: rgba(24, 181, 103, 1);
			border-radius: 44rpx;
			display: flex;
			align-items: center;
			justify-content: center;
			color: white;
			font-size: 32rpx;
		}

		.other {
			position: fixed;
			width: 100%;
			bottom: 74rpx;
			left: 0;
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;

			view:first-of-type {
				color: rgba(153, 153, 153, 1);
				font-size: 24rpx;
			}

			.wx {
				width: 96rpx;
				height: 96rpx;
				margin: 32rpx 0 80rpx 0;
			}

			view:last-of-type {
				font-size: 24rpx;
				color: rgba(102, 102, 102, 1);

				text {
					color: rgba(24, 181, 103, 1);
				}
			}
		}
	}
</style>
