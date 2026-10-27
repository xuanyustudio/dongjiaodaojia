<template>
	<!-- 评分规则卡片 -->
	<view class="scoringRules">
		<cardHader :imgSrc="imgUrl+'images/comprehensiveScoreImg/pfgz.png'">
			<view class="content">
				<view class="content-img">
					<view class="big-img">
						<image :src="imgUrl+'images/comprehensiveScoreImg/zhpf.png'" mode=""></image>
					</view>
					<view class="small-img" >
						<image :src="imgUrl+'images/comprehensiveScoreImg/jia.png'" mode=""></image>
					</view>
					<view class="big-img">
						<image :src="imgUrl+'images/comprehensiveScoreImg/jcrz2.png'" mode=""></image>
					</view>
					<view class="small-img" >
						<image :src="imgUrl+'images/comprehensiveScoreImg/dy.png'" mode=""></image>
					</view>
					<view class="big-img">
						<image :src="imgUrl+'images/comprehensiveScoreImg/fwzs.png'" mode=""></image>
					</view>
					
				</view>
				<view class="content-text">
					综合评分共计5分，其中基础认证占2.2分，
					服务指数占2.8分；服务指数会根据人员成
					交率、加钟率、复购率、好评率、退单率、
					投诉率进行计算，综合评分及所含各项数据
					指标每日更新。
				</view>
			</view>
		</cardHader>
		
	</view>
</template>

<script>
	import cardHader from "./cardHeader.vue"
	export default {
		components:{
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
	.content{
		margin-top: 30rpx;
		.content-img{
			width: 100%;
			height: 72rpx;
			display: flex;
			align-items: center;
			column-gap: 16rpx;
			.big-img{
				width:144rpx;
				height: 72rpx;
				image{
					width: 100%;
					height: 100%;
					
				}
			}
			.small-img{
				width:28rpx;
				height:28rpx;
				image{
					width: 100%;
					height: 100%;
					
				}
			}
		}
		
		.content-text{
			margin-top: 24rpx;
			font-size: 28rpx;
			font-weight: 400;
			color: #666666;
		}
	}
</style>
