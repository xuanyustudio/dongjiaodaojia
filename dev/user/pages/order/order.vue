<template>

	<view class="flex-col page">
		<!--#ifndef H5-->
		<u-sticky bgColor="#fff">
		<!--#endif-->
		
			<u-tabs :current="tabsIndex" @click="click" style="background-color: #FFFFFF;" :list="list4" lineWidth="30"
				lineColor="#18B567" :activeStyle="{
								color: '#303133',
								fontWeight: 'bold',
								transform: 'scale(1.05)'
							}" :inactiveStyle="{
								color: '#606266',
								transform: 'scale(1)'
							}" itemStyle="padding-left: 15px; padding-right: 15px; height: 34px;padding-bottom: 5px">
			</u-tabs>
			
		<!--#ifndef H5-->
		</u-sticky>
		<!--#endif-->
		<view class="flex-col group">

			<view class="flex-col">
				<view class="flex-col group_2">
					<view class="section_3"> </view>
					<view class="flex-col list">
						<u-skeleton rows="10" :loading="loading" :title="false" rowsHeight="170"
							style="margin-top: 30rpx;">
						</u-skeleton>
						<view class="no" v-if="total === 0">
							<zNo></zNo>
						</view>


						<!-- 这是数据修改的地方 -->
						<view class="list-item flex-col" :key="i" v-for="(item, i) in dataList"
							@click="onClick($event,item)">
							<view class="top-section justify-between">
								<text
									class="text_7">{{ item.order_status == 0 ? '待付款' : item.order_status == 1 ? '已支付' : item.order_status == 2 ? '待服务' : item.order_status == 3 ? '进行中' : item.order_status == 4 ? '已完成' : item.order_status == 5 ? '已评价':'已取消' }}</text>
								<text class="text_9">服务技术：{{ item.realname ? item.realname : item.nickname }}</text>
							</view>
							<view class="center-group flex-row">
								<image :src="item.cover" class="image" />
								<view class="right-group flex-col">
									<text class="text_11">{{ item.name }}</text>
									<text class="text_13">服务时间：{{ item.appointment_time }}</text>
									<view class="bottom-group">
										<text class="text_15">￥</text>
										<text class="text_17 price-font">{{ item.pay_money }}</text>
									</view>
								</view>
							</view>
							<view class="divider"> </view>
							<view class="bottom-group_1 justify-end">
								<view class="left-text-wrapper flex-col items-center" id="hide"
									v-if="item.order_status <= 2">
									<text id="hide">取消订单</text>
								</view>
								<view class="right-text-wrapper flex-col items-center" id="pay"
									v-if="item.order_status === 0">
									<text id="pay">立即支付</text>
								</view>
								<view class="left-text-wrappe1 flex-col items-center" id='toushu'
									v-if="item.order_status >= 4">
									<text id="toushu">投诉</text>
								</view>
								<view class="left-text-wrappe1 flex-col items-center" id='comment'
									v-if="item.order_status == 4">
									<text id="comment">评价</text>
								</view>
							</view>
						</view>
						<!-- ~ -->
					</view>
				</view>
			</view>
		</view>

		<u-popup :show="showCancel" @close="close" @open="open" mode="bottom">
			<view style="width:100%;background: #fff;">
				<view class="order-item" @click="checkedFund(index)" v-for="(item,index) in refundList">{{item.title}}
					<u-icon v-if="item.checked" name="checkbox-mark" color="#31d081" size="30"></u-icon>
				</view>
				<view class="order-item" v-if="showInput">
					<textarea :auto-height="true" maxlength="100" v-model="inputValue"
						placeholder="请输入您的退款理由5-100字"></textarea>
				</view>
				<view class="order-item"></view>
				<view style="width:90%;margin:0 auto;">
					<u-button type="warning" @click="hide" shape="circle" text="提交申请"></u-button>
				</view>
				<view class="order-item"></view>
			</view>
		</u-popup>

	</view>
</template>

<script>
	import tmSkeleton from '@/tm-vuetify/components/tm-skeleton/tm-skeleton.vue'
	// 后台没有数据就引入xer.vue  ，后台接口给出数据就引入index.vue
	// import zNo from '@/components/z-no/index.vue'
	import zNo from '@/components/z-no/xer.vue'

	export default {
		components: {
			tmSkeleton,
			zNo
		},
		data() {
			//0待付款；1待确认；2待服务；3进行中;4完成；5评价；6取消订单
			return {
				showInput: false,
				inputValue: '',
				refundList: [{
						title: '技师没有联系',
						isInput: false,
						checked: false
					},
					{
						title: '拍错了',
						isInput: false,
						checked: false
					},
					{
						title: '临时有事',
						isInput: false,
						checked: false
					},
					{
						title: '其他',
						isInput: true,
						checked: false
					}
				],

				list4: [{
					name: '全部',
					status: ''
				}, {
					name: '待付款',
					status: 0,
					// badge: {
					// 	isDot: true
					// }
				}, {
					name: '待确认',
					status: 1,
				}, {
					name: '进行中',
					status: 3,

				}, {
					name: '已完成',
					status: 4,
				}, {
					name: '已取消',
					status: 6,
				}],
				loading: true,
				tabsIndex: 0,
				page: 1,
				dataList: [],
				total: null,
				showCancel: false,
				thisItem: []
			};
		},
		onLoad() {
			let active = uni.getStorageSync('tabsIndex');
			if (active) {
				this.tabsIndex = Number(active);
				uni.removeStorageSync('tabsIndex')
			}
			uni.$u.sleep(1000).then(() => {
				this.init();
			})
		},
		onPullDownRefresh() {
			uni.stopPullDownRefresh();
			this.page = 1;
			this.dataList = [];
			this.total = null;
			this.init();
		},
		onReachBottom() {
			if (this.loading || this.total == null || this.page >= this.total) {
				return;
			}
			this.page++;
			this.init();
		},
		methods: {
			checkedFund(index) {
				this.refundList.forEach(function(item) {
					item.checked = false;
				})

				this.refundList[index].checked = true;
				this.showInput = this.refundList[index].isInput;
				this.inputValue = '';
			},
			open() {

			},
			close() {
				this.showCancel = false;
			},
			init() {
				this.loading = this.dataList.length === 0;
				this.$http.post('api/order/orderList', {
					order_status: this.list4[this.tabsIndex].status,
					page: this.page
				}).then(res => {
					const list = Array.isArray(res) ? res : (res && res.list) || [];
					const total = Array.isArray(res) ? (list.length ? 1 : 0) : (res && res.total);
					this.dataList.push(...list);
					this.total = total == null ? 0 : total;
					this.loading = false;
				}).catch(() => {
					this.loading = false;
					if (this.total == null) {
						this.total = 0;
					}
				})
			},
			click(e) {
				let {
					index
				} = e;
				this.tabsIndex = index;
				this.page = 1;
				this.dataList = [];
				this.init();
			},
			onClick(e, item) {
				console.log('onClick全部内容', e)
				let id = e.target.id;
				if (id === 'hide') {
					// this.hide(item);\
					this.showCancel = true;
					this.thisItem = item
					return
				}
				if (id === 'pay') {

				}
				console.log(item.id)
				if (id === 'toushu') {
					uni.navigateTo({
						url: '/bundle/pages/complaint/complaint?order_id=' + item.id
					})
					return
				}
				if (id === 'comment') {
					uni.navigateTo({
						url: '/bundle/pages/orderComment/orderComment?order_sn=' + item.order_sn
					})
					return
				}
				uni.navigateTo({
					url: '/bundle/pages/order_details/order_details?order_sn=' + item.order_sn
				})
			},
			hide() {
				uni.showModal({
					content: '是否确认取消订单？',
					success: (res) => {
						console.log(res)
						if (res.confirm) {
							//cancelOrder
							console.log('取消订单数据', {
								order_sn: this.thisItem.order_sn,
								refreason: (!this.inputValue ? this.refundList.find(_ => _.checked)
									.title : this.inputValue)
							})
							this.$http.post('api/order/confirmCancel', {
								order_sn: this.thisItem.order_sn,
								refreason: (!this.inputValue ? this.refundList.find(_ => _.checked)
									.title : this.inputValue)
							}).then(res => {
								console.log('取消订单成功', res)
								uni.showToast({
									icon: 'none',
									title: '取消成功'
								});

								// this.$http.post('api/order/confirmCancel',{
								// 	order_sn: item.order_sn
								// }).then(res =>{


								// })
								this.showCancel = false;
								this.tabsIndex = 5;
								this.page = 1;
								this.dataList = [];
								this.thisItem = [];
								this.init();
							})
						}
					}
				})
			}
		}
	};
</script>

<style scoped lang="scss">
	.page {
		background-color: #f5f5f5;
		width: 100%;
		height: 100%;
		overflow-y: auto;

		.order-item {
			width: 90%;
			border-bottom: 2rpx solid #eee;
			margin: 0 auto;
			padding: 20rpx 0rpx;
			display: flex;
			justify-content: space-between;
			align-items: center;
		}

		.no {
			padding-top: 90rpx;
		}

		.group {
			flex: 1 1 auto;


			.section_1 {
				padding: 0 32rpx;
				background-color: rgb(255, 255, 255);

				.group_1 {
					padding: 24rpx 0 16rpx;

					.text_1 {
						color: rgb(0, 0, 0);
						font-size: 28rpx;
						font-weight: 500;
						line-height: 40rpx;
						white-space: nowrap;
					}

					.text_2 {
						margin-left: 48rpx;
						color: rgb(102, 102, 102);
						font-size: 28rpx;
						line-height: 40rpx;
						white-space: nowrap;
					}

					.text_3 {
						margin-left: 48rpx;
						color: rgb(102, 102, 102);
						font-size: 28rpx;
						line-height: 40rpx;
						white-space: nowrap;
					}

					.text_4 {
						margin-left: 46rpx;
						color: rgb(102, 102, 102);
						font-size: 28rpx;
						line-height: 40rpx;
						white-space: nowrap;
					}

					.text_5 {
						margin-left: 48rpx;
						color: rgb(102, 102, 102);
						font-size: 28rpx;
						line-height: 40rpx;
						white-space: nowrap;
					}

					.text_6 {
						margin-left: 48rpx;
						color: rgb(102, 102, 102);
						font-size: 28rpx;
						line-height: 40rpx;
						white-space: nowrap;
					}
				}

				.section_2 {
					margin-left: 8rpx;
					background-color: rgb(24, 181, 103);
					width: 40rpx;
					height: 8rpx;
				}
			}

			.group_2 {
				padding-bottom: 2412rpx;
				position: relative;

				.section_3 {
					background-image: linear-gradient(180deg,
							rgb(255, 255, 255) 0%,
							rgb(255, 255, 255) 0%,
							rgb(248, 248, 248) 100%,
							rgb(248, 248, 248) 100%);
					height: 48rpx;
				}

				.list {
					position: absolute;
					left: 32rpx;
					right: 32rpx;


					.list-item {
						background-color: rgb(255, 255, 255);
						border-radius: 24rpx;
						margin-bottom: 30rpx;

						.top-section {
							padding: 24rpx 24rpx;
							background-image: linear-gradient(180deg,
									rgba(24, 181, 103, 0.2) 0%,
									rgba(24, 181, 103, 0.2) 0.44%,
									rgba(255, 255, 255, 0.2) 100%,
									rgba(255, 255, 255, 0.2) 100%);
							border-radius: 24rpx 24rpx 0px 0px;

							.text_7 {
								color: rgb(24, 181, 103);
								font-size: 28rpx;
								font-weight: 500;
								line-height: 40rpx;
								white-space: nowrap;
							}

							.text_9 {
								color: rgb(102, 102, 102);
								font-size: 28rpx;
								line-height: 40rpx;
								white-space: nowrap;
							}
						}

						.center-group {
							padding: 0 24rpx 22rpx;

							.image {
								border-radius: 12rpx;
								width: 176rpx;
								height: 154rpx;
							}

							.right-group {
								margin-left: 24rpx;
								margin-bottom: 2rpx;
								flex: 1 1 auto;

								.text_11 {
									color: rgb(0, 0, 0);
									font-size: 32rpx;
									font-weight: 500;
									line-height: 44rpx;
									white-space: nowrap;
								}

								.text_13 {
									margin-top: 8rpx;
									color: rgb(102, 102, 102);
									font-size: 28rpx;
									line-height: 40rpx;
									white-space: nowrap;
								}

								.bottom-group {
									margin-top: 16rpx;
									white-space: nowrap;

									.text_15 {
										color: rgb(255, 74, 74);
										font-size: 24rpx;
										line-height: 28rpx;
									}

									.text_17 {
										color: rgb(255, 74, 74);
										font-size: 36rpx;
										line-height: 44rpx;
									}
								}
							}
						}

						.divider {
							margin: 0 24rpx;
							background-color: rgba(151, 151, 151, 0.08);
							height: 2rpx;
						}

						.bottom-group_1 {
							padding: 24rpx 24rpx;

							.left-text-wrapper {
								margin-right: 24rpx;
								padding: 8rpx 0 4rpx;
								color: rgb(24, 181, 103);
								font-size: 28rpx;
								line-height: 40rpx;
								white-space: nowrap;
								border-radius: 27rpx;
								width: 168rpx;
								height: 56rpx;
								border: solid 2rpx rgb(24, 181, 103);
							}

							.right-text-wrapper {
								padding: 10rpx 0 6rpx;
								color: rgb(255, 255, 255);
								font-size: 28rpx;
								line-height: 40rpx;
								white-space: nowrap;
								background-image: linear-gradient(113.7deg,
										rgb(53, 219, 160) 0%,
										rgb(53, 219, 160) -13.77%,
										rgb(24, 181, 103) 111.69%,
										rgb(24, 181, 103) 100%);
								border-radius: 27rpx;
								width: 168rpx;
								height: 56rpx;
							}

							.left-text-wrappe1 {
								margin-right: 24rpx;
								padding: 8rpx 0 4rpx;
								color: #797979;
								font-size: 28rpx;
								line-height: 40rpx;
								white-space: nowrap;
								border-radius: 27rpx;
								width: 168rpx;
								height: 56rpx;
								border: solid 2rpx #d4d4d4;
							}
						}
					}
				}
			}
		}
	}
</style>
