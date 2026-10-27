<template>
	<view class="orderDetails">
		<!-- 地图路线 -->
		<view class="mapRoute">
			<view class="tips"> 服务已开始，请认真工作 </view>
			<view class="tips_time">
				服务已用时
				<text style="margin-left: 16rpx" class="time">{{ hour }}</text>
				<text class="spot">:</text> <text class="time">{{ minutes }}</text>
				<text class="spot">:</text> <text class="time">{{ seconds }}</text>
			</view>
		</view>
		<view class="details">
			<view class="details-header">
				<view class="details-header-left">
					<view class="headportrait">
						<image :src="orderDetailsData.avatar" mode=""></image>
					</view>
					<view class="headportrait-left">
						<view class="number">
							尾号{{
			                orderDetailsData ? orderDetailsData.username.substr(7) : '0000'
			              }}
						</view>
						<view class="score">
							<!-- 信用分98 | 好评率95% -->
							信用分{{ orderDetailsData.praise }}
						</view>
					</view>
				</view>
				<view class="details-header-right">
					<image src="../../static/images/dh.png" mode=""></image>
				</view>
			</view>
			<view class="details-center">
				<!-- 服务地址 -->
				<view class="servicetime">
					<text class="servicetime-one">服务时间<text>{{
			              orderDetailsData
			                ? orderDetailsData.appointment_time.substr(10)
			                : "00:00"
			            }}</text></text>
					<text class="servicetime-two">时长</text>
					<text class="servicetime-three">{{ orderDetailsData.duration }}分钟</text>
				</view>
				<!-- 服务时间 -->
				<view class="service-address">
					<view class="address">
						服务地址：{{ orderDetailsData.province }}{{ orderDetailsData.city
			            }}{{ orderDetailsData.district }}{{ orderDetailsData.address }}
					</view>
					<!-- 距离 -->
					<view class="distance"> {{ orderDetailsData.distance }}km </view>
				</view>
			</view>
			<view class="details-bottom">
				<view class="service-name">
					<view class="icon">
						{{
			              orderDetailsData
			                ? orderDetailsData.name.substr(orderDetailsData.name.length - 2)
			                : "按摩"
			            }}
					</view>
					<view class="name">
						{{ orderDetailsData.service_name }}
					</view>
				</view>
				<view class="money">
					<text class="text">预估收入</text>
					<text class="num">{{ orderDetailsData.technician_money }}</text>
				</view>
			</view>
			<!-- 报警 -->
			<view class="callThePolice">
				<view class="police-left">
					<view class="police-left-police"> 遇到危险，紧急求助 </view>
					<view class="police-left-tip">
						如果目前不能继续接单，请先暂停在线状态
					</view>
				</view>
				<view class="police"> 报警 </view>
			</view>
		</view>
		<view class="btn">
			<view class="confirm" @click="goEndService"> 服务结束 </view>
		</view>

	</view>
</template>

<script>
	export default {
		onLoad(option) {
			if (this.timer) {
				clearInterval(this.timer);
			}
			setInterval(() => {
				this.getTime()
			}, 1000)
			this.orderDetailsData = JSON.parse(
				decodeURIComponent(option.order_details)
			);
		},
		data() {
			const now = new Date();
			return {
				orderDetailsData: "",
				hour: "00",
				minutes: "00",
				seconds: "00",
				timer: null,
			};
		},
		methods: {
			getTime() {
				this.seconds = this.seconds++ > 9 ? this.seconds : "0" + this.seconds;
				if (this.seconds > 59) {
					this.seconds = "00";
					this.minutes = this.minutes++ > 9 ? this.minutes : "0" + this.minutes;
					if (this.minutes > 59) {
						this.hour = this.hour++ > 9 ? this.hour : "0" + this.hour;
					}
				}
				console.log(this.hour);
				console.log(this.minutes);
				console.log(this.seconds);
			},
			// 跳转服务结束
			goEndService() {
				let that = this;
				uni.navigateTo({
					url: "/page-index-subcontract/endOfService/endOfService?order_details=" +
						encodeURIComponent(JSON.stringify(that.orderDetailsData)),
				});
			},
			/**
			 * @description: 打开高德地图
			 * @author: hst
			 */
			openGuide(item) {
				//  微信小程序
				// #ifdef MP-WEIXIN
				console.log("走的这里", item);
				wx.openLocation({
					latitude: +28.146840896, // 纬度
					longitude: +113.065295505, // 经度
					name: "长沙", // 地址名称
					address: "长沙南站", // 详细地址
					success: function(r) {
						console.log(r);
					},
					fail: function(res) {
						console.log("拉起失败啦", res);
					},
				});
				// #endif
			},
		},
	};
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
			background: #46d17e;

			.tips {
				font-size: 40rpx;
				font-weight: bold;
				line-height: 64rpx;
				color: #ffffff;
			}

			.tips_time {
				font-size: 28rpx;
				font-weight: 400;
				line-height: 64rpx;
				color: #ffffff;

				.time {
					font-size: 28rpx;
					padding: 6rpx 10rpx;
					font-weight: bold;
					line-height: 0px;
					color: #46d17e;
					opacity: 1;
					background: #ffffff;
					opacity: 1;
					border-radius: 8rpx;
				}

				.spot {
					font-size: 50rpx;
					color: #9fe7bc;
					margin: 0 8rpx;
				}
			}
		}

		.details {
			box-sizing: border-box;
			padding: 32rpx 32rpx 0 32rpx;
			height: 1040rpx;
			background: #ffffff;
			border-radius: 40rpx 40rpx 0px 0px;
			margin-top: -30rpx;

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
						background: #8cb190;

						image {
							width: 100%;
							border-radius: 50%;
							height: 100%;
						}
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
						color: #ffffff;
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
				height: 120rpx;
				display: flex;
				justify-content: space-between;
				align-items: center;
				border-top: 2rpx #eeeeee solid;

				.service-name {
					display: flex;

					.icon {
						width: 88rpx;
						height: 40rpx;
						background-image: url(../../static/images/orderType.png);
						background-size: 100% 100%;
						font-size: 24rpx;
						font-family: PingFang SC;
						font-weight: 500;
						line-height: 40rpx;
						color: #ffffff;
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

			.callThePolice {
				width: 100%;
				height: 166rpx;
				display: flex;
				align-items: center;
				justify-content: space-between;
				border-top: 2rpx #eeeeee solid;

				.police-left-police {
					font-size: 32rpx;
					font-weight: bold;
					line-height: 64rpx;
					color: #262626;
				}

				.police-left-tip {
					font-size: 24rpx;
					font-weight: 400;
					line-height: 64rpx;
					color: #999999;
				}

				.police {
					width: 160rpx;
					height: 56rpx;
					background: #ffb83c;
					border-radius: 40rpx;
					text-align: center;
					font-size: 28rpx;
					font-weight: 500;
					line-height: 56rpx;
					color: #ffffff;
					letter-spacing: 2rpx;
				}
			}
		}

		.btn {
			box-sizing: border-box;
			padding: 0 30rpx;
			width: 750rpx;
			height: 120rpx;
			background: #ffffff;
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
				background: #46d17e;
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
