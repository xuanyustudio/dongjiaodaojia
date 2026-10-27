<template>
	<!-- 基础认证 -->
	<view class="basicCertification">
		<cardHader :imgSrc="imgUrl+'comprehensiveScoreImg/jcrz.png'">
			<view class="cationinfo">
				<view class="cationinfo-item"  v-for="(item,index) in infoData" :key="item.title">
					<view class="cationinfo-item-left">
						{{item.title}}
					</view>
					<!-- 点 -->
					<view class="spot">
						<view class="spot-item" v-for="(item,index) in 8" :key="index">

						</view>
					</view>
					<!-- 已通过 -->
					<view :class="item.pass?'cationinfo-item-right':'cationinfo-item-right noPass'">
						{{item.passText}}
					</view>
				</view>

			</view>
		</cardHader>
	</view>
</template>

<script>
	import cardHader from "./cardHeader.vue"
	export default {
		components: {
			cardHader
		},
		data() {
			return {
				imgUrl: this.$store.state.imgUrl,
				infoData:[
					{
						title:"实名认证",
						passText:"已通过",
						pass:true
						
					},
					{
						title:"健康证明",
						passText:"待审核",
						pass:false
						
					},
					{
						title:"从业资质",
						passText:"待审核",
						pass:false
						
					},
					{
						title:"头像相似",
						passText:"80%",
						pass:true
						
					}
				]

			}
		},
		methods: {

		}
	}
</script>

<style lang="less">
	.cationinfo {
		box-sizing: border-box;
		padding:0 36rpx 38rpx 36rpx;
		width: 536rpx;
		height: 187px;
		margin-top: 26rpx;
		background: #F0FCF5;
		// opacity: 0.08;
		border-radius: 40rpx;
		border: 1rpx solid #fff;

		.cationinfo-item {
			margin-top: 28rpx;
			width: 100%;
			height: 56rpx;
			display: flex;
			justify-content: space-between;
			align-items: center;


			.cationinfo-item-left {
				font-size: 32rpx;
				font-weight: 400;
				color: #88DDAC;
			}

			.spot {
				width: 180rpx;
				display: flex;
				justify-content: space-between;
				align-items: center;

				.spot-item {
					width: 12rpx;
					height: 12rpx;
					background: #88DDAC;
					border-radius: 50%;

				}

			}

			.cationinfo-item-right {
				width: 108rpx;
				height: 56rpx;
				background: #88DDAC;
				border-radius: 28rpx;
				font-size: 24rpx;
				font-weight: 400;
				color: #FFFFFF;
				line-height: 56rpx;
				text-align: center;
			}
			.noPass{
				background: #FFB83C;
			}
		}
	}
</style>
