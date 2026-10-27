<template>
	<view class="index">
		<!-- 头部绿色背景 -->
		<view class="header-bg"></view>
		<!-- 头部导航 -->
		<uni-nav-bar statusBar class="navBar" :height="'88rpx'" :backgroundColor="'none'" :fontSize="'36rpx'"
			:color="'#F4F4F4'" :border="false" title="首页"></uni-nav-bar>

		<!-- 头部 -->
		<view class="index-header">
			<!-- 状态 和日期 -->
			<view class="stateAndDay">
				<view class="state"> 
					<text class="state-dian"></text>
					<picker @change="bindPickerChange" :value="index" :range="array">
						<text class="state-text" v-if="technician.work_status==0">休息中</text>
						<text class="state-text" v-else-if="technician.work_status==1">工作中</text>
						<text class="state-text" v-else="technician.work_status==2">禁用中</text>
					</picker>
				</view>
			</view>
			<!-- 首页操作 -->
			<!-- 当前定位 -->
			<view class="curPosition">
				<view class="pos-left">
					<image :src="imgUrl+'images/indexImg/slices_one/weizhi.png'" mode=""></image>
					<text>当前</text>
				</view>
				<view class="pos-text">
					<!-- 返回三口	湖南省长沙市开福区湘雅路绿地…返回三口湖南省长沙市开福区湘雅路绿地… -->
					{{currentSite.province}}{{currentSite.city}}{{currentSite.district}}{{currentSite.address}}
				</view>
				<view class="pos-sign" @click="goSignIn">
					去签到
				</view>
			</view>
		</view>
		
		<!-- <view style="position:relative;background:#f8f8f8;padding-top:1px; border:1px saddlebrown solid" > -->
		<view style="position:relative;background:#f8f8f8;padding-top:1px; " >
			<!-- <Nav/> -->

			<!-- 个人信息和订单 -->
			<InfoAndOrder v-if="technician && technician.id" :config="config" :technician="technician" :info="info" :namerema="namerema"/>
			<!-- 通知 -->
			<view class="index-notice">
				<image :src="imgUrl+'images/indexImg/slices_two/laba.png'" mode="" class="img"
					style="width:30rpx;height:32rpx;margin-right:8rpx;"></image>
				<view class="notice-center">
					<!-- {{tips}}  -->
					<u-notice-bar 	:show-icon="true"
			:scrollable="true"
			:single="true" style="padding: 0rpx 12rpx ;" icon=" " color="#46D17E" bgColor="#E2F4E9"  :text="tips">
					123 
			</u-notice-bar>
				</view>
				<view class="notice-right">
					<!-- <text>10分钟前</text> -->
				</view>

			</view>
			
			
			
			
			<!-- 热门推荐 -->
			<!-- <view class="index-hotRed" v-if="imgArray&&imgArray.length>0"> -->
				<!-- <CommonHead describe="热门推荐" /> -->
				<!-- 轮播 -->
				<!-- <uni-swiper-dot class="swiper" :info="imgArray" -->
					<!-- :dotsStyles='{"selectedBackgroundColor":"#fff","selectedBorder":"1px rgba(0, 0, 0, .9) solid","backgroundColor":"rgba(255, 255, 255, .3)"}' -->
					<!-- :current="current" field="content"> -->
					<!-- <swiper autoplay :indicator-active-color="'#fff'" :interval="5000" circular class="swiper-box" -->
						<!-- @change="change"> -->
						<!-- <swiper-item class="swiper-item" v-for="(item ,index) in imgArray" :key="index"> -->
							<!-- <view class="swiper-item"> -->
								<!-- <image :src="item.src" mode=""></image> -->
								<!-- <image class="img" :src="imgUrl+'images/indexImg/slices_one/headerbg.png'" mode="" -->
									<!-- style="width:100%;height:240rpx;"></image> -->
							<!-- </view> -->
						<!-- </swiper-item> -->
					<!-- </swiper> -->
				<!-- </uni-swiper-dot> -->
			<!-- </view> -->
			
			
			
			
			<!-- 热门服务 -->
			<view class="" style="padding-top:24rpx;">
				<!-- <CommonHead describe="热门服务" /> -->
				<HotService ref="hotService" :phone="phone" />

			</view>
		</view>

		<view class="" style=" height: 30rpx;"></view>
		
		<u-popup round="20rpx" :show="false" mode="center" @close="close">
			<view class="text-center p-30">
				<view class="f-s18 p-b10">您还没有绑定手机号</view>
				<view class="f-s14 line-h150">建议您绑定，以免客户无法联系上您<br> 从而错失订单</view>
				<view class="p-t25"><button type="primary" open-type="getPhoneNumber" @getphonenumber="getPhoneNumber" class="regiter-bottom border-radius-100">授权手机号</button></view>
			</view>
		</u-popup>
		
		<!--#ifdef APP-PLUS-->
		<wrap-version-update ref="updateRef" id="634066f211840a0001ddeba5" @check="handleCheck" @error="handleError"
			@finish="handleFinish"></wrap-version-update>
		<!-- <wrap-version-update id="634023a40f90b70001b4cf06"></wrap-version-update> -->
		<!--#endif-->
	</view>
</template>

<script>
	import {
		mapState,
		mapMutations
	} from 'vuex';
	import Nav from "./components/nav.vue" //扫码 签到操作组件
	import InfoAndOrder from "./components/infoAndOrder.vue" //个性信息和订单
	import CommonHead from "../../components/commonHead/commonHead.vue"; //热门推荐组件
	import HotService from "./components/hotService.vue" //热门服务网格grid
	import {
		index,
		getOpenid,
		setStatus,
		getMyIndex,
		getPhone
	} from "./api.js"
	import store from "../../store/index.js"
	export default {
		components: {
			CommonHead,
			Nav,
			InfoAndOrder,
			HotService,
			imageser:'',
			namerema:''
		},
		data() {
			return {
				imgUrl: this.$store.state.imgUrl,
				tips: "", //通知消息
				topInfoStatus: false, //判断用户拖动滚轮
				imgArray: [],
				current: 0,
				technician: "", //技师
				array: ['休息中', '工作中', ],
				index: 0,
				// tianqi:''
				show: false,
				info:'',// 头像的信息
				namerema:'',
				phone:'',
				config:{ showJsInfo: true, showBj: true }
			}
		},
		onLoad() {
			// this.getIndexData()
			 
			// uni.showToast({
			// 	icon:'none',
			// 	title:'平台客服需要开通im'
			// })
		},
		onShow() {
			let token = uni.getStorageSync('token')
			if(!token){
				// 用 reLaunch 关掉首页。navigateTo 会把首页留在登录页下面，
				// 宫格在隐藏状态下把宽度量成 0，回来后布局就挤在一起。
				uni.reLaunch({
					url: "/pages/login/login"
				})
				return
			}
			this.getIndexData()
			this.getPhones()
			this.resizeHome()
		},
		onReady() {
			this.resizeHome()
		},
		//计算属性获取位置
		computed: {
			currentSite() {
				return this.$store.state.currentSite
			},
		},
		methods: {
			resizeHome() {
				this.$nextTick(() => {
					this.$refs.hotService && this.$refs.hotService.resize()
				})
			},
			// 获取到版本信息都会触发
			handleCheck(res) {
				this.release = res;
			},
			handleFinish(res) {
				if (!res.isTest) {
					// uni.showToast({ 
					// 	title: '已经是最新版',
					// 	icon: 'none'
					// });
				}
			},
			// 断掉流量和WIFI试试
			handleError(err) {
				console.error(err);
			},
			toCheck() {
				// 主动检查更新，一般用于设置界面中
				// 用法：设置auto为false,当用户点击检查更新时，执行此方法
				this.$refs.updateRef.check();
			},
			images(){
				let that = this
				let token = uni.getStorageSync("token")
				getMyIndex(token).then(res => {
					console.log("个人信息",res)
					if(res.code == 1){
					this.info = res.data.technician_album.avatar
					this.namerema = res.data.technician_identity.realname
					
					}
				})
			},
			close(){
				this.show = false
				uni.showToast({title:'后续可在个人中心进行授权',icon:'none',duration:2000})
			},
			getPhoneNumber(e){
				uni.showLoading({title:'授权中'})
				let _this = this
				uni.login({
					success(res) {
						if(e.detail.errMsg.indexOf('ok') != -1){
							let data = {
								sessionKey: _this.$db.get('session_key'),
								encryptedData: e.detail.encryptedData,
								iv: e.detail.iv
							}
							_this.$db.http('login/bindMobile',data).then(res => {
								if(res.code==1){
									uni.showToast({title: '授权成功'})
									setTimeout(()=>{
										_this.show = false
									},1000)
								}
							})
						}else{
							uni.showToast({title: '已取消',icon:'none'})
						}
					}
				})
			},
			
			change(e) {
				this.current = e.detail.current;
			},
			// 跳转签到
			goSignIn() {
				uni.navigateTo({
					url: "/page-index-subcontract/signIn/signIn"
				})
			},

			//切换状态
			bindPickerChange(e) {
				if(!this.$db.get('token')){uni.navigateTo({url: "/pages/login/login"});return}
				if(!this.technician.status){
					uni.showToast({title:'请先前往认证',icon:'none'})
					setTimeout(()=>{
						uni.navigateTo({url: 'page-mine-subcontract/personalInformation/personalInformation'});
					},1000)
					return
				}
				let work_status = e.detail.value
				setStatus({
					work_status
				}).then(res => { //切换状态成功后获取最新的技师信息
					this.getIndexData() //获取技师信息
				})
			},
			// 获取手机号
			getPhones() {
				let that = this
				getPhone().then(res=>{
					const data = res && res[1] && res[1].data && res[1].data.data
					if (data && data.mobile) that.phone = data.mobile
					// console.log(res[1].data.data);
					// that.$emit('phone',res[1].data.data)
					//that.huoquPhone(res[1].data.data)
				})
			},
			...mapMutations(["huoquPhone"]),
			// 获取首页和技师信息
			getIndexData() {
				let that = this
				index().then(res => {
					console.log('获取首页信息',res.data)
					this.imgArray = res.data.banner
					this.tips = res.data.tips
					this.technician = res.data.technician
					this.info = this.technician.avatar || ''
					that.$store.dispatch("actionsChangecurrentSite", res.data.technician)
					this.resizeHome()
					
					if(!res.data.technician.mobile) this.show=true
				}).catch(err => {
					if (err) {
						uni.clearStorage();
						// #ifndef MP-WEIXIN
						// uni.reLaunch({
						// 	url: "/pages/login/login"
						// })
						// #endif
					}
				})
			},

		},

	}
</script>

<style lang="less" scoped>
	.index {
		// margin-top: 100rpx;
		position: relative;
		width: 100%;
		background: #F4F5F9;

		// 头部绿色背景
		.header-bg {
			position: absolute;
			left: 0;
			top: 0;
			width: 100%;
			height: 988rpx;
			background: #0ca27b;

			image {
				width: 100%;
			}
		}

		.navBar {
			position: relative;
			z-index: 6;

		}

		// 头部
		.index-header {
			position: relative;
			// width: 100%;
			// width: 750rpx;
			padding: 0 30rpx 94rpx;

			// 日期和状态
			.stateAndDay {
				width: 100%;
				height: 40rpx;
				display: flex;
				justify-content: space-between;

				.state {
					width: 112rpx;
					height: 38rpx;
					background: #FFFFFF;
					opacity: 0.8;
					border-radius: 30rpx;
					display: flex;
					align-items: center;
					// justify-content: space-around;
					// margin-left: 30rpx;

					.state-dian {
						width: 10rpx;
						height: 10rpx;
						background: #0089FF;
						border-radius: 50%;
						margin-left: 15rpx;
						margin-right: 6rpx;
					}

					.state-text {
						font-size: 22rpx;
						font-family: PingFang SC;
						font-weight: 300;
						line-height: 50rpx;
						color: #333333;
						// margin-right: 10rpx;
					}

				}

				.day {
					// width: 254rpx;
					// height: 25rpx;
					font-size: 26rpx;
					font-family: PingFang SC;
					font-weight: 400;
					// line-height: 37px;
					color: #FFFFFF;
					// margin-right: 30rpx;
				}

			}
		}

		.curPosition {
			width: 686rpx;
			margin: 16rpx auto;
			height: 80rpx;
			background: #FFFFFF;
			border-radius: 16rpx;
			// position: absolute;
			// left:30rpx;right:30rpx;
			// bottom: 30rpx;
			display: flex;
			justify-content: space-between;
			align-items: center;

			.pos-left {
				display: flex;
				align-items: center;
				width: 128rpx;
				height: 52rpx;
				margin-left: 24rpx;
				margin-right: 16rpx;
				font-size: 28rpx;
				// font-family: PingFang SC;
				font-weight: 300;

				background: #E8FAEF;
				border-radius: 32rpx;

				image {
					width: 32rpx;
					height: 32rpx;
					margin-left: 11rpx;
					margin-right: 8rpx;
				}

				text {
					color: #46D17E;
				}


			}

			.pos-text {
				// width: 260rpx;
				flex: 1;
				font-size: 28rpx;
				// font-family: PingFang SC;
				font-weight: 300;
				color: #262626;
				white-space: nowrap;
				text-overflow: ellipsis;
				overflow: hidden;
				margin-right: 20rpx;
			}

			.pos-sign {
				height: 38rpx;
				font-size: 28rpx;
				// font-family: PingFang SC;
				font-weight: 300;
				color: #46D17E;
				opacity: 1;
				margin-right: 30rpx;
			}
		}

		// 通知
		.index-notice {
			position: relative;
			// width: 710rpx;
			padding: 0 24rpx;
			height: 64rpx;
			margin: 0 30rpx;
			margin-top: 20rpx;
			background: #E2F4E9;
			border-radius: 16rpx;
			display: flex;
			align-items: center;


			.notice-left {
				margin-right: 8rpx;

				image {
					width: 32rpx;
					height: 32rpx;
				}
			}

			.notice-center {
				flex: 1;
				padding-right: 120rpx;
				font-size: 28rpx;
				font-family: PingFang SC;
				font-weight: 400;
				line-height: 1;
				color: #46D17E;
				overflow: hidden;

			}


			.notice-right {
				position: absolute;
				right: 20rpx;
				font-size: 24rpx;
				font-family: PingFang SC;
				font-weight: 300;
				color: #ACB0B7;
			}
		}

		// 热门推荐
		.index-hotRed {
			margin-top: 20rpx;
			padding: 0 30rpx;
			margin-top: 20rpx;

			.swiper {
				height: 240rpx;

				.swiper-box {
					width: 100%;
					height: 240rpx;
					margin: 0 auto;
				}

				.swiper-item {
					height: 240rpx;
					position: relative;

					image {
						border-radius: 10rpx;
					}
				}
			}

			.activeClass {
				height: 280rpx;
			}
		}


	}
</style>
