<template>
	<!-- 服务指标 -->
	<view class="serviceIndicators">
		<cardHader :imgSrc="imgUrl+'comprehensiveScoreImg/fwzb.png'">
			<view class="content">
				<view class="content-one">
					<view class="content-one-item">
						<view class="bigcircle">
							<view class="percentage">
								98%
							</view>
							<view class="text">
								好评率
							</view>
						</view>

						<view class="circle">

						</view>
					</view>
					<view class="content-one-item">
						<view class="bigcircle">
							<view class="percentage">
								90%
							</view>
							<view class="text">
								成交率
							</view>
						</view>

						<view class="circle">

						</view>
					</view>

				</view>


				<view class="content-two">
					<view class="content-one-item">
						<view class="bigcircle">
							<view class="percentage">
								87%
							</view>
							<view class="text">
								复购率
							</view>
						</view>

						<view class="circle">

						</view>
					</view>
					<view class="content-one-item">
						<view class="bigcircle">
							<view class="percentage">
								0%
							</view>
							<view class="text">
								投诉率
							</view>
						</view>

						<view class="circle">

						</view>
					</view>
					<view class="content-one-item">
						<view class="bigcircle">
							<view class="percentage">
								138
							</view>
							<view class="text">
								服务次数
							</view>
						</view>
					
						<view class="circle">
					
						</view>
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
				imgUrl: this.$store.state.imgUrl
			}
		},
		methods: {

		}
	}
</script>

<style lang="less">
	.content {
		margin-top: 48rpx;

		.content-one {
			width: 360rpx;
			height: 144rpx;
			margin: 0 auto;
			display: flex;
			justify-content: space-between;

		
		}

		.content-two {
			width: 516rpx;
			height: 144rpx;
			margin: 0 auto;
			display: flex;
			justify-content: space-between;
			margin-top: 28rpx;

		}
		.content-one-item {
			position: relative;
		
			.bigcircle {
				position: relative;
				z-index: 2;
				display: flex;
				flex-direction: column;
				justify-content: center;
				align-items: center;
				width: 144rpx;
				height: 144rpx;
				background: #88DDAC;
				border-radius: 50%;
				color: #FFFFFF;
		
				.percentage {
					font-size: 28rpx;
					font-weight: bold;
		
				}
		
				.text {
					font-size: 24rpx;
					font-weight: 400;
				}
		
			}
		
			.circle {
				position: absolute;
				bottom: 0rpx;
				right: -10rpx;
				width: 80rpx;
				height: 80rpx;
				background: #BDECD1;
				border-radius: 50%;
				z-index: 1;
			}
		
		}
	}
</style>
