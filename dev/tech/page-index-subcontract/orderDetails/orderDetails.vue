<template>
	<!-- 订单详情 -->
	<view class="orderDetails">
		<!-- 地图路线 -->
		<view class="mapRoute">
			<map :latitude="28" :longitude="113"></map>

		</view>
		<view class="details">
			<view class="details-header">
				<view class="details-header-left">
					<view class="headportrait">

					</view>
					<view class="headportrait-left">
						<view class="number">
							尾号9919
						</view>
						<view class="score">
							信用分98 | 好评率95%
						</view>
					</view>
				</view>
				<view class="details-header-right">
					<image :src="imgUrl+'dh.png'" mode=""></image>
				</view>
			</view>
			<view class="details-center">
				<!-- 服务地址 -->
				<view class="servicetime">
					<text class="servicetime-one">服务时间19:30</text>
					<text class="servicetime-two">时长</text>
					<text class="servicetime-three">60分钟</text>
				</view>
				<!-- 服务时间 -->
				<view class="service-address">
					<view class="address">
						服务地址：长沙市岳麓区软件园A座1003
					</view>
					<!-- 距离 -->
					<view class="distance">
						14.1km
					</view>
				</view>

			</view>
			<view class="details-bottom">
				<view class="service-name">
					<view class="icon" :style="'backgroundImage:url('+imgUrl+'images/orderImg/ordertype.png'+')'">
						SPA
					</view>
					<view class="name">
						芳香精油SPA
					</view>
				</view>
				<view class="money">
					<text class="text">预估收入</text>
					<text class="num">148.00</text>
				</view>
			</view>

		</view>
		<!-- 接单和取消按钮 -->
		<view class="btn">
			<view class="cancel">
				申请取消
			</view>
			<view class="confirm">
				确认接单
			</view>
		</view>

	</view>
</template>

<script>
	export default {
		data() {
			return {
				imgUrl: this.$store.state.imgUrl
			}
		},
		methods: {

		}
	}
</script>

<style lang="less">
	page{
		background: #fff;
	}
	.orderDetails {
		width: 100%;
		height: 100%;
		

		.mapRoute {
			width: 100%;
			height: 992rpx;

			map {
				width: 100%;
				height: 100%;
			}

		}

		.details {
			box-sizing: border-box;
			padding: 32rpx 32rpx 0 32rpx;
			height: 378rpx;
			background: #FFFFFF;
			border-radius: 40rpx 40rpx 0px 0px;

			// margin-top: -20rpx;
			.details-header {
				display: flex;
				align-items: center;
				justify-content: space-between;

				.details-header-left {
					display: flex;
					column-gap: 16rpx;
					align-items: center;

					.headportrait {
						width: 88rpx;
						height: 88rpx;
						border-radius: 50%;
						background: #8CB190;
					}

					.number {
						font-size: 28rpx;
						font-weight: bold;
						color: #000000;
					}

					.score {
						font-size: 24rpx;
						font-weight: 400;
						color: #666666;
						margin-top: 8rpx;

					}
				}

				.details-header-right {
					width: 80rpx;
					height: 80rpx;

					image {
						width: 100%;
						height: 100%;
					}
				}
			}

			.details-center {
					margin-top: 40rpx;
				.servicetime {
					.servicetime-one {
						font-size: 32rpx;
						font-weight: bold;
						color: #000000;
					}

					.servicetime-two {
						background: #262626;

						border-radius: 8rpx;
						padding: 1rpx 12rpx;
						font-size: 24rpx;
						font-weight: 400;
						color: #FFFFFF;
						margin-left: 24rpx;
					}

					.servicetime-three {
						font-size: 28rpx;
						font-weight: 400;
						color: #666666;
						margin-left: 12rpx;
						
					}
				}

				.service-address {
					display: flex;
					justify-content: space-between;
					font-size: 24rpx;
					font-weight: 400;
					color: #666666;
					margin-top: 16rpx;
				}
			}

			.details-bottom {
				margin-top: 42rpx;
				display: flex;
				justify-content: space-between;

				.service-name {
					display: flex;
				

					.icon {
						width: 88rpx;
						height: 40rpx;
						// background-image: url(../../static/images/orderImg/ordertype.png);
						background-size: 100% 100%;
						font-size: 24rpx;
						font-family: PingFang SC;
						font-weight: 500;
						line-height: 40rpx;
						color: #FFFFFF;
						text-align: center;
					}

					.name {
						font-size: 32rpx;
						font-weight: bold;
						color: #000000;
						margin-left: 12rpx;
					}
				}

				.money {
					.text {
						font-size: 24rpx;
						font-weight: 400;
						color: #666666;
					}

					.num {
						font-size: 40rpx;
						font-weight: bold;
						color: #000000;
						margin-left: 8rpx;
					}
				}
			}
		}

		.btn {
			box-sizing: border-box;
			padding: 0 30rpx;
			width: 750rpx;
			height: 120rpx;
			background: #FFFFFF;
			box-shadow: 0px -8rpx 16rpx rgba(0, 0, 0, 0.06);
			margin-bottom: 60rpx;
			text-align: center;
			line-height: 88rpx;
			font-size: 32rpx;
			font-weight: 400;
			display: flex;
			justify-content:space-between ;
			align-items: center;
			.cancel{
				width: 216rpx;
				height: 88rpx;
				background: #F4F4F4;
				border-radius: 16rpx;
				
				color: #999999;
			}
			.confirm{
				width: 446rpx;
				height: 88rpx;
				background: #46D17E;
				border-radius: 16rpx;
				color: #FFFFFF;
			}
		}
	}
</style>
