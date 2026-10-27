<template>
	<view class="mine">
		<!-- 头部绿色背景 -->
		<!-- <view class="header-bg"><image :src="imgUrl+'images/indexImg/slices_one/headerbg.png'" mode="widthFix"></image></view> -->
		<view class="header-bg"></view>
		<!-- 个人信息和综合批评 -->
		<view class="" :style="{height:headHeight+'px'}"></view>
		<view class="infoAndOverallMerit">
			<view class="info" v-if="info">
				<!-- 头像 -->
				<view class="info-left">
					<image :src="info.avatar" mode=""></image>
					<!-- <image src="" mode=""></image> -->
				</view>
				<view class="info-right">
					<view class="info-right-name">
						<view class="name">
							{{xm.realname}}
						</view>
						<!-- <view class="name-icon"></view> -->
					</view>
					<view class="info-right-token" v-if="infos.status">已认证</view>
					<view class="info-right-token"
						@tap="navigateTo('/page-mine-subcontract/personalInformation/personalInformation')" v-else>
						您还没有认证，快去认证吧</view>
				</view>
			</view>
			<view class="info" @tap="navigateTo('/page-mine-subcontract/personalInformation/personalInformation')"
				v-else>
				<!-- 头像 -->
				<view class="info-left" style="overflow: hidden;">
					<!-- #ifdef MP-WEIXIN -->
					<open-data type="userAvatarUrl"></open-data>
					<!-- #endif -->
					<!-- #ifndef MP-WEIXIN -->
					<image src="" mode=""></image>
					<!-- #endif -->
				</view>
				<view class="info-right">
					<view class="info-right-name">
						<view class="name">请在个人信息，设置头像昵称</view>
					</view>
					<view class="info-right-token">在个人信息认证，快去认证吧</view>
				</view>
			</view>
		</view>
		<view style="position: relative;background:#f8f8f8;padding-top:1px;">
			<!-- 综合评分 -->
			<view class="overallMerit">
				<view @click="handeOverallMeritNavTo(item)"
					:class="index==overallMerit.length-1?'overallMerit-item-noneBorder overallMerit-item':'overallMerit-item'"
					v-for="(item,index) in overallMerit" :key="item.describe">
					<!-- 数值 -->
					<view class="numValue">{{item.numValue}}</view>
					<!-- 描述 -->
					<view class="describe">{{item.describe}}</view>
				</view>
			</view>

			<!-- 轮播图任务中心 -->
			<view class="bodyContent">
				<!-- <uni-swiper-dot class="swiper" :info="imgArray"
					:dotsStyles='{"selectedBackgroundColor":"#fff","selectedBorder":"1px rgba(0, 0, 0, .9) solid","backgroundColor":"rgba(255, 255, 255, .3)"}'
					:current="current" field="content">
					<swiper autoplay :indicator-active-color="'#fff'" :interval="5000" circular class="swiper-box">
						<swiper-item class="swiper-item" v-for="(item ,index) in imgArray" :key="index">
							<view class="swiper-item">
								<image :src="imgUrl+item.src" mode=""></image>
							</view>
						</swiper-item>
					</swiper>
				</uni-swiper-dot> -->

				<!-- 任务中心 -->
				<view class="taskCenter">
					<view class="taskCenter-top">
						<view class="taskCenter-top-h3">任务中心</view>
						<view class="taskCenter-top-h4">新手必备任务</view>
					</view>
					<view class="taskCenter-item" v-for="(item,i) in renwu" @tap="rwzx(item)" v-if="(config.showJsInfo && i>-1) || (!config.showJsInfo && i>0)">
						<view class="taskCenter-item-left">
							<view class="taskType">
								<view class="taskType-left">新手任务</view>
								<view class="taskType-right">{{item.title}}</view>
							</view>
							<text>{{item.text}}</text>
						</view>

						<template v-if="!i">
							<view class="toFinish toFinishStatus" v-if="item.border==1">已完成</view>
							<button type="primary" open-type="getPhoneNumber" @getphonenumber="getPhoneNumber"
								class="toFinish"
								style="background:#0DA17D ;color:#fff;width:150rpx;height:64rpx;padding:0;"
								v-else>去完成</button>
						</template>
						<template v-else>
							<view class="toFinish toFinishStatus" v-if="item.border==1">已完成</view>
							<view class="toFinish toFinishStatus" v-else-if="item.border==0">认证中</view>
							<view class="toFinish" v-else>去完成</view>
						</template>
					</view>
				</view>

				<!-- 设置个人信息 -->
				<view class="setUp">
					<view class="setUp-item" v-for="(item,index) in setUpData" :key="item.title"
						@click="clickNavTo(item)"  v-if="(config.showJsInfo && index>-1) || (!config.showJsInfo && index>0)">
						<view class="setUp-item-left">
							<view class="setUp-item-left-icon">
								<image :src="imgUrl+item.icon" mode=""></image>
							</view>
							<view class="setUp-item-left-text">{{item.title}}</view>
						</view>
						<view class="setUp-item-right">
							<view :class="item.active? 'setUp-item-right-text active': 'setUp-item-right-text' ">
								{{item.title=="服务区域"?!infos || !(infos.city || infos.district)?'未设置':(infos.city || infos.district):item.tip}}
							</view>
							<view class="setUp-item-right-icon">
								<image :src="imgUrl+'images/indexImg/slices_two/jiantou.png'" mode=""></image>
							</view>
						</view>
					</view>
					<view class="setUp-item" @click="zhuXiao">
						<view class="setUp-item-left">
							<view class="setUp-item-left-icon">
								<image :src="imgUrl+'images/mineImg/fwxm.png'" mode=""></image>
							</view>
							<view class="setUp-item-left-text">注销账号</view>
						</view>
					</view>
					<view class="setUp-item" @click="Gologin()">
						<view class="setUp-item-left">
							<view class="setUp-item-left-icon">
								<image :src="imgUrl+'images/mineImg/fwxm.png'" mode=""></image>
							</view>
							<view class="setUp-item-left-text">退出登录</view>
						</view>

					</view>
				</view>
			</view>
		</view>
		<u-overlay :show="show" @click="show = false">
			<view class="dd-flex" style="height:90vh;">
				<view class="bg_fff text-center p-b15 border-radius-5">
					<image src="/static/images/wx.jpg" mode="" class="img border-radius-5"
						style="width:500rpx;height:500rpx;"></image>
					<view class="f-s12 co_666">截图保存二维码，微信扫一扫关注公众号</view>
				</view>
			</view>
		</u-overlay>
	</view>
</template>

<script>
	import {
		headHeight,
		getOpenid,
		getMyIndex,
		clearUser
	} from "./api.js"
	export default {
		data() {
			return {
				data: '',
				info: "", //个人信息,
				value: false,
				xm: '', //姓名

				imgUrl: this.$store.state.imgUrl,
				headHeight: 0,
				imgArray: [{
						src: "images/indexImg/slices_three/lb.png"
					},
					{
						src: "images/indexImg/slices_three/lb.png"
					},
					{
						src: "images/indexImg/slices_three/lb.png"
					}
				],
				renwu: [
					// {title:"手机号授权",text:"以免客户无法联系到您，从而错失客户",border:null},
					// {title:"关注公众号",text:"方便接收新订单/状态消息提醒",border:null},
					{
						title: "认证信息",
						text: "实名认证/头像/资质",
						border: null,
						url: '/page-mine-subcontract/personalInformation/personalInformation'
					},
					{
						title: "签署协议",
						text: "入驻平台，签署协议",
						border: null,
						url: '/page-mine-subcontract/agreementSigning/agreementSigning'
					},
					{
						title: "设置服务时间",
						text: "设置可提供服务的时间段",
						border: null,
						url: '/page-index-subcontract/serviceTime/serviceTime'
					},
					{
						title: "设置服务区域",
						text: "设置服务城市和接单地址",
						border: null,
						url: '/page-index-subcontract/signIn/signIn'
					}
				],
				setUpData: [{
						title: "个人信息",
						icon: "images/mineImg/grxx.png",
						tip: "",
						path: "/page-mine-subcontract/personalInformation/personalInformation",
						active: true
					},
					{
						title: "服务项目",
						icon: "images/mineImg/fwxm.png",
						tip: "",
						path: "/page-index-subcontract/serviceItems/serviceItems"
					},
					{
						title: "服务时间",
						icon: "images/mineImg/fwxm.png",
						tip: "",
						path: "/page-index-subcontract/serviceTime/serviceTime"
					},
					{
						title: "服务区域",
						icon: "images/mineImg/fwqy.png",
						tip: "长沙市 未来城小区",
						path: "/page-index-subcontract/signIn/signIn"
					},
					{
						title: "用户评价",
						icon: "images/mineImg/yhpj.png",
						tip: "",
						path: "/page-mine-subcontract/evaluate/evaluate"
					},
					{
						title: "意见反馈",
						icon: "images/mineImg/yjfk.png",
						tip: "",
						path: "/page-mine-subcontract/feedback/feedback"
					},
					// {title: "退出登录",icon: "images/mineImg/yjfk.png",tip: "",path: "/pages/login/login"}
				],
				// 综合评分数据
				overallMerit: [{
						numValue: '0',
						describe: "综合评分",
						path: "/page-mine-subcontract/comprehensiveScore/comprehensiveScore"
					},
					{
						numValue: '0%',
						describe: "成交率",
						path: "/page-mine-subcontract/comprehensiveScore/comprehensiveScore"
					},
					{
						numValue: '0',
						describe: "服务次数",
						path: "/page-mine-subcontract/comprehensiveScore/comprehensiveScore"
					},
					{
						numValue: '0',
						describe: "信用分",
						path: "/page-mine-subcontract/creditscore/creditscore"
					}
				],
				current: 0,
				show: false,
				token: '',
				guid: '',
				infos: {},
				config: { showJsInfo: true, showBj: true }
			}
		},
		onLoad() {
			//#ifndef MP-ALIPAY
			this.headHeight = uni.getSystemInfoSync()['statusBarHeight']
			//#endif
			
		},
		onShow() {
			let token = this.$db.get('token')
			this.token = token
			if (token) {
				this.getMyIndexData()
			}

		},
		computed: {
			currentSite() {
				return this.$store.state.currentSite
			},
		},
		methods: {
			zhuXiao() {
				var that = this;

				uni.showModal({
					title: '提示',
					content: '注销后无法还原，是否继续？',
					success: function(res) {
						if (res.confirm) {

							var guid = that.guid;
							if (!guid) {
								uni.showToast({
									title: '您好，请联系平台客服进行账号注销'
								})

								setTimeout(function() {
									uni.navigateBack();
								}, 1000)
							} else {
								clearUser({'guid':guid}).then(res => {
									uni.showToast({
										title: '注销成功'
									})
									setTimeout(function() {
										uni.clearStorage();
										uni.reLaunch({
											url: '/pages/login/login'
										})
									}, 1000)
								})
							}


						} else if (res.cancel) {
							console.log('用户点击取消');
						}
					}
				});
			},
			//退出登录
			Gologin() {
				let _this = this

				let token = uni.getStorageSync("token")
				console.log(token)
				_this.$db.http('login/logout', {
					token: token
				}).then(res => {
					console.log(res, "请求的res")
					if (res.code == 1) {
						uni.showToast({
							title: "退出成功",
							icon: 'success'
						})
						setTimeout(function() {
							uni.reLaunch({
								url: '/pages/login/login'
							})
						}, 1000)
					}
				})
			},
			getPhoneNumber(e) {
				uni.showLoading({
					title: '授权中'
				})
				let _this = this
				uni.login({
					success(res) {
						if (e.detail.errMsg.indexOf('ok') != -1) {
							let data = {
								sessionKey: _this.$db.get('session_key'),
								encryptedData: e.detail.encryptedData,
								iv: e.detail.iv
							}
							_this.$db.http('login/bindMobile', data).then(res => {
								if (res.code == 1) {
									uni.showToast({
										title: '授权成功'
									})
									setTimeout(() => {
										_this.getMyIndexData()
									}, 1000)
								}
							})
						} else {
							uni.showToast({
								title: '已取消',
								icon: 'none'
							})
						}
					}
				})
			},
			guanzhu() {
				this.$db.http('login/follow').then(res => {

				})
			},
			rwzx(item) {
				if (!this.$db.get('token')) {
					this.navigateTo('/pages/login/login');
					return
				}
				if (item.title == "手机号授权") {
					return
				}
				if (item.title == "关注公众号") {
					// this.guanzhu()
					// this.show = true
				} else if (item.title == "签署协议") {
					if (this.data.technician_identity && this.data.technician_identity.status == 1) {
						this.navigateTo(item.url)
					} else {
						uni.showToast({
							title: '请先完成实名认证，方可签署协议',
							icon: 'none'
						})
						setTimeout(() => {
							this.navigateTo(
								'/page-mine-subcontract/identityAuthentication/identityAuthentication')
						}, 1000)
					}
				} else {
					this.navigateTo(item.url)
				}
			},
			change(e) {
				// console.log('change', e);
				uni.showLoading({
					title: '加载中'
				})
				this.$db.http('index/getTmplIds', {
					date: this.riqi
				}).then(res => {
					console.log(res, "res打印的")
					if (res.code == 1) {
						uni.hideLoading()
						let list = res.data
						this.aaa(list)
					} else {
						this.value = false
					}
				})
			},
			aaa(tmplIds) {
				let _this = this
				uni.requestSubscribeMessage({
					tmplIds,
					success(res) {
						const _o = Object.assign({}, res)
						Reflect.deleteProperty(_o, 'errMsg')
						const a_len = Object.values(_o).filter(s => s === 'accept').length
						if (a_len == tmplIds.length) {
							uni.showToast({
								title: '打开成功'
							})
							_this.value = true
						} else {
							uni.showToast({
								title: '打开失败，建议全部勾选',
								icn: 'none'
							})
							_this.value = false
						}
					},
					fail(err) {
						uni.showToast({
							title: '已取消',
							icon: 'none'
						})
						_this.value = false
					}
				})
			},
			clickNavTo(item) {
				if (!this.$db.get('token')) {
					this.navigateTo('/pages/login/login');
					return
				}
				uni.navigateTo({
					url: item.path
				})
			},
			handeOverallMeritNavTo(item) {
				uni.navigateTo({
					url: item.path
				})
			},
			// 获取个人信息
			getMyIndexData() {
				uni.showLoading({
					title: '加载中'
				})
				let that = this
				let token = uni.getStorageSync("token")
				getMyIndex({
					openid: token
				}).then(res => {
					console.log("加载个人信息", res)
					if (res.code == 1) {
						uni.hideLoading()
						let data = res.data || {}
						let info2 = data.technician_album
						let xm2 = data.technician_identity
						let info22 = data.info || {}
						this.xm = xm2 || { realname: info22.realname || info22.nickname || '' }
						this.data = data
						this.info = info2 || { avatar: info22.avatar || '' }
						this.infos = info22
						this.guid = info22.guid || ''

						//任务信息
						this.renwu.forEach(item => {

							if (item.title == "认证信息") {
								if ((data.technician_identity && data.technician_identity.status ==
										1) &&
									this.info.avatar && (data.qualifications && data.qualifications
										.status == 1)) item.border = 1
								else item.border = null
							}
							if (item.title == "签署协议") {
								if (data.technician_tcp) item.border = data.technician_tcp.status
								else item.border = null
							}
							if (item.title == "设置服务时间") {
								if (info22.week) item.border = 1
								else item.border = null
							}
							if (item.title == "设置服务区域") {
								if (info22.district) item.border = 1
								else item.border = null
							}
						})

						//过滤底部个人信息项
						this.setUpData.forEach(item => {
							if (item.title == "个人信息") {
								let a = 0
								if (this.data.technician_identity) a = a + 20
								if (this.data.technician_album) a = a + 20
								if (this.data.qualifications) a = a + 20
								if (this.data.technician_healthy) a = a + 20
								if (this.data.technician_data) a = a + 20
								if (a == 100) item.tip = "";
								else item.tip = a + '%'
							}
							if (item.title == "服务时间") {
								if (this.infos.end_time && this.infos.start_time) {
									item.tip = this.infos.start_time + "-" + this.infos.end_time
								}
							}
							if (item.title == "服务项目") {
								if (this.infos.service && this.infos.service != "") {
									item.tip = "已设置"
								}
							}
						})
						//过滤信用分项
						let info = res.data.service_index || {}
						let aaa = [info.composite || 0, info.turnover_ratio || 0, info.service_sum || 0, info.credit || 0]
						console.log(aaa, "大一")
						this.overallMerit.forEach((item, i) => {
							console.log(this.overallMerit, "打印的数据2662655")
							if (item.describe == '成交率') item.numValue = aaa[i] + '%'
							else item.numValue = aaa[i]
						})

						uni.setStorage({
							key: "infos",
							data: that.infos
						})
					}

				})
			},
			navigateTo(e) {
				uni.navigateTo({
					url: e
				})
			}
		}
	}
</script>

<style lang="less">
	.mine {
		background: #F8F8F8;

		// 头部绿色背景
		.header-bg {
			position: absolute;
			left: 0;
			top: 0;
			right: 0;
			height: 988rpx;
			background: #0ca27b;

			image {
				width: 100%;
			}
		}

		.infoAndOverallMerit {
			position: relative;
			z-index: 2;
			padding: 100rpx 30rpx 126rpx;
			background: none;

			.info {
				width: 100%;
				height: 112rpx;
				display: flex;
				align-items: center;

				.info-left {
					width: 112rpx;
					height: 112rpx;
					border: 6rpx solid #FFFFFF;
					border-radius: 50%;

					image {
						width: 100%;
						height: 100%;
						border-radius: 50%;
					}
				}

				.info-right {
					margin-left: 20rpx;

					.info-right-name {
						font-size: 32rpx;
						font-weight: bold;
						color: #FFFFFF;
						display: flex;
						align-items: center;

						.name-icon {
							width: 36rpx;
							height: 36rpx;
							background: #FFFFFF;
							border-radius: 50%;
							margin-left: 18rpx;
						}
					}

					.info-right-token {
						padding: 0 30rpx;
						height: 44rpx;
						border: 2rpx solid #FFFFFF;
						border-radius: 24rpx;
						font-size: 24rpx;
						font-weight: 400;
						text-align: center;
						line-height: 44rpx;
						color: #fff;
						margin-top: 16rpx;
						opacity: .7;
					}
				}
			}
		}

		.overallMerit {
			box-sizing: border-box;
			// width: 686rpx;
			height: 166rpx;
			background: #FFFFFF;
			border-radius: 24rpx;
			display: flex;
			padding: 40rpx 0;
			text-align: center;
			justify-content: space-between;
			margin: 0 30rpx;
			margin-top: -86rpx;

			.overallMerit-item {
				border-right: 2rpx solid rgba(0, 0, 0, .1);
				flex: 1;


				.numValue {
					font-size: 36rpx;
					font-weight: bold;
					color: #000000;
					font-family: DINPro;
				}

				.describe {
					font-size: 24rpx;
					font-weight: 400;
					color: #666666;
					margin-top: 6rpx;
				}
			}

			.overallMerit-item-noneBorder {
				border-right: none
			}
		}

		.bodyContent {
			box-sizing: border-box;
			width: 100%;
			padding: 0 32rpx;
			background: #F8F8F8;
			margin-top: 28rpx;

			.swiper {
				height: 150rpx;


				margin-bottom: 20rpx;
				border-radius: 10rpx;


				.swiper-box {
					width: 100%;
					height: 150rpx;
					margin: 0 auto;
					border-radius: 10rpx;
				}

				.swiper-item {
					height: 100%;
					position: relative;

					image {
						width: 100%;
						height: 150rpx;
						border-radius: 10rpx;
					}
				}
			}

			.taskCenter {
				box-sizing: border-box;
				background: #FFFFFF;
				margin-top: 24rpx;
				padding-left: 24rpx;
				border-radius: 16rpx;
				padding-bottom: 24rpx;

				.taskCenter-top {
					height: 95rpx;
					width: 100%;
					display: flex;
					align-items: center;

					.taskCenter-top-h3 {
						font-size: 36rpx;
						font-weight: bold;
						color: #000000;
						margin-top: 30rpx;
					}

					.taskCenter-top-h4 {
						margin-left: 16rpx;
						font-size: 24rpx;
						font-weight: 400;
						color: #999999;
						margin-top: 30rpx;
					}
				}

				.taskCenter-item {
					margin-top: 34rpx;
					display: flex;
					justify-content: space-between;
					align-items: center;
					height: 100rpx;

					.taskCenter-item-left {
						// width: 500rpx;
					}

					.taskType {
						display: flex;
						align-items: center;

						.taskType-left {
							width: 102rpx;
							height: 44rpx;
							background: #FFF1D8;
							border-radius: 12rpx;
							color: #FFB83C;
							font-size: 22rpx;
							text-align: center;
							line-height: 44rpx;
						}

						.taskType-right {
							// width: 128rpx;
							height: 44rpx;
							font-size: 32rpx;
							font-weight: 500;
							color: #000000;
							margin-left: 10rpx
						}
					}

					text {
						font-size: 24rpx;
						font-weight: 400;
						color: #666666;
						margin-top: 12rpx;

					}

					.toFinish {
						width: 142rpx;
						height: 56rpx;
						border: 4rpx solid #0DA17D;
						border-radius: 38rpx;
						text-align: center;
						line-height: 56rpx;
						color: #0DA17D;
						font-size: 30rpx;
						margin-right: 24rpx;

					}

					.toFinishStatus {
						color: #E4E4E4;
						border: 4rpx solid #E4E4E4;
					}
				}

			}

			.setUp {
				width: 686rpx;
				// height: 720rpx;
				border-radius: 16rpx;
				background: #fff;
				margin-top: 24rpx;
				margin-bottom: 36rpx;

				.setUp-item {
					width: 686rpx;
					height: 120rpx;
					border-bottom: 1rpx solid #f8f8f8;
					display: flex;
					justify-content: space-between;
					align-items: center;
				}

				.setUp-item-left {
					display: flex;
					width: 300rpx;
					height: 100%;
					align-items: center;

					.setUp-item-left-icon {
						margin-left: 24rpx;
						width: 36rpx;
						height: 36rpx;

						image {
							width: 100%;
							height: 100%;
							vertical-align: middle;
						}
					}

					.setUp-item-left-text {
						font-size: 32rpx;
						font-weight: bold;
						color: #303030;
						margin-left: 10rpx;
						margin-top: 10rpx;
					}
				}

				.setUp-item-right {
					display: flex;
					height: 100%;
					align-items: center;
					font-size: 28rpx;
					font-weight: 400;
					color: #666666;

					.active {
						color: #FFB83C;
					}

					.setUp-item-right-icon {
						width: 17rpx;
						height: 48rpx;
						margin: 0 24rpx 0 20rpx;
					}

					image {
						width: 100%;
						height: 100%;
					}
				}
			}
		}
	}
</style>
