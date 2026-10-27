<template>
	<view class="flex-col page">

		<view class="flex-col section_1">
			<view class="flex-row group">
				<view class="flex-row">
					<image src="../../static/my/anq.png" class="image" />
					<text class="text">优质服务</text>
				</view>
				<view class="flex-row group_2">
					<image src="../../static/my/dao.png" class="image_1" />
					<text class="text_1">随叫随到</text>
				</view>
			</view>
			<view class="flex-row section_2" v-show="!loading">
				<image src="../../static/my/anm.png" class="image_2" />
				<text class="text_2">{{ name }}</text>
			</view>

		</view>
		<!-- <u-skeleton rows="10" :loading="" titleWidth='100%' rowsHeight='26'></u-skeleton> -->
		<view class="flex-col list">
			<view class="list-item justify-center" :key="i" v-for="(item, i) in productlList" @click="toPath(item)">
				<view class="left-section flex-col items-center" style="position: relative;"
					:style="{'backgroundImage': `url(${item.avatar})`}" @click.stop="showImages(i)">
					<image v-show="item.new" src="../../static/my/tnew.png" class="image_3" />

					<image v-if="item.hframe==1" style="width: 182rpx;height: 182rpx;position: absolute;top:-2px;"
						src='@/static/images/header.gif' />

					<image v-if="item.hhot==1"
						style="width: 20px;height: 32px;position: absolute;bottom: 5px;right: -5px;"
						src='@/static/images/hot.gif' />
					<image v-if="item.hrocket==1"
						style="width: 25px;height: 45px;position: absolute;top: 0px;right: -5px;"
						src='@/static/images/huojian.gif' />

					<view class="flex-col items-center text-wrapper" v-if="item.appointment_time != '休息中'">
						<text>核酸正常</text>
					</view>

					<view class="pos" v-if="item.appointment_time == '休息中'">
						休息中
					</view>
				</view>

				<view class="center-group flex-col">
					<view class="group_3 flex-row">
						<text class="text_4 text_5">{{ item.realname }}</text>
						<image src="../../static/my/auth.png" class="image_5" />
					</view>
					<view class="group_4 flex-row">
						<u-icon name="star-fill" v-for="(a,b) in parseInt(item.praise)" size="12"
							color="rgba(243, 151, 53, 1)"></u-icon>
						<text class="text_6">好评{{ item.praise }}</text>
					</view>
					<view class="group_5 flex-row">
						<view class="left-text-wrapper flex-col" v-if="item.label">
							<text class="text_8">{{ item.label }}</text>
						</view>
						<view class="right-text-wrapper flex-col items-center">
							<text>{{ item.practice }}年经验</text>
						</view>
					</view>
					<view class="group_6 flex-row">
						<image v-if="false" src="" class="image_11" />
						<text class="text_11 text_12" v-if="false">浅墨养生</text>
						<image src="../../static/my/pl.png" class="image_13" />
						<text class="text_13 text_14">{{ item.comment }}评论</text>
						<image src="../../static/my/dang.png" class="image_15" />
						<text class="text_15 text_16">{{ item.sales }}单</text>
					</view>
				</view>
				<view class="right-group flex-col" style="align-items:flex-end;">
					<view class="top-text-wrapper flex-col items-center">
						<!-- {{ item.appointment_time }} -->
						<text>{{ item.appointment_time === '休息中' ? '' : '可约' }}</text>
					</view>
					<text class="text_18">距离{{ item.distance }}km</text>
					<view class="bottom-text-wrapper flex-col items-center"
						:class="item.appointment_time == '休息中'? 'gayer':''">
						<text>立即预约</text>
					</view>
				</view>

			</view>

		</view>
	</view>
</template>

<script>
	// #ifndef H5
	import permision from "@/js_sdk/wa-permission/permission.js"
	// #endif
	export default {
		data() {
			return {
				loading: true,
				productlList: [],
				latitude: '',
				longitude: '',
				value: '',
				dataList: [],
				total: null,
				page: 1,
				name: ''
			};
		},
		onReachBottom() {
			if (this.total === this.dataList.length) {
				return;
			}
			// this.page++;
			this.init();
		},
		onLoad(options) {

			this.productlList = []

			let {
				name
			} = options;
			if (name) {
				this.name = name;
			}
			uni.showLoading({
				title: '加载中',
				mask: true
			});

			// #ifndef H5
			uni.getSystemInfoSync().platform == "android" ? this.requestAndroidPermission(
				'android.permission.ACCESS_FINE_LOCATION') : this.judgeIosPermission('location');
			// #endif

			// #ifdef H5
			this.getLat();
			// #endif
		},
		// onShow() {
		// 	console.log(this.local)
		// 	if(this.productlList.length == 0){
		// 		this.init();
		// 	}  
		// },
		methods: {
			async showImages(index) {
				console.log('数组', this.productlList[index])
				var id = this.productlList[index].id;

				let data = await this.$http.post('api/technician/details', {
					id: id
				});

				var header = data.technician.avatar;
				var album = data.technician.album;

				album.push(header);

				uni.previewImage({
					current: 0, //预览图片的下标
					urls: album //预览图片的地址，必须要数组形式，如果不是数组形式就转换成数组形式就可以
				})

				console.log('详情', album)
			},
			getLat() {
				uni.getLocation({
					altitude: false,
					geocode: true,
					// type: 'gcj02',
					success: (res) => {
						this.longitude = res.longitude;
						this.latitude = res.latitude;
						this.init();
					}
				})
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
			},
			init() {
				let order_info = uni.getStorageSync('order_info');
				this.$http.post('api/technician/index', {
					lat: this.latitude,
					lng: this.longitude,
					realname: this.value,
					service_id: order_info.service_id,
					page: this.page
				}).then(res => {
					console.log(res, 'xxxx')
					let {
						list,
						total
					} = res;
					this.productlList.push(...list);

					this.productlList.forEach(item => {
						if (item.work_status == 0)
							item.appointment_time = '休息中';
					})

					this.total = total;
					this.loading = false;
					this.page++
					uni.hideLoading()
				}).catch(Error => {
					uni.hideLoading()
				})
			},
			toPath(item) {
				if (item.appointment_time == '休息中') {
					uni.showToast({
						title: '该技师正在休息中',
						icon: 'none'
					})
					return
				}
				let order_info = uni.getStorageSync('order_info');
				console.log(order_info, "errors")
				order_info.technician_id = item.id;
				uni.setStorageSync('order_info', order_info);
				uni.navigateTo({
					url: `/pages/confirm_order/confirm_order?id=${item.id}`
				})
			},
			onPullDownRefresh() {
				let _self = this
				setTimeout(() => {
					uni.stopPullDownRefresh(); //停止当前页面下拉刷新
					this.page = 1;
					this.dataList = [];
					this.total = null;
					this.init();
				}, 1000);
			},
		},
		onReachBottom() {
			var _this = this;
			_this.init();
		}
	};
</script>

<style scoped lang="scss">
	.page {
		padding-bottom: 10rpx;
		background-color: #f8f8f8;
		height: 100%;
		width: 100%;
		// overflow-y: auto;

		.section_1 {
			padding: 50rpx 32rpx 206rpx;

			background-image: linear-gradient(180deg,
					rgb(49, 208, 129) 0%,
					rgb(49, 208, 129) 2.91%,
					rgb(36, 194, 116) 75.56%,
					rgb(248, 248, 248) 92.1%,
					rgb(248, 248, 248) 100%);

			.group {

				color: rgb(255, 255, 255);
				font-size: 28rpx;
				line-height: 40rpx;
				white-space: nowrap;

				.group_2 {
					margin-left: 54rpx;

					.image_1 {
						margin-bottom: 4rpx;
						width: 28rpx;
						height: 34rpx;
					}

					.text_1 {
						margin-left: 14rpx;
					}
				}

				.image {
					width: 40rpx;
					height: 40rpx;
				}

				.text {
					margin-left: 8rpx;
				}
			}

			.section_2 {

				margin-top: 24rpx;
				padding: 26rpx 24rpx;
				color: rgb(24, 181, 103);
				font-size: 32rpx;
				font-weight: 500;
				line-height: 44rpx;
				white-space: nowrap;
				background-color: rgb(255, 255, 255);
				border-radius: 24rpx 24rpx 0px 0px;

				.image_2 {
					width: 48rpx;
					height: 48rpx;
				}

				.text_2 {
					margin-left: 8rpx;
				}
			}
		}

		.list {


			margin-top: -206rpx;
			padding: 0 32rpx;
			position: relative;

			.list-item:last-of-type {
				border-radius: 0px 0px 24rpx 24rpx;
			}

			.list-item {

				padding: 34rpx 0 30rpx;
				padding-left: 10rpx;
				background-color: rgb(255, 255, 255);
				border-bottom: #F8F8F8 1px solid;

				.left-section {

					padding-top: 140rpx;
					color: rgb(255, 255, 255);
					font-size: 24rpx;
					line-height: 34rpx;
					white-space: nowrap;
					border-radius: 50%;
					background-size: cover;
					background-repeat: no-repeat;
					width: 176rpx;
					height: 176rpx;
					position: relative;

					.image_3 {
						width: 80rpx;
						height: 32rpx;
						position: absolute;
						right: 0;
						top: 0;
					}

					.text-wrapper {
						padding-bottom: 4rpx;
						background-color: rgb(243, 151, 53);
						border-radius: 18rpx;
						width: 128rpx;
						position: relative;
					}
				}

				.center-group {
					margin-left: 16rpx;
					flex: 1;

					.group_3 {
						color: rgb(0, 0, 0);
						font-size: 32rpx;
						font-weight: 500;
						line-height: 44rpx;
						white-space: nowrap;

						.text_4 {
							color: rgb(0, 0, 0);
							font-size: 32rpx;
							font-weight: 500;
							line-height: 44rpx;
							white-space: nowrap;
						}

						.text_5 {
							color: initial;
							font-size: initial;
							font-weight: initial;
							line-height: initial;
							white-space: initial;
						}

						.image_5 {
							margin: 4rpx 0 4rpx 8rpx;
							width: 90rpx;
							height: 36rpx;
						}
					}

					.group_4 {
						margin-top: 6rpx;
						color: rgb(153, 153, 153);
						font-size: 20rpx;
						line-height: 28rpx;
						white-space: nowrap;

						.image_7 {
							width: 80rpx;
							height: 24rpx;
						}

						.image_9 {
							margin-left: 4rpx;
							width: 52rpx;
							height: 24rpx;
						}

						.text_6 {
							margin-left: 8rpx;
						}
					}

					.group_5 {
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

							.text_8 {
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

					.group_6 {
						margin-top: 16rpx;
						color: rgb(153, 153, 153);
						font-size: 20rpx;
						line-height: 28rpx;
						white-space: nowrap;

						.image_11 {
							width: 24rpx;
							height: 24rpx;
						}

						.text_11 {
							margin-left: 4rpx;
						}

						.text_12 {
							margin-left: initial;
						}

						.image_13 {
							margin-left: 12rpx;
							width: 24rpx;
							height: 24rpx;
						}

						.text_13 {
							margin-left: 4rpx;
						}

						.text_14 {
							margin-left: initial;
						}

						.image_15 {
							margin-left: 12rpx;
							width: 24rpx;
							height: 24rpx;
						}

						.text_15 {
							margin-left: 6rpx;
						}

						.text_16 {
							margin-left: initial;
						}
					}
				}

				.right-group {
					margin-left: 10rpx;
					margin-right: 24rpx;

					.top-text-wrapper {
						padding: 4rpx 0 2rpx;
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
						padding: 4rpx 10rpx;
					}

					.text_18 {
						margin-top: 10rpx;
						align-self: flex-end;
						color: rgb(153, 153, 153);
						font-size: 20rpx;
						line-height: 28rpx;
						white-space: nowrap;
					}

					.bottom-text-wrapper {
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
