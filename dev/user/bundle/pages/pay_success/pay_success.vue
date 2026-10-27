<template>
	<view class="flex-col page">

		<view class="flex-col section_1">

			<view class="flex-col group">
				<image
					src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844621395277574.png"
					class="image_1" />
				<view class="flex-col items-center group_1">
					<text class="text">支付成功</text>
					<text class="text_1">您已支付成功，请去订单查看</text>
				</view>
			</view>
			<view class="section_2"> </view>
		</view>
		<view class="flex-col section_3">
			<view class="group_2">
				<text class="text_2">￥</text>
				<text class="text_3">{{ pay_money }}</text>
			</view>
			<view class="flex-col group_3">
				<view class="flex-row">
					<text class="text_4">支付方式</text>
					<text class="text_5">{{ payType }}</text>
				</view>
				<view class="flex-row group_5">
					<text class="text_6">支付单号</text>
					<text class="text_7">{{ order_sn }}</text>
				</view>
				<view class="flex-row group_6">
					<text class="text_8">支付时间</text>
					<text class="text_9">{{ create_time }}</text>
				</view>
			</view>
			<view class="flex-col group_7">
				<view class="flex-col items-center button" @click="toPath">
					<text>查看订单</text>
				</view>
				<view class="flex-col items-center button_1" @click="back">
					<text>返回</text>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				payType:'',
				order_sn:'',
				create_time:'',
				pay_money:''
			};
		},
		onShow(){
			this.init();
		},
		methods:{
			init(){
				let order_info = uni.getStorageSync('order_info');
				console.log(order_info,1212	)
				for( let i in order_info ){
					this[i] = order_info[i];
				}
			},
			back(){
				uni.navigateTo({
					url:'/pages/confirm_order/confirm_order'
				})
			},
			toPath(){
				uni.reLaunch({
					url:'/pages/order/order'
				})
			}
		}
	};
</script>

<style scoped lang="scss">
	.page {
		padding-bottom: 230rpx;
		background-color: #f8f8f8;
		height: 100%;
		width: 100%;
		overflow-y: auto;

		.section_1 {
			padding: 116rpx 32rpx 66rpx;
			background-image: linear-gradient(145.6deg,
					rgb(49, 208, 129) 0%,
					rgb(49, 208, 129) -6.14%,
					rgb(24, 181, 103) 111.55%,
					rgb(24, 181, 103) 100%);

			.image {
				margin-left: 16rpx;
				width: 16rpx;
				height: 28rpx;
			}

			.group {
				margin-top: 70rpx;
				padding: 0 160rpx;

				.image_1 {
					align-self: center;
					width: 188rpx;
					height: 188rpx;
				}

				.group_1 {
					margin-top: 24rpx;

					.text {
						color: rgb(255, 255, 255);
						font-size: 36rpx;
						font-weight: 500;
						line-height: 50rpx;
						white-space: nowrap;
					}

					.text_1 {
						margin-top: 8rpx;
						color: rgb(255, 255, 255);
						font-size: 28rpx;
						line-height: 40rpx;
						white-space: nowrap;
					}
				}
			}

			.section_2 {
				margin-top: 50rpx;
				background-color: rgb(7, 140, 74);
				border-radius: 20rpx;
				height: 40rpx;
			}
		}

		.section_3 {
			margin: -86rpx 32rpx 0;
			padding: 48rpx 86rpx 142rpx;
			background-image: url('https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844622964609533.png');
			background-size: 100% 100%;
			background-repeat: no-repeat;
			position: relative;

			.group_2 {
				align-self: center;
				white-space: nowrap;

				.text_2 {
					color: rgb(80, 80, 80);
					font-size: 32rpx;
					line-height: 38rpx;
				}

				.text_3 {
					font-family: Price;
					color: rgb(80, 80, 80);
					font-size: 64rpx;
					line-height: 76rpx;
				}
			}

			.group_3 {
				margin-top: 86rpx;

				.group_5 {
					margin-top: 24rpx;

					.text_6 {
						color: rgb(102, 102, 102);
						font-size: 28rpx;
						line-height: 40rpx;
						white-space: nowrap;
					}

					.text_7 {
						margin-left: 56rpx;
						color: rgb(0, 0, 0);
						font-size: 28rpx;
						line-height: 40rpx;
						white-space: nowrap;
					}
				}

				.group_6 {
					margin-top: 24rpx;

					.text_8 {
						color: rgb(102, 102, 102);
						font-size: 28rpx;
						line-height: 40rpx;
						white-space: nowrap;
					}

					.text_9 {
						margin-left: 56rpx;
						color: rgb(0, 0, 0);
						font-size: 28rpx;
						line-height: 40rpx;
						white-space: nowrap;
					}
				}

				.text_4 {
					color: rgb(102, 102, 102);
					font-size: 28rpx;
					line-height: 40rpx;
					white-space: nowrap;
				}

				.text_5 {
					margin-left: 56rpx;
					color: rgb(0, 0, 0);
					font-size: 28rpx;
					line-height: 40rpx;
					white-space: nowrap;
				}
			}

			.group_7 {
				margin-top: 80rpx;
				padding: 0 34rpx;

				.button {
					margin-right: 6rpx;
					padding: 18rpx 0;
					color: rgb(255, 255, 255);
					font-size: 32rpx;
					font-weight: 500;
					line-height: 44rpx;
					white-space: nowrap;
					background-color: rgb(24, 181, 103);
					border-radius: 40rpx;
				}

				.button_1 {
					margin-right: 6rpx;
					margin-top: 38rpx;
					padding: 16rpx 0;
					color: rgb(24, 181, 103);
					font-size: 32rpx;
					font-weight: 500;
					line-height: 44rpx;
					white-space: nowrap;
					border-radius: 40rpx;
					border: solid 2rpx rgb(24, 181, 103);
				}
			}
		}
	}
</style>
