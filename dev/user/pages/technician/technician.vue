<template>
	<view class="flex-col page">
		<!-- <uni-nav-bar title="" backgroundColor="white"></uni-nav-bar> -->
		<view class="search_wrapper" style="position: sticky;top: 0;z-index: 1;">
			<view class="search">
				<view class="flex-row">
					<text class="text" @click="opencity">{{ city||'正在获取定位...' }}</text>
					<u-icon class="content_col10" name="arrow-down-fill" color="black" size="14"></u-icon>
					<u--input confirm-type="search" @blur="confirm" @confirm="confirm" class="text_1"
						placeholder="搜索项目/导师" border="none" suffixIcon="search" fontSize=" 28rpx"
						suffixIconStyle="font-size: 25px;color: black" v-model="value"></u--input>
				</view>
			</view>
		</view>
		<view class="flex-col section_1">
			<view class="flex-row group_1">
				<view class="flex-row">
					<image src="../../static/icons/dun.png" class="image_3" />
					<text class="text_2">优质服务</text>
				</view>
				<view class="flex-row group_3">
					<image src="../../static/icons/dun.png" class="image_4" />
					<text class="text_3">随叫随到</text>
				</view>
			</view>
		</view>
		<view class="flex-col list">
			<u-skeleton rows="10" :loading="loading" :title="false" rowsHeight="110" style="margin-top: 30rpx;">
			</u-skeleton>

			<view class="no" v-if="total === 0">
				<zNo></zNo>
			</view>

			<view class="list-item justify-center" @click="toPath(item)" :key="i" v-for="(item, i) in dataList"
				style="flex-shrink:0;">
				<view class="left-section flex-col items-center" :style="{'backgroundImage': `url(${item.avatar})`}"
					@click.stop="showImages(i)">
					<!-- <image
						v-if="item.new"
						src="../../static/my/tnew.png"
						class="image_5" /> -->
					<image v-if="item.hframe==1" style="width: 182rpx;height: 182rpx;position: absolute;top:-2px;"
						src='@/static/images/header.gif' />

					<image v-if="item.hhot==1"
						style="width: 20px;height: 32px;position: absolute;bottom: 5px;right: -5px;"
						src='@/static/images/hot.gif' />
					<image v-if="item.hrocket==1"
						style="width: 25px;height: 45px;position: absolute;top: 0px;right: -5px;"
						src='@/static/images/huojian.gif' />
					<view class="flex-col items-center text-wrapper" style="margin-top: 45rpx;"
						v-if="item.appointment_time !== '休息中'">
						<text>核酸正常</text>
					</view>

					<view class="pos" v-if="item.appointment_time == '休息中'">
						休息中
					</view>
				</view>
				<view class="center-group flex-col">
					<view class="group_4 flex-row">
						<text class="text_5 text_6">{{ item.realname }}</text>
						<image src="../../static/my/auth.png" class="image_7" />
					</view>
					<view class="group_5 flex-row">
						<u-icon v-for="(sitem,sindex) in parseInt(item.praise)" :key="sindex" name="star-fill" size="12"
							color="rgba(243, 151, 53, 1)"></u-icon>
						<text class="text_7">好评{{ item.praise }}分</text>
					</view>
					<view class="group_6 flex-row">
						<view class="left-text-wrapper flex-col" v-if="item.label">
							<text class="text_9">{{ item.label }}</text>
						</view>
						<view class="right-text-wrapper flex-col items-center">
							<text>{{ item.practice }}年经验</text>
						</view>
					</view>
					<view class="group_7 flex-row">
						<image v-if="false" src="" class="image_13" />
						<text v-if="false" class="text_12 text_13">浅墨养生</text>
						<image src="../../static/my/pl.png" class="image_15" />
						<text class="text_14 text_15">{{ item.comment }}评论</text>
						<image src="../../static/my/dang.png" class="image_17" />
						<text class="text_16 text_17">{{ item.sales }}单</text>
					</view>
				</view>
				<view class="right-group flex-col">
					<view class="top-text-wrapper flex-col items-center"
						:class="item.appointment_time == '休息中' ? 'opacity-0':''">
						<text>可约</text>
						<!-- {{ item.appointment_time }} -->
					</view>
					<text class="text_19">距离{{ item.distance }}km</text>
					<view class="bottom-text-wrapper flex-col items-center"
						:class="item.appointment_time == '休息中'? 'gayer':''">
						<text>立即预约</text>
					</view>
				</view>
			</view>
		</view>
		<!--#ifdef H5-->
		<mumu-getlocation ref='muLocation'></mumu-getlocation>
		<!--#endif-->
	</view>
</template>

<script>
	// #ifndef H5
	import permision from "@/js_sdk/wa-permission/permission.js"
	// #endif
	import tmSkeleton from '@/tm-vuetify/components/tm-skeleton/tm-skeleton.vue'
	// #ifdef H5
	import mumuGetlocation from '@/components/mumu-getlocation/mumu-getlocation.vue'
	// #endif
	// 后台没有数据就引入xer.vue  ，后台接口给出数据就引入index.vue
	// import zNo from '@/components/z-no/index.vue'
	import zNo from '@/components/z-no/xer.vue'
	export default {
		components: {
			// #ifdef H5
			mumuGetlocation
			// #endif
		},
		data() {
			return {
				listbJVHRZ5E: [],
				latitude: '',
				longitude: '',
				value: '',
				dataList: [],
				total: null,
				loading: true,
				page: 1,
				city: "",
				local: 0,
				productlList: []
			};
		},
		components: {
			tmSkeleton,
			zNo
		},
		onReachBottom() {
			if (this.loading || this.total == null || this.page >= this.total) {
				return;
			}
			this.page++;
			this.init();
		},

		onLoad() {
			this.dataList = []
			//this.getLat();
			//if (this.productlList.length == 0) {
			// // #ifdef APP-PLUS
			// this.getLat();
			// // #endif

			// #ifndef H5
			console.log('APP加载')
			uni.getSystemInfoSync().platform == "android" ? this.requestAndroidPermission(
				'android.permission.ACCESS_FINE_LOCATION') : this.judgeIosPermission('location');
			// #endif

			// this.getLat();

			// #ifdef H5
			console.log('H5加载')
			this.getLat();
			// #endif

			//}
			// // #ifdef H5
			// this.init();
			// // #endif
		},
		// onShow() {
		// 	console.log(this.local)
		// 	if(this.dataList.length == 0){
		// 		this.init();
		// 	}  
		// },
		methods: {
			async showImages(index) {
				var id = this.dataList[index].id;
				let data = await this.$http.post('api/technician/details', {
					id: id
				});

				var header = data.technician.avatar;
				var album = data.technician.album;
				
				album.push(header);

				uni.previewImage({
					current: index, //预览图片的下标
					urls: album //预览图片的地址，必须要数组形式，如果不是数组形式就转换成数组形式就可以
				})

				console.log('详情', album)
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

				console.log('权限状态', strStatus)

				if (!strStatus) {
					uni.showToast({
						title: '请检查定位权限',
						icon: 'none'
					})
					_this.city = '未知';
					_this.longitude = '116.397128';
					_this.latitude = '39.916527';
					_this.init();
				} else {
					_this.getLat();
				}

			},
			judgeIosPermission: function(permisionID) {
				var _this = this;
				var result = permision.judgeIosPermission(permisionID)
				console.log(result);

				if (!result) {
					uni.showToast({
						title: '请检查定位权限',
						icon: 'none'
					})
					_this.city = '未知';
					_this.longitude = '116.397128';
					_this.latitude = '39.916527';
					_this.init();
				} else {
					_this.getLat();
				}

				// var strStatus = (result) ? "已" : "未"
				// uni.showModal({
				// 	content: permisionID + '权限' + strStatus + "获得授权",
				// 	showCancel: false
				// });
			},
			// getLat() {
			// 	let _this = this
			// 	uni.getLocation({
			// 		altitude: false,
			// 		geocode: true,
			// 		type: 'gcj02',
			// 		success: (res) => {
			// 			console.log(res,"打印的res值获取city")
			// 			_this.local = 1
			// 			// let {
			// 			// 	latitude,
			// 			// 	longitude,
			// 			// 	address
			// 			// } = res;
			// 			this.latitude  = res.latitude
			// 			this.longitude = res.longitude
			// 			// address.longitude = this.longitude;
			// 			// address.latitude = this.latitude;
			// 			let wz = uni.getStorageSync('address_info')
			// 			// if(!wz || wz.district != address.district || !_this.dataList.length){

			// 				// uni.setStorageSync('address_info',address)
			// 				// _this.city = address.district ? address.district : address.city;
			// 			// 	_this.latitude = address.latitude;
			// 			// 	_this.longitude = address.longitude;
			// 			// 	console.log(_this.longitude,"123")
			// 				_this.init();
			// 			// }
			// 		},
			// 		fail: (err) => {
			// 			console.log(err)
			// 		}
			// 	})
			// },

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
							_this.getWeather();
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


				uni.getLocation({
					altitude: false,
					geocode: true,
					type: 'gcj02',
					success: (res) => {

						_this.page = 1;
						_this.dataList = [];
						_this.total = null;


						console.log(res, "打印的res值获取city")
						_this.local = 1
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
							// address.longitude = this.longitude;
							// address.latitude = this.latitude;
							let wz = uni.getStorageSync('address_info')
							// if(!wz || wz.district != address.district || !_this.dataList.length){

							uni.setStorageSync('address_info', address)
							_this.city = address.district ? address.district : address.city;
							_this.latitude = address.latitude;
							_this.longitude = address.longitude;
							console.log(_this.longitude, "123")
							_this.init();
						}
						// }
					},
					fail: (err) => {
						console.log("获取位置失败fail", err);
						_this.city = '未知'
						_this.longitude = '116.397128';
						_this.latitude = '39.916527';
						// console.log(_this.city)
						_this.init();
					}
				})
			},




			init() {

				let obj = {
					lat: this.latitude,
					lng: this.longitude,
					page: this.page
				}

				if (this.value) {
					obj.realname = this.value
				}
				console.log(obj, "打印的obj")
				this.loading = true;
				this.$http.post('api/technician/index', obj).then(res => {
					const list = Array.isArray(res) ? res : (res && res.list) || [];
					const total = Array.isArray(res) ? (list.length ? 1 : 0) : (res && res.total);
					this.dataList.push(...list);
					this.total = total == null ? 0 : total;
					this.dataList.forEach(item => {
						if (item.work_status == 0) {
							item.appointment_time = '休息中';
						}
					})
					this.loading = false;
				}).catch(() => {
					this.loading = false;
					if (this.total == null) {
						this.total = 0;
					}
				})
			},
			confirm() {
				this.page = 1;
				this.dataList = [];
				this.total = null;
				this.init();
			},
			opencity() {
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
						this.longitude = longitude
						this.latitude = latitude;
						let arr = address.match(reg);
						this.city = arr[1];
						uni.setStorageSync('address_info', {
							address,
							longitude,
							latitude,
							city: this.city
						});
						this.confirm();
					}
				})
				// #endif
			},
			toPath(item) {
				console.log(item, "打印的需要值")
				if (item.appointment_time == '休息中') {
					uni.showToast({
						title: '该技师正在休息中',
						icon: 'none'
					})
					return
				}
				let url = `/bundle/pages/home/home?id=${item.id}`;
				uni.navigateTo({
					url,
				})
			}
		},
		onPullDownRefresh() {
			uni.stopPullDownRefresh();
			this.page = 1;
			this.dataList = [];
			this.total = null;
			this.init();
		}
	};
</script>

<style scoped lang="scss">
	.page {
		padding-bottom: 338rpx;
		background-color: #f8f8f8;
		height: 100%;
		width: 100%;

		.no {
			padding-top: 300rpx;
		}

		.search_wrapper {
			// #ifdef H5
			padding: 52rpx 32rpx 20rpx;
			//#endif

			// #ifndef H5
			padding: 102rpx 32rpx 20rpx;
			//#endif
			background-color: white;

			.search {

				padding: 10rpx 24rpx;
				background-color: #f5f5f5;

				.image {
					width: 44rpx;
					height: 44rpx;
				}

				.text {
					color: black;
					font-size: 28rpx;
					line-height: 50rpx;
					white-space: nowrap;
				}

				.text_1 {
					margin-left: 24rpx;
					color: black;
					font-size: 28rpx;
					line-height: 40rpx;
					white-space: nowrap;
				}
			}
		}

		.section_1 {
			padding: 0 32rpx 250rpx;
			background-color: #f5f5f5;



			.group_1 {
				margin-top: 24rpx;
				color: black;
				font-size: 28rpx;
				line-height: 40rpx;
				white-space: nowrap;

				.group_3 {
					margin-left: 54rpx;

					.image_4 {
						margin-bottom: 4rpx;
						width: 28rpx;
						height: 34rpx;
					}

					.text_3 {
						margin-left: 14rpx;
					}
				}

				.image_3 {
					width: 40rpx;
					height: 40rpx;
				}

				.text_2 {
					margin-left: 8rpx;
				}
			}
		}

		.list {
			margin-top: -246rpx;
			padding: 0 32rpx;
			position: relative;

			.list-item {
				padding: 34rpx 0 60rpx;
				padding-left: 10rpx;
				background-color: rgb(255, 255, 255);
				border-radius: 24rpx;

				&:not(:first-of-type) {
					margin-bottom: 25rpx;
				}

				.left-section {
					padding-top: 140rpx;
					color: rgb(255, 255, 255);
					font-size: 24rpx;
					line-height: 34rpx;
					white-space: nowrap;
					border-radius: 50%;

					background-position: 0px 0px;
					background-size: cover;
					background-repeat: no-repeat;
					width: 176rpx;
					height: 176rpx;
					position: relative;

					.image_5 {
						width: 80rpx;
						height: 32rpx;
						position: absolute;
						right: 0;
						top: 0;
					}

					.text-wrapper {
						//#ifdef H5
						padding-bottom: 10rpx;
						//#endif
						// #ifdef APP-PLUS
						padding-bottom: 4rpx;
						//#endif
						background-color: rgb(243, 151, 53);
						border-radius: 18rpx;
						width: 128rpx;
						position: relative;
					}
				}

				.center-group {
					margin-left: 16rpx;
					flex: 1;

					.group_4 {
						color: rgb(0, 0, 0);
						font-size: 32rpx;
						font-weight: 500;
						line-height: 44rpx;
						white-space: nowrap;

						.text_5 {
							color: rgb(0, 0, 0);
							font-size: 32rpx;
							font-weight: 500;
							line-height: 44rpx;
							white-space: nowrap;
						}

						.text_6 {
							color: initial;
							font-size: initial;
							font-weight: initial;
							line-height: initial;
							white-space: initial;
						}

						.image_7 {
							margin: 4rpx 0 4rpx 8rpx;
							width: 90rpx;
							height: 36rpx;
						}
					}

					.group_5 {
						margin-top: 6rpx;
						color: rgb(153, 153, 153);
						font-size: 20rpx;
						line-height: 28rpx;
						white-space: nowrap;

						.image_9 {
							width: 80rpx;
							height: 24rpx;
						}

						.image_11 {
							margin-left: 4rpx;
							width: 52rpx;
							height: 24rpx;
						}

						.text_7 {
							margin-left: 8rpx;
						}
					}

					.group_6 {
						margin-top: 14rpx;
						color: rgb(243, 151, 53);
						font-size: 24rpx;
						line-height: 34rpx;
						white-space: nowrap;

						.left-text-wrapper {
							padding: 2rpx 0 4rpx;
							background-color: rgba(243, 151, 53, 0.16);
							border-radius: 8rpx;
							height: 40rpx;

							.text_9 {
								margin-left: 12rpx;
								margin-right: 10rpx;
							}
						}

						.right-text-wrapper {
							margin-left: 8rpx;
							padding: 2rpx 10rpx 4rpx;
							background-color: rgba(243, 151, 53, 0.16);
							border-radius: 8rpx;
							height: 40rpx;
						}
					}

					.group_7 {
						margin-top: 16rpx;
						color: rgb(153, 153, 153);
						font-size: 20rpx;
						line-height: 28rpx;
						white-space: nowrap;

						.image_13 {
							width: 24rpx;
							height: 24rpx;
						}

						.text_12 {
							margin-left: 4rpx;
						}

						.text_13 {
							margin-left: initial;
						}

						.image_15 {
							margin-left: 12rpx;
							width: 24rpx;
							height: 24rpx;
						}

						.text_14 {
							margin-left: 4rpx;
						}

						.text_15 {
							margin-left: initial;
						}

						.image_17 {
							margin-left: 12rpx;
							width: 24rpx;
							height: 24rpx;
						}

						.text_16 {
							margin-left: 6rpx;
						}

						.text_17 {
							margin-left: initial;
						}
					}
				}

				.right-group {
					margin-left: 10rpx;
					margin-right: 24rpx;

					.top-text-wrapper {
						padding: 4rpx 10rpx 2rpx;
						color: rgb(24, 181, 103);
						font-size: 24rpx;
						line-height: 34rpx;
						white-space: nowrap;
						background-image: linear-gradient(111.2deg,
								rgba(53, 219, 160, 0.12) 0%,
								rgba(53, 219, 160, 0.12) -13.77%,
								rgba(24, 181, 103, 0.12) 111.69%,
								rgba(24, 181, 103, 0.12) 100%);
						border-radius: 6rpx;
					}

					.opacity-0 {
						opacity: 0;
					}

					.text_19 {
						margin-top: 10rpx;
						align-self: flex-end;
						color: rgb(153, 153, 153);
						font-size: 20rpx;
						line-height: 28rpx;
						white-space: nowrap;
					}

					.bottom-text-wrapper {
						align-self: flex-end;
						margin-left: 8rpx;
						margin-top: 48rpx;
						padding: 8rpx 0 6rpx;
						color: rgb(255, 255, 255);
						font-size: 24rpx;
						line-height: 34rpx;
						white-space: nowrap;
						background-image: linear-gradient(116.3deg,
								rgb(53, 219, 160) 0%,
								rgb(53, 219, 160) -13.77%,
								rgb(24, 181, 103) 111.69%,
								rgb(24, 181, 103) 100%);
						border-radius: 24rpx;
						width: 128rpx;
					}

					.gayer {
						background: rgba(228, 228, 228, 1);
					}
				}
			}
		}
	}

	.pos {
		position: absolute;
		width: 100%;
		height: 100%;
		top: 0;
		left: 0;
		background-color: rgba(0, 0, 0, .36);
		display: flex;
		align-items: center;
		justify-content: center;
		color: white;
		font-size: 28rpx;
		border-radius: 50%;
	}
</style>
