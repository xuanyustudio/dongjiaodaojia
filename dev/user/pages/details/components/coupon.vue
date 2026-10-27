<template>
	<view>
		<u-popup :show="show" :round="10" mode="bottom" @close="close" @open="open" :safeAreaInsetBottom="true">
			<view class="head-warpper flex">
				<view class="right">　</view>
				<text class="left">优惠券领券</text>
			</view>
			<view class="content">
				<scroll-view scroll-y="true" style="height: 710rpx;" scroll-with-animation="true">
					<view class="item flex" v-for="(item,index) in couponList" @tap="lq(index,item)">　
						<view class="left">
							<view class="num">
								<!-- <text>{{ item.money }}</text> -->
								<text  style="font-size: 37rpx;" v-if="item.type==1">{{ item.name }}</text>
								<text v-else>¥{{ item.money }}</text>
							</view>
							<view class="condition">
								<text v-if="item.type==1"></text>
								<text v-else>{{ item.name }}</text>
							</view>
						</view>
						<view class="right flex">
							<view class="circular">
								<view class="circular-item" v-for="(item,index) in ['','','','']">
								</view>
							</view>
							<view class="explain">
								<view class="title">
									店铺优惠券</view>
								<view class="details">有效期至{{ item.end_time }}</view>
							</view>
							<!-- <view class="receive">
								领取
							</view> -->
							<view class="receive_img" v-if="item.is_receive">
								<image src="../../../static/images/receive.png"></image>
							</view>
						</view>
					</view>
				</scroll-view>
			</view>
		</u-popup>
	</view>
</template>

<script>
	export default {
		name: 'coupon',
		props: {
			couponList: {
				default: () => {
					return [];
				}
			}
		},
		data() {
			return {
				show: false
			}
		},
		methods: {
			open() {
				this.show = true
			},
			close() {
				this.show = false
			},
			lq(i,item){
				if(item.is_receive){ return}
				uni.showLoading({title:'加载中'})
				this.$http.post('api/index/receive',{ id: item.id }).then(() =>{
					uni.hideLoading()
					uni.showToast({title:'领取成功',icon:'none'})
					this.$set(this.couponList[i], 'is_receive', 1)
				}).catch(() => {
					uni.hideLoading()
				})
			}
		}
	}
</script>

<style scoped lang="scss">
	page {
		height: 500rpx;

		.head-warpper {
			justify-items: center;
			padding: 30rpx 20rpx;
			font-weight: bold;

			.right {
				margin: auto 0;
				width: 10rpx;
				height: 35rpx;
				background: #18B567;
				border-radius: 5rpx;

			}

			.left {
				padding-left: 10rpx;
			}
		}

		.content {
			height: 700rpx;

			.item {
				width: 95%;
				margin: 20rpx auto;
				height: 180rpx;
				background: #EDF9F3;
				border-radius: 20rpx;

				.right {
					width: 65%;
					height: 180rpx;
					background: #18B567;
					border-radius: 0 20rpx 20rpx 0;
					float: right;
					position: relative;
					overflow: hidden;

					.circular {
						height: 180rpx;
						display: flex;
						margin-left: -16rpx;
						flex-direction: column;
						justify-content: space-around;

						.circular-item {
							height: 30rpx;
							width: 30rpx;
							border-radius: 50%;
							background: #EDF9F3;

						}
					}

					.explain {
						margin: auto 20rpx;

						.title {
							font-size: 32rpx;
							font-weight: bold;
							font-family: PingFangSC-Medium, PingFang SC;
							color: #FFFFFF;
						}

						.details {
							font-size: 28rpx;
							font-family: PingFang-SC-Regular, PingFang-SC;

							color: #FFFFFF;
						}

					}

					.receive {
						margin-left: 36rpx;
						padding: 10rpx 0 6rpx;
						align-self: center;
						color: rgb(24, 181, 103);
						font-size: 28rpx;
						line-height: 40rpx;
						white-space: nowrap;
						background-image: linear-gradient(124.4deg,
								rgb(255, 255, 255) 0%,
								rgb(255, 255, 255) -13.77%,
								rgb(189, 255, 222) 111.69%,
								rgb(189, 255, 222) 100%);
						box-shadow: 4rpx 6rpx 12rpx 0px rgb(1, 147, 74);
						border-radius: 27rpx;
						width: 108rpx;
						height: 56rpx;
						text-align: center;

					}

					.receive_img {
						width: 130rpx;


						image {
							width: 140rpx;
							height: 140rpx;
							position: absolute;
							bottom: -30rpx;
							right: -20rpx;
						}
					}
				}

				.left {
					width: 40%;
					text-align: center;
					margin: auto 0;

					.num {

						margin: auto;

						color: #18B567;

						text {
							font-family: Price-Bold;
							font-size: 70rpx;
							font-weight: bold;
						}

					}

					.condition {
						font-family: PingFangSC-Regular, PingFang SC;
						font-weight: 400;
						color: #505050;
					}
				}
			}
		}
	}
</style>
