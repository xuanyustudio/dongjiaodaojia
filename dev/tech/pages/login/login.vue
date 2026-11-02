<template>
	<!-- 登录 -->
	<view class="login">
		<view class="bg"
			style="background-image:url(https://ssdqf.oss-cn-shanghai.aliyuncs.com/admin/202204/202204121451290192104.png)'">
		</view>
		<!-- #ifndef MP-WEIXIN -->
		<view class="title">
			<view class="title-h">
				预约到家
			</view>
			<view class="title-h3">
				未注册过的手机将自动创建账号
			</view>
		</view>

		<view class="phone-form">
			<u--input style="" maxlength="11"  placeholder="输入手机号 (+86)" color="#999" fontSize="28rpx" border="bottom" clearable v-model="username">
			</u--input>
			<!-- #ifndef APP-NVUE -->
			<u-input placeholder="输入验证码" maxlength="6" color="#999" fontSize="28rpx" border="bottom" clearable v-model="sms_code">
				<!-- #endif -->
				<!-- #ifdef APP-NVUE -->
				<u--input placeholder="输入验证码" maxlength="6"  color="#999" fontSize="28rpx" border="bottom" clearable>
					<!-- #endif -->
					<template slot="suffix">
						<view class="getcode">
						<text @click="sendMsg">{{ msg }}</text>
						</view>
						
					</template>
					<!-- #ifndef APP-NVUE -->
			</u-input>
			<!-- #endif -->
			<!-- #ifdef APP-NVUE -->
			</u--input>
			<!-- #endif -->
		</view>
		<button type="default" class="login-btn" @tap="userLogin">登录</button>
		
		<view style="width:100%;text-align:center;margin-top:50rpx;color:#46D17E;z-index: 9999;position: relative;" @click="to('/pages/login/settled')"><text style="text-decoration: underline">我要入驻</text></view>

		<!-- #ifdef MP-WEIXIN -->
		<!-- 其他方式登录 -->
		<view class="other-login">
			<view class="other-login-text">
				其他登录方式
			</view>
			<!-- <view @click="getUserInfo" class="other-login-img">
					<image :src="imgUrl+'wxlogin.png'" mode=""></image>
				</view> -->
		</view>
		<!-- #endif -->
		<!-- #endif -->

		<!-- #ifdef MP-WEIXIN -->
		<view class="other-login p-t5">
			<view class="title f-s18" style="margin-top:60rpx;">
				<image src="https://ssdqf.oss-cn-shanghai.aliyuncs.com/admin/202204/202204121451290192104.png" mode=""
					class="img border-radius-8" style="width:200rpx;height:200rpx;"></image>
				<view class="title-h p-t15" style="text-align: center;">预约到家</view>
			</view>
			<view style="padding-top:100rpx">
				<button type="primary" @click="getUserInfo" class="regiter-bottom h50 f-s16 border-radius-100"
					style="background:#46D17E;">微信授权登录</button>
				<!-- <button type="primary" open-type="getPhoneNumber" @getphonenumber="getPhoneNumber" style="border-radius:100px;background:#46D17E;">微信用户登录</button> -->
			</view>
		</view>
		<!-- #endif -->

		<!-- 服务条款 -->
		<view class="clause b-liuhai">
			点击登录，即表示同意 <text @tap="to('/pages/index/cms?id=tech_privacy')">《隐私政策》</text>和<text
				@tap="to('/pages/index/cms?id=tech_instructions')">《服务条款》</text>
		</view>
	</view>
</template>

<script>
	var clear;
	import {
		login,
		userLogin,
		getOpenid,
		sms
	} from './api.js'; //引入需要的api
	import {
		index
	} from "../index/api.js"
	import {
		mapState,
		mapMutations
	} from 'vuex';
	export default {
		onLoad() {
		
		},
		data() {
			return {
				imgUrl: this.$store.state.imgUrl,
				userInfo: null,
				username:'',
				sms_code:'',
				msg: '获取验证码'
				
			}
		},
		methods: {
			...mapMutations(['setUserInfo']),
			// 获取雅正吗
			sendMsg() {
				if (this.readonly) {
					return;
				}
				if (!this.username) {
					uni.showToast({
						title: '请输入手机号',
						icon: 'none'
					});
					return;
				}
				let moble ={
					mobile: this.username
				}
				sms(moble).then((res) =>{
					this.getCodeState();
				})
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
			// 登录
			userLogin() {
				if(this.check.username(this.username) == false){return}
				if(this.check.sms_code(this.sms_code) == false){return}
				let data = {
					username: this.username,
					sms_code: this.sms_code,
					type: 2
				}
				login(data).then(res => {
					this.$store.commit("changeToken", res.data)
					console.log(res.data)
					uni.setStorage({
							key: 'token',
							
							data: res.data,
							success:()=> {
								uni.reLaunch({
									url: "/pages/index/index"
								})
							}
						},
					);
				}).catch(err => {
					if (err) {
						// uni.clearStorage();
						// uni.reLaunch({
						// 	url:"/pages/login/login"
						// })
					}
				})
			},
			getUserInfo() {
				uni.showLoading({
					title: '授权中'
				})
				let _this = this
				let userInfo = {}
				uni.getUserProfile({
					desc: '登录', // 这个参数是必须的
					success: async res => {
						console.log(res)
						userInfo.nickname = res.userInfo.nickName
						userInfo.avatar = res.userInfo.avatarUrl
						//登录获取openid
						uni.login({
							provider: 'weixin',
							success: function(loginRes) {
								console.log(loginRes)
								let code = loginRes.code
								getOpenid({
									code
								}).then(res => {
									uni.setStorage({
										key: 'openid',
										data: res.msg.openid,
									});
									uni.setStorage({
										key: 'session_key',
										data: res.msg.session_key,
									});
									userInfo.openid = res.msg.openid
									userInfo.unionid = res.msg.unionid
									login(userInfo).then(res2 => {
										if (res2.code == 1) {
											_this.$store.commit("changeToken", res2
												.data)
											uni.setStorage({
												key: 'token',
												data: res2.data
											});
											_this.getMyIndexData()
											uni.reLaunch({
												url: "/pages/index/index"
											})
										} else {
											uni.showToast({
												title: '登录失败，请重新登录',
												icon: 'none'
											})
										}
									})
								})
							}
						});
					},
					fail() {
						uni.showToast({
							title: '登录失败，请重新登录',
							icon: 'none'
						})
					}
				})
			},

			// 获取个人信息
			getMyIndexData() {
				let token = uni.getStorageSync("token")
				this.$db.http('my.Index', token).then(res => {
					if (res.code == 1) {
						uni.setStorage({
							key: "info",
							data: res.data.info
						})
					}
				})
			},

			to(e) {
				console.log('跳转地址',e)
				uni.navigateTo({
					url: e
				})
			}
		}
	}
</script>

<style lang="less">
	.login {
		overflow: hidden;
		background: #fff;
		min-height: 100vh;

		button {
			border: none;
			position: relative;
			display: block;
			margin-left: auto;
			margin-right: auto;
			padding-left: 14px;
			padding-right: 14px;
			box-sizing: border-box;
			font-size: 18px;
			text-align: center;
			text-decoration: none;
			line-height: 2.55555556;
			-webkit-tap-highlight-color: transparent;
			overflow: hidden;
			cursor: pointer;
		}

		.bg {
			position: absolute;
			left: 0;
			right: 0;
			top: 0;
			bottom: 0;
			background-size: cover;

			image {
				width: 100%;
				height: 100%;
			}

		}

		.title {
			position: relative;
			width: 600rpx;
			margin: 0 auto;
			margin-top: 172rpx;

			.title-h {
				font-size: 40rpx;
				font-weight: bold;
				color: #292929;
			}

			.title-h3 {
				font-size: 24rpx;
				font-weight: 400;
				color: #999999;
				margin-top: 6rpx;
			}
		}

		.phone-form {
			position: relative;
			width: 600rpx;
			height: 208rpx;
			display: flex;
			flex-direction: column;
			justify-content: space-between;
			margin: 0 auto;
			margin-top: 110rpx;
		}

		.login-btn {
			position: relative;
			width: 600rpx;
			height: 88rpx;
			background: #46D17E;
			border-radius: 44rpx;
			margin: 0 auto;
			margin-top: 96rpx;
			font-size: 32rpx;
			color: #FFFFFF;
			text-align: center;
			line-height: 88rpx;
		}

		.other-login {
			position: relative;
			width: 600rpx;
			margin: 0 auto;
			text-align: center;
			margin-top: 300rpx;
			font-size: 24rpx;
			font-weight: 400;
			color: #999999;

			.other-login-img {
				display: block;
				width: 96rpx;
				height: 96rpx;
				margin: 0 auto;
				margin-top: 24rpx;

				image {
					width: 100%;
					height: 100%;
				}
			}
		}
	}

	.clause {
		position: absolute;
		width: 600rpx;
		margin: 0 auto;
		font-size: 24rpx;
		font-weight: 400;
		color: #666666;
		left: 0;
		bottom: 20rpx;
		right: 0;

		text {
			color: #46D17E;
		}
	}

	.getcode {
		font-size: 28rpx;
		font-weight: 400;
		color: #000000;
	}
</style>
