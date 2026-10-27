<template>
	<!-- 订单详情 -->
	<view class="orderDetails">
		<!-- 地图路线 -->
		<view class="mapRoute">
			<view class="tips">
				<view class="tips-icon">
					<image src="../../static/images/js.png" mode=""></image>
				</view>
				<view class="tips-text">
					本次服务已结束
				</view>

			</view>
			<view class="tips_time">
				预估收入 <text style="font-size:30rpx ;">￥</text><text> 148.00</text> （包含出行费18.00元）
			</view>
		</view>

		<view class="details">
			<view class="details-header">
				<view class="details-header-text">
					对本次客户的评价
				</view>
				<view class="complaint">
					<view class="complaint-text">
						去投诉
					</view>
					<view class="Settledtobesettled-right-three">
						<image :src="imgUrl+'images/indexImg/slices_two/jiantou.png'" mode="widthFix" class="img" style="width:14rpx;"></image>
					</view>


				</view>
			</view>
			<view class="evaluate">
				<view class="evaluate-img">
					<view class="img">
						<image src="../../static/images/xx.png" mode=""></image>
					</view>
					<view class="img">
						<image src="../../static/images/xx.png" mode=""></image>
					</view>
					<view class="img">
						<image src="../../static/images/xx.png" mode=""></image>
					</view>
					<view class="img">
						<image src="../../static/images/xx.png" mode=""></image>
					</view>
					<view class="img">
						<image src="../../static/images/xx.png" mode=""></image>
					</view>
					

				</view>
				<view class="evaluate-text">
					您的宝贵评价，对我们非常重要
				</view>
			</view>


		</view>
		<view class="btn">
			<view class="confirm">
				提交
			</view>
		</view>

	</view>
</template>

<script>
	export default {
		onLoad(option) {

			// this.orderDetailsData = JSON.parse(decodeURIComponent(option.order_details))
		},
		data() {
			const now = new Date()
			return {
				orderDetailsData: "",
				hour: "00",
				minutes: "00",
				seconds: "00",
				timer: null,
				imgUrl: this.$store.state.imgUrl,


			}
		},
		methods: {


		}
	}
</script>

<style lang="less">
	page {
		background: #fff;
	}

	.orderDetails {
		width: 100%;
		height: 100%;


		.mapRoute {
			box-sizing: border-box;
			padding: 40rpx 0 0 32rpx;
			width: 100%;
			height: 260rpx;
			background: #46D17E;


			.tips {
				display: flex;
				align-items: center;
				height: 88rpx;
				column-gap: 10rpx;

				.tips-icon {
					width: 40rpx;
					height: 40rpx;

					image {
						width: 100%;
						height: 100%;
					}
				}

				.tips-text {
					font-size: 40rpx;
					font-weight: bold;
					line-height: 64rpx;
					color: #FFFFFF;
				}
			}

			.tips_time {
				font-size: 24rpx;
				font-weight: 400;
				color: #FFFFFF;

				text {
					font-size: 40rpx;
					font-weight: bold;
				}


			}

		}

		.details {
			box-sizing: border-box;
			padding: 32rpx 32rpx 0 32rpx;
			height: 1040rpx;
			background: #FFFFFF;
			border-radius: 40rpx 40rpx 0px 0px;
			margin-top: -30rpx;

			.details-header {
				display: flex;
				justify-content: space-between;
				align-items: center;

				.details-header-text {
					font-size: 32rpx;
					font-weight: bold;
					line-height: 64rpx;
					color: #262626;
				}

				.complaint {
					display: flex;
					justify-content: space-between;
					column-gap: 26rpx;
					align-items: center;
					font-size: 28rpx;
					font-weight: 400;
					color: #666666;
					.Settledtobesettled-right-three{
						width: 12rpx;
						height: 48rpx;
						image{
							width: 100%;
							height: 100%;
						}
					}

				}

			}

			.evaluate {
				text-align: center;
				font-size: 28rpx;
				font-weight: 400;
				color: #999999;
				

				.evaluate-img {
					display: flex;
					justify-content: space-between;
					height: 57rpx;
					width: 400rpx;
					margin: 100rpx auto 42rpx auto;
					
					.img{
						width: 60rpx;
						height: 57rpx;
						
						image{
							width: 100%;
							height: 100%;
						}
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
			justify-content: space-between;
			align-items: center;


			.confirm {
				width: 685rpx;
				height: 88rpx;
				background: #46D17E;
				border-radius: 16rpx;
				color: #fff;
			}
		}

		.jump {
			position: absolute;
			top: 866rpx;
			right: 32rpx;
			width: 112rpx;
			height: 112rpx;

			cover-image {
				width: 100%;
				height: 100%;
			}
		}
	}
</style>
