<template>
	<!-- 订单统计 -->
	<view class="orderStatus">
		<view class="tabs">
			<cnpTabs @changeCurrent="changeCurrent" :list="list" itemWidth="135rpx" />
		</view>
		<!-- 已完成订单金额 -->
		<view class="completedOrder" v-show="true">
			<view class="completedOrder-big">
				<view class="completedOrder-big-left">
					<view class="completedOrder-big-left-describe">已完成订单金额(元)</view>
					<view class="completedOrder-big-left-money">￥{{complete.money||'0.00'}}</view>
				</view>
				<view class="completedOrder-big-right">
					<view class="completedOrder-big-right-describe">已完成订单数</view>
					<view class="completedOrder-big-right-money"><text>{{complete.count||0}}</text>笔</view>
				</view>

			</view>
			<u-line></u-line>
			<view class="completedOrder-small">
				<view class="completedOrder-small-left">
					<view>已完成订单数(笔)</view>
					<view>已完成订单金额(元)</view>
				</view>
				<view class="completedOrder-small-right text-right">
					<view>{{complete.count||0}}</view>
					<view>￥{{complete.money||'0.00'}}</view>
				</view>
			</view>
			<u-line></u-line>
			<view class="completedOrder-small">
				<view class="completedOrder-small-left">
					<view>已取消订单数(笔)</view>
					<view>已取消订单金额(元)</view>
				</view>
				<view class="completedOrder-small-right text-right">
					<view>{{cancel.count||0}}</view>
					<view>￥{{cancel.money||'0.00'}}</view>
				</view>
			</view>
		</view>


		<!-- 每日完成订单明细 -->
		<view class="dailyDetails" v-show="true">
			<view class="dailyDetails-title">
				<view class="title-text">
					每日完成订单明细  
				</view>                    
			</view>
			<u-line></u-line>
			<view class="dailyDetails-describe">
				<view class="">
					日期
				</view>
				<view class="">
					完成订单
				</view>
				<view class="">
					项目金额
				</view>
				<view class="">
					夜间路费
				</view>
			</view>
			<u-line></u-line>
			<view class="details-item">
				<view class="">
					{{detailed.name||'-/-'}}
				</view>
				<view class="">
					{{detailed.count||0}}
				</view>
				<view class="">
					{{detailed.money||0}}
				</view>
				<view class="">
					{{detailed.fare||0}}
				</view>
			</view>
		</view>

	</view>
</template>

<script>
	import {
		orderStatistics
	} from "./api.js"
	import cnpTabs from "../components/cnpTabs/cnpTabs.vue"
	export default {
		components: {
			cnpTabs
		},
		
		data() {
			return {
				current: 0,
				complete: {}, //已完成
				cancel: {}, //已取消
				detailed: {}, //详情
				list: [
					{name: '全部'},
					{name: '近3日'},
					{name: '近7日'},
					{name: '近30日'},
				],
			}
		},
		onLoad() {
			this.init()
		},
		methods: {
			init(){
				let data = {}
				if(this.current!=3){
					data = {
						status: this.current
					}
				}
				uni.showLoading({title:'加载中'})
				this.$db.http('index/orderStatistics',data).then(res => {
					if(res.code==1){
						uni.hideLoading()
						this.complete = res.data.complete
						this.cancel = res.data.cancel
						this.detailed = res.data.detailed
					}
				})
			},
			changeCurrent(item) {
				this.current = item.index
				this.init()
			}
		}
	}
</script>

<style lang="less">
	.orderStatus {
		width: 100%;
		height: 100%;
		background: #F8F8F8;

		.completedOrder {
			width: 686rpx;
			height: 454rpx;
			margin: 0 auto;
			background: #FFFFFF;
			border-radius: 16rpx;
			margin-top: 24rpx;

			.completedOrder-big {
				width: 100%;
				height: 166rpx;
				display: flex;
				align-items: center;
				justify-content: space-between;

				.completedOrder-big-left {
					height: 106rpx;
					display: flex;
					flex-direction: column;
					justify-content: space-between;
					margin-left: 36rpx;


					.completedOrder-big-left-describe {
						font-size: 28rpx;
						font-weight: 400;
						color: #000000;
					}

					.completedOrder-big-left-money {
						font-size: 48rpx;
						font-weight: bold;
						color: #46D17E;
					}
				}

				.completedOrder-big-right {
					margin-right: 140rpx;
					font-size: 28rpx;
					font-weight: 400;
					color: #000000;
					height: 106rpx;
					display: flex;
					flex-direction: column;
					justify-content: space-between;

					.completedOrder-big-right-money {
						text {
							font-size: 48rpx;
							font-weight: bold;
							color: #7C94FB;
							margin-right: 8rpx;
						}
					}
				}

			}

			.completedOrder-small {
				display: flex;
				align-items: center;
				justify-content: space-between;
				width: 100%;
				height: 144rpx;

				.completedOrder-small-left {
					font-size: 28rpx;
					font-weight: 400;
					height: 96rpx;
					color: #666666;
					margin-left: 36rpx;
					display: flex;
					flex-direction: column;
					justify-content: space-between;
				}

				.completedOrder-small-right {
					height: 96rpx;
					font-size: 32rpx;
					font-weight: 500;
					color: #000000;
					margin-right: 24rpx;
					display: flex;
					flex-direction: column;
					justify-content: space-between;
				}
			}
		}

		.dailyDetails {
			width: 686rpx;
			background: #fff;
			margin: 0 auto;
			border-radius: 16rpx 16rpx 0px 0px;

			.dailyDetails-title {
				width: 100%;
				height: 96rpx;
				display: flex;
				align-items: center;
				margin-top: 24rpx;

				.title-text {
					position: relative;
					font-size: 32rpx;
					font-weight: bold;
					color: #262626;
					margin-left: 68rpx;

					&::before {
						content: '';
						position: absolute;
						background: #46D17E;
						width: 8rpx;
						height: 36rpx;
						border-radius: 4rpx;
						left: -24rpx;
						top: 2rpx;

					}
				}
			}

			.dailyDetails-describe {
				display: flex;
				height: 88rpx;
				// padding: 0 42rpx 0 58rpx;
				justify-content: space-between;
				align-items: center;
				font-size: 28rpx;
				font-weight: 400;
				color: #262626;
				view{
					text-align: center;
					flex: 1;
				}
			}

			.details-item {
				display: flex;
				height: 88rpx;
				// padding: 0 42rpx 0 58rpx;
				justify-content: space-between;
				align-items: center;
				font-size: 28rpx;
				font-weight: 400;
				color: #666;
				view{
					flex: 1;
					text-align: center;
					// text-align: center;
				}
			}
		}
	}
</style>
