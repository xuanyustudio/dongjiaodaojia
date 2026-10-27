<template>
	<view class="signIn">
		<!-- <uni-nav-bar statusBar fixed dark class="navBar" left-icon="left" :height="'88rpx'" :title="'订单详情'"
			:fontSize="'36rpx'" :color="'#F4F4F4'" :border="false" @clickLeft="back"></uni-nav-bar> -->

		<view class="position-absolute" style="top:0;bottom:calc(374rpx + 108rpx);left:0;right:0;z-index: 1;">
			<map level :circles="circles" :longitude="longitude" :scale="scale" :latitude="latitude" :markers="covers"
				v-show="maphidden" />
		</view>

		<view class="position-absolute b-liuhai" style="right:16rpx;bottom:558rpx">
			<image @tap="chooseLocation" :src="imgUrl+'images/orderImg/img04.png'" mode="" class="img"
				style="width:148rpx;height:148rpx;"></image>
		</view>

		<view class="tool-fixed bg_fff" style="border-top-left-radius:40rpx;border-top-right-radius:40rpx;">
			<view style="color: #46D17E;font-size: 37rpx;padding-left: 35rpx;padding-top: 5rpx;" @click="go">返回</view>
			<view class="p-15">
				<view class="dd-flex">
					<image :src="data.avatar" mode="aspectFill" class="img border-radius-100"
						style="width:88rpx;height:88rpx;"></image>
					<view class="flex1 p-l10">
						<view class="f-s14 f-w700">尾号{{data&&data.username.substr(7)}}
						</view>
						<!-- <view class="f-s12 co_666 p-t3">信用分98 | 好评率95%</view> -->
						<view class="f-s12 co_666 p-t3">信用分{{data.praise}} | 好评率{{data.praise}}%</view>
					</view>
					<image @tap="call(data.username)" :src="imgUrl+'images/orderImg/img03.png'" mode="" class="img"
						style="width:80rpx;height:80rpx;"></image>
					<image @tap="toGaoDe" src="../../static/images/daohang.png" mode="" class="img"
						style="width:80rpx;height:80rpx;margin-left: 20rpx;"></image>
				</view>
				<view class="d-flex align-items-center p-t20">
					<view class="f-s16 f-w700">
						服务时间{{data&&data.appointment_time.substr(10)}}</view>
					<view class="f-s12 bg_000 co_fff border-radius-3 line-h150 p-l5 p-r5 m-l15">时长</view>
					<view class="f-s14 co_666 p-l3">{{data.duration}}分钟</view>
				</view>
				<view class="d-flex justify-content-space-between f-s12 p-t5">
					<view class="p-r20 co_666">
						服务地址：{{data.province}}{{data.city}}{{data.district}}{{data.address}}
					</view>
					<text class="f-s12 co_999">{{data.distance}}km</text>
				</view>
				<view class="dd-flex p-t20 p-b5">
					<view class="border-radius-100 co_fff f-s12 text-center"
						style="width:88rpx;line-height:40rpx;background:#7C94FB;border-bottom-right-radius:0!important;">
						项目
					</view>
					<view class="f-s16 f-w700 p-l5 flex1 p-r20 webkit-line-clamp-1">{{data.service_name}}
					</view>
					<view class="dd-flex f-w700 co_000 f-s20">
						<view class="f-s12 co_666 f-w400 p-t5">预估收入</view>
						<view class="f-s14 p-t3">￥</view>
						<view>{{data.technician_money}}</view>
					</view>
				</view>
			</view>
			<view class="p-15 p-t10 p-b10 d-flex" style="box-shadow:0 -8rpx 16px rgba(0,0,0,.06);">
				<view class="regiter-bottom gray border-radius-6 p-l20 p-r20 flex1" v-if="data.order_status==6">该订单已取消
				</view>

				<template v-if="data.order_status==1 || data.order_status==2">
					<view class="regiter-bottom gray border-radius-6 p-l20 p-r20" @click="cancel" id="quxiao">申请取消
					</view>
					<view class="flex1 regiter-bottom border-radius-6 m-l10" @click="cancel" id="jiedan"
						v-if="data.order_status==1">确认接单</view>
					<view class="flex1 regiter-bottom border-radius-6 m-l10" @click="cancel" id="fuwu"
						v-if="data.order_status==2">开始服务</view>
				</template>

				<view class="regiter-bottom border-radius-6 p-l20 p-r20 flex1" v-if="data.order_status==3">服务进行中</view>
			</view>
			<view class="b-liuhai"></view>
		</view>

		<u-modal :show="show" :showCancelButton='true' :content='content' @cancel="cancerlser" @confirm="confirm">
		</u-modal>

	</view>
</template>

<script>
	// 获取订单详情
	import {
		orderDetails
	} from "./api.js";
	export default {
		data() {
			return {

				//地图的显示与隐藏
				maphidden: true,
				show: false,
				content: '',
				id: '',

				order_sn: 'O2022012452555599',
				data: '',

				imgUrl: this.$store.state.imgUrl,
				circles: [],
				signIn: false, //签到状态
				radius: 2, //区域半径
				latitude: 26.22,
				longitude: 111.63,
				scale: 13,
				address:'',
				covers: [{
					title: 'sss11111',
					latitude: 26.22,
					longitude: 111.63,
					iconPath: this.$store.state.imgUrl + 'dt.png',
					width: "60rpx",
					height: "60rpx",
					callout: {}
				}],
			};
		},
		onLoad(e) {
			if (e.order_sn) this.order_sn = e.order_sn;
			this.maphidden = true
		},
		onShow() {
			this.chushihua();
		},
		methods: {
			toGaoDe() { 
				var _this = this;
				let latitude = Number(_this.data.lat);
				let longitude = Number(_this.data.lng);
				var packageName = 'com.autonavi.minimap';
				var main = plus.android.runtimeMainActivity();
				var packageManager = main.getPackageManager();
				var PackageManager = plus.android.importClass(packageManager)
				var packageInfo = packageManager.getPackageInfo(packageName, PackageManager.GET_ACTIVITIES);
				if (packageInfo) {
					var Uri = plus.android.importClass("android.net.Uri");
					var url = "amapuri://route/plan?sourceApplication=maxuslife" +
						"&sid=A&slat=" + latitude + "&slon=" + longitude + "&sname=我的位置" +
						"&did=B&dlat=" + _this.covers[0].latitude + "&dlon=" + _this.covers[0].longitude + "&dname=" +
						_this.address + "&dev=0&t=0";
					var Intent = plus.android.importClass('android.content.Intent');
					var intent = new Intent();
					intent.setAction(Intent.ACTION_VIEW);
					intent.addCategory(Intent.CATEGORY_DEFAULT);
					var uri = Uri.parse(url);
					//将功能Scheme以URI的方式传入data  
					intent.setData(uri);
					intent.setPackage("com.autonavi.minimap");
					var main = plus.android.runtimeMainActivity();
					main.startActivity(intent);
				} else {
					// alert('未安装' + packageName + '')
					uni.showToast({
						title: `目前导航暂只支持${packageName}`
					})
				}
			},
			go() {
				uni.switchTab({
					url: '/pages/order/order'
				})
			},
			// 页面初始化
			chushihua() {
				this.maphidden = true
				uni.showLoading({
					title: '加载中'
				})
				this.$db.http('order/orderDetails', {
					order_sn: this.order_sn
				}).then(res => {
					if (res.code == 1) {
						uni.hideLoading()
						this.data = res.data
						this.latitude = res.data.lat
						this.longitude = res.data.lng
						this.covers[0].latitude = res.data.lat
						this.covers[0].longitude = res.data.lng
						this.address = res.data.province + res.data.city + res.data.district + res.data.address
						this.covers[0].callout = {
							content: '客户地址：' + res.data.province + res.data.city + res.data.district + res.data
								.address,
							bgColor: '#fff',
							color: '#000',
							fontSize: 14,
							padding: 7,
							borderWidth: 1,
							borderColor: '#e4e4e4',
							borderRadius: 5,
							display: 'ALWAYS',
						}

						// 若订单正在服务中，直接跳转至服务详情
						if (res.data.order_status == 3 || res.data.order_status == 4) {
							uni.redirectTo({
								url: '/page-index-subcontract/orderIng/index?order_sn=' + this.order_sn
							})
						}
					}
				})
			},

			// 选择位置
			chooseLocation() {
				let _this = this
				let latitude = Number(_this.data.lat);
				let longitude = Number(_this.data.lng);
				uni.openLocation({
					latitude: latitude,
					longitude: longitude,
					name: '服务地址',
					address: _this.data.province + _this.data.city + _this.data.district + _this.data.address,
					success: function() {
						console.log('success');
					}
				});
			},

			// 返回上一级
			back() {
				uni.navigateBack({
					delta: 1,
				});
			},

			confirm() {
				this.show = false
				if (this.id == 'jiedan') {
					uni.showLoading({
						title: '加载中'
					})
					this.$db.http('order/orderReceiving', {
						order_sn: this.order_sn
					}).then(res => {
						if (res.code == 1) {
							uni.showToast({
								title: '接单成功'
							})
							setTimeout(() => {
								this.chushihua()
							}, 1000)
							// this.yy = res.data
						}
					})
					// this.url('')
				}
				if (this.id == 'fuwu') {
					uni.showLoading({
						title: '加载中'
					})
					this.$db.http('order/orderService', {
						order_sn: this.order_sn
					}).then(res => {
						if (res.code == 1) {
							uni.hideLoading()
							this.navigateTo('/page-index-subcontract/orderIng/index?order_sn=' + this.order_sn)
							// this.yy = res.data
						}
					})
					// this.url('')
				}
			},
			cancerlser() {
				// 用户点击取消
				this.maphidden = true
				this.show = false
			},

			cancel(e) {
				this.maphidden = false
				let {
					id
				} = e.target;
				this.id = id;
				if (id == 'quxiao') {
					this.navigateTo('/pages/order/cancel?order_sn=' + this.order_sn)
					return
				}
				if (id == 'jiedan') {

					this.content = '是否确认接单？'

					// this.confirm()

				}
				if (id == 'fuwu') {
					this.content = '是否可以开始服务？'
				}
				this.show = true
			},
			call(e) {
				uni.makePhoneCall({
					phoneNumber: e
				});
			},
			navigateTo(e) {
				uni.navigateTo({
					url: e
				})
			}
		},
	};
</script>

<style lang="less">
	map {
		width: 100%;
		height: 100%;
	}

	.signIn {
		width: 100%;
		height: 100%;

		.currentlocation {
			position: relative;
			background: #fff;
			width: 686rpx;
			height: 308rpx;
			border-radius: 16rpx;
			margin: 0 auto;
			padding-left: 24rpx;

			.currentlocation-bg {
				position: absolute;
				right: 0;
				top: 0;
				width: 172rpx;
				height: 172rpx;

				image {
					width: 100%;
					height: 100%;
				}
			}

			.currentlocation-top {
				margin-top: 24rpx;
				display: flex;
				align-items: center;
				height: 88rpx;
				font-size: 32rpx;
				font-weight: bold;
				color: #262626;


				.img {
					width: 44rpx;
					height: 44rpx;
					margin-right: 8rpx;

					image {
						width: 100%;
						height: 100%;
					}
				}
			}

			.currentlocation-center {
				font-size: 28rpx;
				font-weight: 500;
				color: #262626;
			}

			.currentlocation-bottom {
				margin-top: 8rpx;
				font-size: 24rpx;
				font-weight: 400;
				color: #666666;
				margin-bottom: 20rpx
			}

			.currentlocation-clicksignin {
				height: 112rpx;
				width: 100%;
				display: flex;
				align-items: center;
				justify-content: space-between;

				.currentlocation-clicksignin-left {
					height: 100%;
					display: flex;
					align-items: center;
					font-size: 32rpx;
					font-weight: 500;
					color: #46D17E;

					view {
						margin-top: -3rpx;
						margin-right: 10rpx;
					}
				}

				.currentlocation-clicksignin-right {
					width: 160rpx;
					height: 64rpx;
					background: linear-gradient(135deg, #46D17E 0%, #3EC735 100%);
					border-radius: 8rpx;
					margin-right: 24rpx;
					text-align: center;
					line-height: 64rpx;
					font-size: 32rpx;
					font-weight: 500;
					color: #FFFFFF;

				}
			}
		}

		.map-loctionMap {
			margin: 0 auto;
			width: 686rpx;
			height: 792rpx;
			margin-top: 26rpx;
			background: #fff;


			.locationmap-top {
				box-sizing: border-box;
				display: flex;
				align-items: center;
				justify-content: space-between;
				width: 100%;
				height: 96rpx;
				padding: 0 24rpx;
				font-size: 32rpx;
				font-weight: bold;
				color: #262626;

				.locationmap-top-counter {
					box-sizing: border-box;
					width: 176rpx;
					height: 56rpx;
					background: #F7F8F9;
					border-radius: 28rpx;
					display: flex;
					align-items: center;
					justify-content: space-between;
					padding: 0 20rpx;

					.add {
						width: 28rpx;
						height: 28rpx;
						margin-top: -8rpx;

						image {
							width: 100%;
							height: 100%;
						}
					}

					.cut {
						width: 28rpx;
						height: 28rpx;
						margin-top: -30rpx;

						image {
							width: 100%;
							height: 21.4%;
						}
					}

					.num {
						font-size: 28rpx;
						font-weight: 500;
						color: #000000;
					}
				}


			}

			.locationmap-map {
				box-sizing: border-box;
				padding: 24rpx 24rpx 32rpx 24rpx;
				width: 100%;
				height: 695rpx;


			}
		}
	}
</style>
