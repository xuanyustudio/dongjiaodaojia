<template>
	<view class="page home-bg">
		<uni-nav-bar title="" backgroundColor="white"></uni-nav-bar>
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
		<view class="content-p mt30" style="background-color: #f5f5f5;">
			<u-swiper :list="list1" indicator indicatorMode="line" keyName="image" circular class=" " height="180"
				radius="5">
			</u-swiper>


		</view>
		<!-- 这是修改的地方 -->
		<view class="boxerts">
			<view class="ser">
				<view class="sers" @click="ert()">
					<view style="font-size: 30rpx;font-weight: 600;">上门服务</view>
					<view class="ilitem"></view>
					<view style="display: flex;font-size: 20rpx;align-items: flex-end;padding-left: 150rpx;">More ->
					</view>
				</view>
			</view>
			<view class="ser">
				<view class="sers" @click="ertr()">
					<view style="font-size: 30rpx;font-weight: 600;">到店服务</view>
					<view class="ilitem"></view>
					<view style="display: flex;font-size: 20rpx;align-items: flex-end;padding-left: 150rpx;">More ->
					</view>
				</view>
			</view>
		</view>

		<view class="biox" @click="GOdao()">
			<image style="width: 100%;height: 100%;" src="../../static/icons/one.png"></image>
		</view>
		<view class="bioxser" @click="GOzhuan()">
			<image style="width: 100%;height: 100%;" src="../../static/icons/two.png"></image>
		</view>
		<!-- 	<view class="nav-bd content-p">
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
		</view> -->
		<!-- 	<view class="product-wrapper content-p">
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
							<text
								class="text_18 price-font">{{ item.price }}</text>
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
		</view> -->

		<!-- <wrap-version-update id="623401ba89eefa00015523d2"></wrap-version-update> -->
	</view>
</template>

<script>
	import tmSkeleton from '@/tm-vuetify/components/tm-skeleton/tm-skeleton.vue';
	export default {
		components: {
			tmSkeleton,
		},
		data() {
			return {
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
				list1: [

				],
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
				this.getLat();
			}
		},
		onShow() {
			// if(this.productlList.length == 0){
			// 	this.getLat();
			// }  

		},
		methods: {
			// 修改的
			GOdao() {
				console.log("去往导师招募")
				uni.navigateTo({
					url: '/bundle/pages/settled/settled'
				})
			},
			GOzhuan() {
				console.log("去往邀请好友")
				uni.navigateTo({
					url: '/bundle/pages/share/share'
				})
			},
			opencity() {
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
			},
			ert() {
				// 上门服务
				uni.navigateTo({
					url: "/pages/index/indexs"
				})
			},
			// 到店服务
			ertr() {
				uni.switchTab({

					url: "/pages/technician/technician"
				})
			},

			getLat() {
				console.log("获取位置……")
				let _this = this
				uni.getLocation({
					altitude: false,
					geocode: true,
					// type: 'gcj02',
					success: (res) => {
						console.log(2222222, res)
						let {
							latitude,
							longitude,
							address
						} = res;
						if (!address) {
							address = {}
						}
						address.longitude = longitude;
						address.latitude = latitude;
						let wz = uni.getStorageSync('address_info')
						// if(!wx || wz.district != address.district || !_this.productlList.length){
						uni.setStorageSync('address_info', address)
						_this.city = address.district ? address.district : address.city;
						console.log(_this.city)
						_this.init();
						_this.getWeather();
						// }
					},
					fail: (err) => {
						console.log("获取位置失败")
						uni.getLocation({
							altitude: false,
							geocode: true,
							success: (res) => {
								console.log(2222222, res)
								let {
									latitude,
									longitude,
									address
								} = res;
								if (!address) {
									address = {}
								}
								address.longitude = longitude;
								address.latitude = latitude;
								let wz = uni.getStorageSync('address_info')
								// if(!wx || wz.district != address.district || !_this.productlList.length){
								uni.setStorageSync('address_info', address)
								_this.city = address.district ? address.district : address.city;
								console.log(_this.city)
								_this.init();
								_this.getWeather();
								// }
							},
							fail: (err) => {
								console.log("获取位置失败")
							}
						})
					}
				})
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
						console.log("警告", "天气接口获取失败")
					},
					complete: () => {}
				});
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
					this.productlList.push(...list);
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
	.ilitem {
		width: 30rpx;
		height: 4rpx;
		background-color: #0DA17D;
	}

	.boxerts {
		width: 92%;
		height: 150rpx;
		margin: 30rpx auto;
		display: flex;
		justify-content: space-between;
	}

	.ser {
		border-radius: 20rpx;
		width: 45%;
		height: 100%;
		border: 1px black solid;
	}

	.sers {
		width: 84%;
		height: 80%;
		margin: 15rpx auto;
		display: flex;
		flex-direction: column;
		justify-content: space-around;
	}

	.biox {
		width: 93%;
		height: 300rpx;
		margin: 0 auto;
		background-color: antiquewhite;
		border-radius: 20rpx;
	}

	.bioxser {
		width: 93%;
		height: 300rpx;
		margin: 20rpx auto;
		background-color: antiquewhite;
		border-radius: 20rpx;
	}


	.home-bg {

		height: 324rpx;
		// background: linear-gradient(to right, #31D081, #18B567);
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

		.search_wrapper {
			padding: 0rpx 32rpx 20rpx;
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
