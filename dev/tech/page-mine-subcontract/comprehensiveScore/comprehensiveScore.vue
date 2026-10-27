<template>
	<!--综合评分 -->
	<view class="comprehensiveScore">
		<view class="comprehensiveScore-header">
			<image class="bg" :src="imgUrl+'comprehensiveScoreImg/header-bg.png'" mode=""></image>
			<!-- 期数 第一期 -->
			<view class="Numberofperiods">
				第一期
			</view>
			<!-- 评分星星 -->
			<view class="star">
				<view class="star-item">
					<image :src="imgUrl+'comprehensiveScoreImg/full.png'" mode=""></image>

				</view>
				<view class="star-item">
					<image :src="imgUrl+'comprehensiveScoreImg/full.png'" mode=""></image>

				</view>
				<view class="star-item">
					<image :src="imgUrl+'comprehensiveScoreImg/full.png'" mode=""></image>

				</view>
				<view class="star-item">
					<image :src="imgUrl+'comprehensiveScoreImg/half.png'" mode=""></image>

				</view>
			</view>
			<!-- 超越同行 -->
			<view class="transcend">
				- 超越55%同行 -
			</view>


		</view>
		<view class="scoringRules">
			<scoringRules />
		</view>
		<view class="basicCertification">
			<basicCertification/>
		</view>
		<view class="serviceIndicators">
			<serviceIndicators/>
		</view>
		<view class="totalRanking">
			<totalRanking/>
		</view>

	</view>
</template>

<script>
	import scoringRules from "./components/scoringRules.vue"  //评分规则
	import basicCertification from "./components/basicCertification.vue"  //基础认证
	import serviceIndicators from "./components/serviceIndicators.vue"  //服务指标
	import totalRanking from "./components/totalRanking.vue"  //总排名榜
	export default {
		components: {
			scoringRules,
			basicCertification,
			serviceIndicators,
			totalRanking
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
	.comprehensiveScore {
		width: 100%;
		// height: 100%;
		background: #88DDAC;
		padding-bottom: 36rpx;

		.comprehensiveScore-header {
			position: relative;
			width: 100%;
			height: 764rpx;

			.bg {
				position: absolute;
				left: 0;
				top: 0;
				width: 100%;
				height: 100%;
			}

			.Numberofperiods {
				position: absolute;
				left: 100rpx;
				top: 60rpx;
				font-size: 32rpx;
				font-weight: 400;
				color: #FFFFFF;
			}

			.star {
				position: absolute;
				left: 0;
				bottom: 285rpx;
				width: 100%;
				height: 56rpx;
				display: flex;
				column-gap: 24rpx;
				justify-content: center;
				align-items: center;

				.star-item {
					width: 60rpx;
					height: 56rpx;

					image {
						width: 100%;
						height: 100%;
					}
				}
			}

			.transcend {
				position: absolute;
				left: 0;
				bottom: 210rpx;
				width: 100%;
				text-align: center;
				font-size: 32rpx;
				font-weight: 400;
				color: #FFFFFF;
			}
		}
		.scoringRules{
			position: relative;
			top: -55rpx;
		}
		.serviceIndicators{
			position: relative;
			top: -15rpx;
		}
		.basicCertification{
			position: relative;
			top: -35rpx;
		}
		.totalRanking{
			// position: relative;
			// top: 40rpx;
			margin-top: 13rpx;
		}
	}
</style>
