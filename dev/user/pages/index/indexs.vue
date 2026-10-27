<template>
	<view class="page home-bg">
		<!--#ifndef H5 -->
		<uni-nav-bar title="" backgroundColor="linear-gradient(to right, #31D081, #18B567)"></uni-nav-bar>
		<!--#endif-->

		<!--#ifdef H5 -->
		<view style="width:100%;height: 30rpx;"></view>
		<!--#endif-->
		<view class="head-warpper content-p">
			<view class="nav-head  mt10">
				<view style="margin-top: 5rpx;" @click="showCity">
					<text class="mr5">{{ city||'正在获取定位...' }}</text>
					<u-icon name="arrow-down" color="#FFFFFF" size="16"></u-icon>
				</view>
				<view class="flex" style="margin-top: 5rpx;">
					<text class="mr5" v-if="weather && weather.type">{{ weather.type }}
						{{ weather.low }}-{{ weather.high }}°C{{ weather.date }}</text>
					<text v-else></text>
					<u-icon name="bell" color="#FFFFFF" size="20" @click="toPath('/bundle/pages/message/message')">
					</u-icon>
				</view>
			</view>
		</view>
		<view class="content-p mt30">
			<u-swiper :list="list1" indicator indicatorMode="line" keyName="image" circular class=" " height="180"
				radius="12">
			</u-swiper>
		</view>
		<view class="nav-bd content-p">
			<view class="flex-row justify-bw mt30">
				<view class="nav-bd-item" @click="onClickItem(0)">
					<text :class="current==0?'text-t2':'text-t1'">综合排序</text>
					<image v-show="current==0"
						src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844598500548205.png"
						class="image center" style="width: 30%;height:30% ;" />
				</view>
				<view class="nav-bd-item" @click="onClickItem(1)">
					<text :class="current==1?'text-t2':'text-t1'">价格</text>
					<image v-show="current==1"
						src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844598500548205.png"
						class="image center" style="width: 30%;height:30% ;" />
				</view>
				<view class="nav-bd-item" @click="onClickItem(2)">
					<text :class="current==2?'text-t2':'text-t1'">销量</text>
					<image v-show="current==2"
						src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844598500548205.png"
						class="image center" style="width: 30%;height:30% ;" />
				</view>
				<view class="nav-bd-item" @click="onClickItem(3)">
					<text :class="current==3?'text-t2':'text-t1'">好评度</text>
					<image v-show="current==3"
						src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844598500548205.png"
						class="image center" style="width: 30%;height:30% ;" />
				</view>
			</view>
		</view>
		<view class="product-wrapper content-p">
			<u-skeleton rows="10" :loading="loading" :title="false" rowsHeight="120">
			</u-skeleton>
			<navigator class="product-item flex-row" :key="i" v-for="(item, i) in productlList"
				:url="`../details/details?id=${item.id}`">
				<view class="left-section flex-col">
					<view class="text-wrapper flex-col">
						<u--image :showLoading="true" :src="item.cover" width=" 216rpx" height="182rpx" radius="4"
							:fade="true" duration="450" :lazy-load="true"></u--image>
						<text class="text_6">{{ item.label }}</text>
					</view>
				</view>
				<view class="right-group flex-col">
					<view class="flex-row justify-bw">
						<text class="text_8">{{ item.name }}</text>
						<text class="text_10">销量{{ item.sales }}</text>
					</view>
					<view class="group_7 flex-row">
						<view class="left-text-wrapper flex-col items-center" v-if="item.coupon">
							<text>领券立减</text>
						</view>
						<view class="right-text-wrapper flex-col items-center" v-if="item.discount">
							<text>限时折扣</text>
						</view>
					</view>
					<view class="group_8 flex-row">
						<image
							src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844603777503649.png"
							class="image_5" />
						<text class="text_14">时长{{ item.duration }}分钟</text>
					</view>
					<view class="group_9 flex-row">
						<view class="left-group">
							<text class="text_16">￥</text>
							<text class="text_18 price-font">{{ item.price }}</text>
						</view>
						<view class="center-group flex-col">
							<view class="group_10">
								<text class="text_20">￥</text>
								<text class="text_22 price-font1">{{item.original_price}}</text>
							</view>
							<view class="divider"> </view>
						</view>
						<view class="right-text-wrapper_1 flex-col items-center">
							<text>立即下单</text>
						</view>
					</view>
				</view>
			</navigator>
		</view>

		<!--#ifdef H5-->
		<view style="width:100%;height: 180rpx;"></view>
		<mumu-getlocation ref='muLocation'></mumu-getlocation>
		<!--#endif-->
		<!--#ifdef APP-PLUS-->
		<wrap-version-update ref="updateRef" id="63529bcc702e9d00013847de" @check="handleCheck" @error="handleError"
			@finish="handleFinish"></wrap-version-update>
		<!-- <wrap-version-update id="634023a40f90b70001b4cf06"></wrap-version-update> -->
		<!--#endif-->
 
		<u-popup :show="show" mode="center" @close="close"
			:customStyle="{'width':'90%','margin':'0 auto','border-radius':'30rpx'}">
			<view style="background: #fff;border-radius: 20rpx;padding:20rpx;">
				<view style="height: 80rpx;line-height: 80rpx;text-align: center;font-weight: bold;">权限提示</view>
				<view style="text-align: center;font-size: 24rpx;">您的定位权限未开通，无法正常获取定位计算距离从而导致无法查看和下单。请立即前往打开定位授权后重新启动
				</view>
				<view style="display: flex;margin-top: 30rpx;">
					<u-button type="primary" text="立即前往授权" @click="gotoAppPermissionSetting"></u-button>
				</view>
			</view>
		</u-popup>
	</view>
</template>

<script>
	// #ifndef H5
	import permision from "@/js_sdk/wa-permission/permission.js"
	// #endif
	import tmSkeleton from '@/tm-vuetify/components/tm-skeleton/tm-skeleton.vue';
	// #ifdef H5
	import mumuGetlocation from '@/components/mumu-getlocation/mumu-getlocation.vue'
	// #endif
	export default {
		components: {
			tmSkeleton,
			// #ifdef H5
			mumuGetlocation
			// #endif
		},
		data() {
			return {
				show: false,
				city: "",
				weather: {
					type: "",
					low: '',
					high: '',
					date: ""
				},
				bgColor: {
					"backgroundImage": "linear-gradient(to right, #31D081,#18B567)"
				},
				list1: [],
				page: 1,
				current: 0,
				total: null,
				productlList: [],
				loading: true,
				src: "https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844604317986283.png"
			}
		},
		computed: {

		},
		onLoad() {
			if (this.productlList.length == 0) {
				// #ifdef APP-PLUS
				// this.$refs.updateRef.check();
				//this.getLat();
				// #endif
				// this.getLat();

				// // #ifndef H5
				// uni.getSystemInfoSync().platform == "android" ? this.requestAndroidPermission(
				// 	'android.permission.ACCESS_FINE_LOCATION') : this.judgeIosPermission('location');
				// // #endif

				// // #ifdef H5
				// this.getLat();
				// // #endif
			}
		},
		onShow() {
			// #ifndef H5
			uni.getSystemInfoSync().platform == "android" ? this.requestAndroidPermission(
				'android.permission.ACCESS_FINE_LOCATION') : this.judgeIosPermission('location');
			// #endif
			
			// #ifdef H5
			this.getLat();
			// #endif
				this.clearOrder();
		},
		methods: {
			gotoAppPermissionSetting: function() {
				permision.gotoAppPermissionSetting()
			},
			close() {
				this.show = true;
			},
			async requestAndroidPermission(permisionID) {
				var _this = this;
				var result = await permision.requestAndroidPermission(permisionID)
				var strStatus
				if (result == 1) {
					strStatus = true;
				} else if (result == 0) {
					strStatus = false;
				} else {
					strStatus = false;
				}

				if (!strStatus) {
					// uni.showToast({
					// 	title: '请检查定位权限1',
					// 	icon: 'none'
					// })
					// _this.city = '未知';
					// _this.longitude = '116.397128';
					// _this.latitude = '39.916527';
					// _this.init();
					_this.show = true;
					uni.hideTabBar();
				} else {
					_this.show = false;
					uni.showTabBar();
					if (!_this.productlList || _this.productlList.length <= 0)
					_this.getLat();
				}

			},
			judgeIosPermission: function(permisionID) {
				var _this = this;
				var result = permision.judgeIosPermission(permisionID)
				console.log(result);

				if (!result) {
					// uni.showToast({
					// 	title: '请检查定位权限',
					// 	icon: 'none'
					// })
					// _this.city = '未知';
					// _this.longitude = '116.397128';
					// _this.latitude = '39.916527';
					// _this.init();
					_this.show = true;
					uni.hideTabBar();
				} else {
					_this.show = false;
					uni.showTabBar();
					if (!_this.productlList || _this.productlList.length <= 0)
						_this.getLat();
				}

				// var strStatus = (result) ? "已" : "未"
				// uni.showModal({
				// 	content: permisionID + '权限' + strStatus + "获得授权",
				// 	showCancel: false
				// });
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
			// 获取精准定位
			getH5Lat() {
				var _this = this;
				this.locationRef.getLocation() // 调用 getLocation 方法获取精准定位
					.then(res => {
						// res 就是返回的数据
						console.log('获取位置成功', res)
						var address = {
							"country": res.nation,
							"province": res.province,
							"city": res.city,
							"district": res.district,
							"street": "",
							"streetNum": "",
							"poiName": res.addr,
							"cityCode": res.adcode,
							"longitude": res.lng,
							"latitude": res.lat
						};
						let wz = uni.getStorageSync('address_info')
						uni.setStorageSync('address_info', address)
						_this.city = address.district ? address.district : address.city;
						console.log(_this.city)
						_this.init();
						_this.getWeather();
					})
			},
			initH5Lat() {
				var that = this;
				// 使用 $nextTic 等待组件加载完成调用
				this.$nextTick(() => {
					// 在检测用户是否开启定位权限需要一个过程，推荐使用 login 提示一下
					// uni.showLoading({
					// 	title: '定位组件加载中...',
					// 	mask: true
					// })
					// 初始化组件，并且检测是否开启与授权定位
					this.$refs.muLocation.__init().then(location => {
						// 用户授权了和开启了定位，把定位对象保存到 data 中
						this.locationRef = location;
						that.getH5Lat();
						// uni.hideLoading()
					}, err => {
						// 用户拒绝了定位请求，获取系统没有开启定位功能
						uni.hideLoading()
						if (err.code === 1) {
							uni.showModal({
								title: '获取定位权限失败',
								content: '你拒绝了位置授权服务。请允许当前页面获取定位授权，后刷新页面。'
							})
						} else {
							uni.showModal({
								title: '获取定位权限失败',
								content: '请确定手机定位已打开，并且当前浏览器允许获取定位，都开启后请刷新页面。'
							})
						}
					})

				})
			},
			async getGaoDe(res) {
				var _this = this;
				// await _this.$loc.getLocation(res.longitude, res.latitude);
				var gdinfo = {};
				if (!res.address || !res.address.city) {
					if (res.longitude && res.latitude) {
						// gdinfo = await _this.$loc.getLocation(res.longitude, res.latitude);
						// 	return gdinfo;
						_this.$loc.getLocation(res.longitude, res.latitude).then(res => {
							console.log('高德回调', res);
							uni.setStorageSync('address_info', res)
							_this.city = res.district ? res.district : res.city;
							_this.latitude = res.latitude;
							_this.longitude = res.longitude;
							// console.log(_this.city)
							_this.init();
							//_this.getWeather();
							return res;
						}).catch(e => {
							return e;
						});
					}
					// console.log('高德数据', gdinfo)
				}
				return gdinfo;
			},
			getLat() {
				let _this = this
				console.log("获取位置……")

				// var timer = 5;
				// var inter = setInterval(function() {

				// 	if (_this.latitude && _this.longitude) {
				// 		clearInterval(inter);
				// 	} else {
				// 		timer--;
				// 		if (timer <= 0 && !_this.latitude && !_this.longitude) {
				// 			clearInterval(inter);
				// 			uni.showToast({
				// 				title: '请检查定位权限',
				// 				icon: 'none'
				// 			})
				// 			_this.city = '未知';
				// 			_this.longitude = '116.397128';
				// 			_this.latitude = '39.916527';
				// 			_this.init();
				// 		}
				// 	}
				// }, 1000)


				// setTimeout(function() {

				// 	if (!_this.dataList || _this.dataList.length <= 0) {
				// 		uni.showToast({
				// 			title: '请检查定位权限',
				// 			icon: 'none'
				// 		})
				// 		_this.city = '未知';
				// 		_this.longitude = '116.397128';
				// 		_this.latitude = '39.916527';
				// 		_this.init();
				// 	}

				// }, 5000)


				try {

					uni.getLocation({
						altitude: false,
						geocode: true,
						// type: 'gcj02',
						success: (res) => {
							_this.page = 1;
							_this.dataList = [];
							_this.total = null;
							console.log('获取位置成功', res)
							// var gdinfo = this.getGaoDe(res);
							let {
								latitude,
								longitude,
								address
							} = res;

							if (!address) {
								_this.getGaoDe(res);
								// console.log('gdinfo', gdinfo)
								// address = gdinfo;
							} else {
								address.longitude = longitude;
								address.latitude = latitude;
								console.log('地址', address)
								// console.log('address_info', address)
								//let wz = uni.getStorageSync('address_info')
								// if(!wx || wz.district != address.district || !_this.productlList.length){
								uni.setStorageSync('address_info', address)
								_this.city = address.district ? address.district : address.city;
								console.log(_this.city)
								_this.init();
								//_this.getWeather();
							}

							// if (!address) {
							// 	var gdinfo = this.getGaoDe(res);
							// 	address = gdinfo;
							// }
							// address.longitude = longitude;
							// address.latitude = latitude;
							// console.log('地址', address)
							// // console.log('address_info', address)
							// let wz = uni.getStorageSync('address_info')
							// // if(!wx || wz.district != address.district || !_this.productlList.length){
							// uni.setStorageSync('address_info', address)
							// _this.city = address.district ? address.district : address.city;
							// console.log(_this.city)
							// _this.init();
							// _this.getWeather();
							// }
						},
						fail: (err) => {
							console.log("获取位置失败fail", err);
							_this.city = '未知'
							_this.longitude = '116.397128';
							_this.latitude = '39.916527';
							// console.log(_this.city)
							_this.init();

							// uni.getLocation({
							// 	altitude: false,
							// 	geocode: true,
							// 	// type:'gcj02',
							// 	success: (res) => {
							// 		console.log(2222222, res)
							// 		let {
							// 			latitude,
							// 			longitude,
							// 			address
							// 		} = res;
							// 		if (!address) {
							// 			address = {}
							// 		}
							// 		address.longitude = longitude;
							// 		address.latitude = latitude;
							// 		console.log('address_info', address)
							// 		let wz = uni.getStorageSync('address_info')
							// 		// if(!wx || wz.district != address.district || !_this.productlList.length){
							// 		uni.setStorageSync('address_info', address)
							// 		_this.city = address.district ? address.district : address
							// 			.city;
							// 		console.log(_this.city)
							// 		_this.init();
							// 		_this.getWeather();
							// 		// }
							// 	},
							// 	fail: (err) => {
							// 		console.log("获取位置失败")
							// 	}
							// })
						}
					})
				} catch (err) {
					console.log("获取位置失败", err);
					// uni.showToast({
					// 	title: err,
					// 	icon: 'error'
					// })
				}

			},
			getWeather() {
				// 获取日历
				uni.request({
					url: `http://wthrcdn.etouch.cn/weather_mini?city=${this.city}`,
					method: 'GET',
					success: res => {
						let data = res.data.data.forecast[0];
						let high = data.high;
						let low = data.low;
						let arr = high.split('高温 ');
						data.high = arr[1].split('℃')[0];
						arr = low.split('低温 ');
						data.low = arr[1].split('℃')[0];
						this.weather = data;
					},
					fail: () => {
						// console.log("警告", "天气接口获取失败");

					},
					complete: () => {}
				});
			},
			clearOrder() {
				console.log('无效订单检测')
				this.$http.post('api/order/cleanGarbageData').then(res => {
					console.log('cleanGarbageData处理结果',res)
				})
			},
			// 获取轮播图
			getBanner() {
				this.$http.post('api/index/index').then(res => {

					this.list1 = res;
					console.log(res, 'res')
				})
			},
			// 获取首页列表数据
			getList() {
				this.$http.post('api/index/service', {
					page: this.page,
					orderBy: this.current
				}).then(res => {
					this.loading = false
					let {
						list,
						total
					} = res;
					//this.productlList.push(...list);
					this.productlList = res.list;
					this.total = total;
				})
			},
			init() {
				this.getBanner();
				this.getList();
			},
			onClickItem(e) {
				if (this.current !== e) {
					this.current = e;
					this.page = 1;
					this.productlList = [];
					this.total = null;
					this.getList();
				}
			},
			toPath(url) {
				uni.navigateTo({
					url,
				})
			},
			showCity() {

				// #ifndef H5
				uni.chooseLocation({
					success: (res) => {
						console.log(res)
						let reg = /.+?(省|市|镇|自治区|自治州|县|区)/g;
						let {
							longitude,
							latitude,
							address
						} = res;
						let arr = address.match(reg);
						this.city = arr[1];
						uni.setStorageSync('address_info', {
							address,
							longitude,
							latitude,
							city: this.city
						})
						this.getWeather();
					}
				})
				// #endif
			}
		},
		onNavigationBarButtonTap(e) {
			console.log(e)
			this.$refs.mpvueCityPicker.show();
		},
		onPullDownRefresh() {
			let _self = this
			console.log('refresh');
			setTimeout(() => {
				uni.stopPullDownRefresh(); //停止当前页面下拉刷新
				this.page = 1;
				this.productlList = [];
				this.total = null;
				this.init();
			}, 1000);
		}
	}
</script>

<style lang="scss">
	.home-bg {

		height: 324rpx;
		background: linear-gradient(to right, #31D081, #18B567);
		border-radius: 0px 0px 50rpx 50rpx;

		.head-warpper {
			//position: fixed;

		}

		.nav-head {
			// width: 100vw;
			// position: fixed;
			//background: linear-gradient(to right, #31D081, #18B567);

			//z-index: 1;
			font-size: 30rpx;
			font-weight: 380;
			display: flex;

			view {
				display: flex;
				flex-direction: row;
				align-items: center;
				color: white;
			}

			.flex {
				flex: 1;
				display: flex;
				flex-direction: row;
				flex-wrap: nowrap;

				text {
					flex: 1;
					display: flex;
					justify-content: flex-end;
				}
			}

			.flex_2 {
				flex: 2;
			}
		}

		.nav-bd {
			width: 85%;

			.nav-bd-item {
				width: 150rpx;
				text-align: center;
			}

			.text-t1 {
				font-size: 30rpx;
			}

			.text-t2 {
				font-size: 35rpx;
				font-weight: bold;
			}
		}

		.product-wrapper {
			margin: 30rpx 0;

			.product-item {
				padding: 16rpx 22rpx 24rpx 24rpx;
				background-color: rgb(255, 255, 255);
				border-radius: 28rpx;
				margin: 20rpx 0;

				.left-section {
					//padding-top: 140rpx;
					color: rgb(255, 255, 255);
					font-size: 24rpx;
					line-height: 34rpx;
					white-space: nowrap;
					border-radius: 12rpx;
					//background-image: url('https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844604317986283.png');
					background-size: 100% 100%;
					background-repeat: no-repeat;
					width: 216rpx;
					height: 182rpx;

					.text-wrapper {
						padding: 13rpx 0 0 0;
						position: relative;

						.text_6 {
							text-align: center;
							width: 216rpx;
							position: absolute;
							bottom: 0;
							background-color: rgba(0, 0, 0, 0.36);
							border-radius: 0px 0px 12rpx 12rpx;
							//margin: 0 6rpx;
						}
					}
				}

				.right-group {
					margin-left: 24rpx;
					flex: 1 1 auto;

					.group_7 {
						margin-top: 8rpx;

						.left-text-wrapper {
							padding: 4rpx 0;
							color: rgb(255, 255, 255);
							font-size: 20rpx;
							line-height: 28rpx;
							white-space: nowrap;
							background-color: rgb(243, 151, 53);
							border-radius: 8rpx;
							width: 104rpx;
							height: 36rpx;
							text-align: center;
						}

						.right-text-wrapper {
							margin-left: 6rpx;
							padding: 2rpx 0;
							color: rgb(243, 151, 53);
							font-size: 20rpx;
							line-height: 28rpx;
							white-space: nowrap;
							border-radius: 8rpx;
							width: 104rpx;
							height: 36rpx;
							border: solid 2rpx rgb(243, 151, 53);
							text-align: center;
						}
					}

					.group_8 {
						margin-top: 8rpx;
						color: rgb(102, 102, 102);
						font-size: 24rpx;
						line-height: 34rpx;
						white-space: nowrap;

						.image_5 {
							width: 32rpx;
							height: 32rpx;
						}

						.text_14 {
							margin-left: 4rpx;
						}
					}

					.group_9 {
						margin-top: 16rpx;
						width: 100%;
						display: flex;
						justify-content: space-around;

						.left-group {
							margin-top: 14rpx;
							white-space: nowrap;
							height: 44rpx;

							.text_16 {
								color: rgb(255, 74, 74);
								font-size: 24rpx;
								line-height: 28rpx;
							}

							.text_18 {
								font-family: Price-Bold;
								color: rgb(255, 74, 74);
								font-size: 36rpx;
								line-height: 44rpx;
							}
						}

						.center-group {
							margin-left: 4rpx;
							margin-top: 20rpx;
							position: relative;

							.group_10 {
								line-height: 28rpx;
								white-space: nowrap;

								.text_20 {
									color: rgb(153, 153, 153);
									font-size: 24rpx;
									line-height: 28rpx;
								}

								.text_22 {

									color: rgb(153, 153, 153);
									font-size: 24rpx;
									line-height: 28rpx;
								}
							}

							.divider {
								background-color: rgb(151, 151, 151);
								width: 88rpx;
								height: 2rpx;
								position: absolute;
								left: 2rpx;
								right: 0;
								top: 50%;
								transform: translateY(-50%);
							}
						}

						.right-text-wrapper_1 {


							margin-left: 40rpx;
							margin-top: 6rpx;
							padding: 4rpx 0 2rpx;
							color: rgb(255, 255, 255);
							font-size: 24rpx;
							line-height: 34rpx;

							background-image: linear-gradient(112.4deg,
									rgb(53, 219, 160) 0%,
									rgb(53, 219, 160) -13.77%,
									rgb(24, 181, 103) 111.69%,
									rgb(24, 181, 103) 100%);
							border-radius: 20rpx;
							width: 128rpx;
							height: 40rpx;
							text-align: center;
						}
					}

					.text_8 {
						color: rgb(0, 0, 0);
						font-size: 32rpx;
						font-weight: 500;
						line-height: 44rpx;
						white-space: nowrap;
					}

					.text_10 {
						margin-bottom: 10rpx;
						color: rgb(153, 153, 153);
						font-size: 24rpx;
						line-height: 34rpx;
						white-space: nowrap;
						padding-right: 30rpx;
					}
				}
			}
		}
	}
</style>
