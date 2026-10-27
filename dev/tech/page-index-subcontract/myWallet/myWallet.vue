<template>
	<!-- 我的钱包 -->
	<view class="myWallet">
		<!-- 背景 -->
		<view class="header-bg"></view>
		<!-- 钱包金额 -->
		<!-- <view class="walletMoney" :style="'background-image:url('+imgUrl+'wallet.png)'"> -->
			<view class="walletMoney">
			<view class="walletMoney-left">
				<view class="walletMoney-det">
					我的余额(元)
				</view>
				<view class="walletMoney-num">
					{{withdrawData.balance?withdrawData.balance:0}}
				</view>
			</view>
			<view class="walletMoney-right" @click="toWithdrawCash">
				去提现
			</view>
			<!-- <image :src="imgUrl+'bei.png'" mode=""></image> -->
			<view class="xbg">
				
			</view>
			
		</view>
	
		<view class="myWallet-log">
				<!-- 账户流水 -->
			<!-- <view class="myWallet-log-item">
				<view class="">
					账户流水
				</view>
				<u-icon name="arrow-right" color="#666666" size="20"></u-icon>
			</view>
			<u-line></u-line> -->
			<!-- 提现记录 -->
			<view class="myWallet-log-item" @click="goWithdrawList">
				<view class="">
					提现记录
				</view>
				<u-icon name="arrow-right" color="#666666" size="20"></u-icon>
			</view>
		</view>
		
		
	</view>
</template>

<script>
	import {wallet} from "./api.js"
	export default {
		onReady(){
			this.walletinfo()  //获取钱包信息
		},
		data() {
			return {
				imgUrl: this.$store.state.imgUrl,
				withdrawData:"0" //我的钱包数据
				
			}
		},
		methods: {
			toWithdrawCash(){
				uni.navigateTo({
					url:"/page-index-subcontract/withdrawal/withdrawal"
				})
			},
			// 跳转提现记录
			goWithdrawList(){
				uni.navigateTo({
					url:"/page-index-subcontract/withdrawals/withdrawals"
				})
			},
			//获取钱包信息
			walletinfo(){
				wallet().then(res=>{
					console.log(res)
					this.withdrawData =res.data
				})
			},
			
			
		}
	}
</script>

<style lang="less">
	.myWallet{
		position: relative;
		width: 100%;
		height: 100%;
		
		
		.header-bg{
			position: absolute;
			width: 1062rpx;
			height:830rpx;
			background: #0ea17d;
			border-radius: 50%;
			left: -250rpx;
			top: -407rpx;
			
		}
		.walletMoney{
			position: relative;
			display: flex;
			align-items: center;
			justify-content: space-between;
			width: 690rpx;
			height: 240rpx;
			background-size: 100% 100%;
			top: 100rpx;
			margin: 0 auto;
			background-image: url('../../static/icons/bei.png');
			image{
				position: absolute;
				z-index: -1;
				left: 0;
				top: 0;
				width: 100%;
				height: 100%;
			}
			.xbg{
				position: absolute;
				z-index: 1;
				left: 0;
				top: 0;
				width: 100%;
				height: 100%;
				opacity: .7;
			}
			.walletMoney-left{
				position: relative;
				z-index: 3;
				left: 40rpx;
				color: black;
				font-weight: bold;
				.walletMoney-det{
					font-size: 32rpx;
				}
				.walletMoney-num{
					font-size: 62rpx;
					
				}
			}
			.walletMoney-right{
				position: relative;
				z-index: 3;
				right: 40rpx;
				width: 176rpx;
				height: 80rpx;
				background:#0da17d;
				box-shadow: 12rpx 12rpx 12rpx rgba(27, 151, 36, 0.59);
				border-radius: 48rpx;
				text-align: center;
				font-size: 32rpx;
				font-weight: 500;
				line-height: 80rpx;
				color: white;
				
			}
		}
		.myWallet-log{
				margin-top: 205rpx;
			.myWallet-log-item{
				display: flex;
				justify-content: space-between;
				align-items: center;
				padding: 0 30rpx;
				box-sizing: border-box;
				width: 750rpx;
				height: 124rpx;
				background: #fff;
				overflow: hidden;
				font-size: 32rpx;
				font-weight: bold;
				color: #303030;
			}
		}
		
	}

</style>
