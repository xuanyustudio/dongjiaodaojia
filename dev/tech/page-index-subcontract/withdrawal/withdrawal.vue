<template>
	<!-- 提现 -->
	<view class="withdrawal">
		<view class="withdrawal-bg">
			<!-- 提现框 -->
			<view class="withdrawalFrame">
				<view class="money-text">
					提现金额
				</view>

				<view class="money">
					<text>¥ </text>
					<input type="number" v-model="money" />
				</view>
				<view class="depositFooter">
					<text class="left">可提现余额 ¥{{withdrawalData.balance?withdrawalData.balance:0}}</text> <text
						@click="allWithdrawal" class="right">全部提现</text>
				</view>
			</view>

		</view>
		<!-- 提现到支付宝 -->
		<view class="selectDeposit">
			<view class="selectDeposit-left">
				<view class="img">
					<image :src="imgUrl+'zbft.png'" mode=""></image>
				</view>
				<view class="text">
					提现到支付宝
				</view>
			</view>
			<view class="selectDeposit-right">
				<!-- 提现名称 -->
				<view class="deposit-name" v-if="withdrawalData.ali==1">
					{{withdrawalData.realname}}
				</view>
				<view class="deposit-name" @click="goBinding" v-else>
					去绑定支付宝
				</view>
				<!-- 箭头icon -->
				<view class="icon">
					<u-icon name="arrow-right" color="#666666" size="19"></u-icon>
				</view>
			</view>
		</view>
		
		
		<!-- 体现到微信 -->
		<view class="selectDeposits">
			<view class="selectDeposit-lefts">
				<view class="imgs">
					<image src="../../static/icons/wxser.png" mode=""></image>
				</view>
				<view class="texts">
					提现到微信
				</view>
			</view>
			<view class="selectDeposit-rights">
				<!-- 提现名称 -->
				<view class="deposit-names" v-if="withdrawalData.ali==0">
					{{withdrawalData.realname}}
				</view>
				<view class="deposit-names" @click="gowx" v-else>
					去绑定微信
				</view>
				<!-- 箭头icon -->
				<view class="icons">
					<u-icon name="arrow-right" color="#666666" size="19"></u-icon>
				</view>
			</view>
		</view>
		
		
		<!-- 立即提现 -->
		<view class="btn" @click="handWithdrawal">
			立即提现
		</view>

	</view>
</template>

<script>
	import {
		withdraw,
		withdrawApply
	} from "./api.js"
	export default {
		onReady() {
			this.getWihdrawInfo()  //获取提现页信息
		},
		data() {
			return {
				imgUrl: this.$store.state.imgUrl,
				withdrawalData: "",
				money: 0 //提现余额
			}
		},

		methods: {
			// 立即提现
			handWithdrawal() {

				if (this.money < 100) {
					uni.showToast({
						icon: 'none',
						duration: 3000,
						title: '提现金额最少为100'
					});
					return false
				}
				console.log( this.money)
				console.log( this.withdrawalData.balance)
				if (Number(this.money)  >Number(this.withdrawalData.balance)) {
					uni.showToast({
						icon: 'none',
						duration: 3000,
						title: '提现金额不能大于可提现金额'
					});
					return false
				}
				this.postSithdrawApply(this.money)


			},
			//全部提现
			allWithdrawal() {
				if (this.withdrawalData.balance > 0) {
					this.money = this.withdrawalData.balance
				}
			},
			// 去绑定支付宝
			goBinding() {
				uni.navigateTo({
					url: "/page-index-subcontract/withdrawalAccount/withdrawalAccount"
				})
			},
			//绑定微信
			gowx(){
				uni.navigateTo({
					url: "/page-index-subcontract/withdrawalAccount/wxser"
				})
			},
			//获取提现页信息
			getWihdrawInfo() {
				withdraw().then(res => {
					this.withdrawalData = res.data
				})

			},
			//发送提现请求
			postSithdrawApply(money) {
				withdrawApply({
					money
				}).then((res) => {
					// 提现成功重新请求一下钱包刷新余额
					console.log(res)
					uni.showToast({
						title:res.msg,
						icon:"none"
					})
					this.money = 0
					this.getWihdrawInfo()
				})
			}
		}
	}
</script>

<style lang="less">
	.withdrawal {
		width: 100%;
		height: 100%;

		.withdrawal-bg {
			width: 100%;
			height: 424rpx;
			background: #0ea17d;
			border-radius: 0px 0px 60rpx 60rpx;
			margin-top: -160rpx;
			position: relative;

			.withdrawalFrame {
				box-sizing: border-box;
				position: absolute;
				width: 686rpx;
				height: 310rpx;
				background: #FFFFFF;
				margin: 0 auto;
				border-radius: 16rpx;
				left: 50%;
				transform: translateX(-50%);
				padding: 0 24rpx;
				bottom: -102rpx;

				.money-text {
					font-size: 28rpx;
					font-weight: 500;
					color: #000000;
					margin-top: 32rpx;
				}

				.money {
					position: relative;
					width: 638rpx;
					height: 130rpx;
					font-size: 80rpx;
					margin: 0 auto;
					font-weight: bold;
					color: #303030;
					line-height: 130rpx;
					border-bottom: 1rpx #d6d7d9 solid;

					input {
						position: absolute;
						left: 52rpx;
						top: 0;
						min-height: 130rpx;
					}

					text {
						vertical-align: 20rpx;
						font-size: 52rpx;
					}
				}

				.depositFooter {
					height: 100rpx;
					width: 100%;
					display: flex;
					align-items: center;
					justify-content: space-between;

					.left {
						font-size: 24rpx;
						font-family: PingFang SC;
						font-weight: 400;
						color: #999999;
					}

					.right {
						font-size: 28rpx;
						font-weight: 500;
						color: #0ea17d;
					}
				}
			}

		}

		.selectDeposit {
			width: 686rpx;
			height: 112rpx;
			margin: 0 auto;
			background: #FFFFFF;
			border-radius: 16rpx;
			display: flex;
			align-items: center;
			justify-content: space-between;
			margin-top: 100rpx;

			.selectDeposit-left {
				display: flex;
				align-items: center;

				.img {
					width: 56rpx;
					height: 56rpx;
					margin-left: 24rpx;

					image {
						width: 100%;
						height: 100%;
					}
				}

				.text {
					font-size: 32rpx;
					height: 100%;
					font-weight: bold;
					color: #000000;
					margin-left: 24rpx;
				}
			}

			.selectDeposit-right {
				display: flex;

				.deposit-name {
					font-size: 28rpx;
					font-weight: 400;
					color: #666666;
				}

				.icon {
					margin: 0 12rpx;
				}
			}
		}




.selectDeposits {
			width: 686rpx;
			height: 112rpx;
			margin: 0 auto;
			background: #FFFFFF;
			border-radius: 16rpx;
			display: flex;
			align-items: center;
			justify-content: space-between;
			margin-top: 20rpx;

			.selectDeposit-lefts {
				display: flex;
				align-items: center;

				.imgs {
					width: 56rpx;
					height: 56rpx;
					margin-left: 24rpx;

					image {
						width: 100%;
						height: 100%;
					}
				}

				.texts {
					font-size: 32rpx;
					height: 100%;
					font-weight: bold;
					color: #000000;
					margin-left: 24rpx;
				}
			}

			.selectDeposit-rights {
				display: flex;

				.deposit-names {
					font-size: 28rpx;
					font-weight: 400;
					color: #666666;
				}

				.icons {
					margin: 0 12rpx;
				}
			}
		}







		.btn {
			width: 686rpx;
			height: 88rpx;
			background: #0ea17d;
			line-height: 88rpx;
			text-align: center;
			opacity: 1;
			border-radius: 16rpx;
			font-size: 32rpx;
			font-weight: 500;
			color: #FFFFFF;
			margin: 0 auto;
			margin-top: 48rpx;
		}
	}
</style>
