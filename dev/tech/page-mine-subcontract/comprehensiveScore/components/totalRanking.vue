<template>
	<!-- 总排名榜 -->
	<view class="totalRanking">
		<cardHader :imgSrc="imgUrl+'comprehensiveScoreImg/zbpm.png'">
			<view class="Ranking">
				<view class="Ranking-item" v-for="(item,index) in rankingData" :key="item.num">
					<view class="Ranking-item-left">
						<!-- 名次 -->
						<view class="on-ranking">
							<image v-if="item.num==1" :src="imgUrl+'comprehensiveScoreImg/one.png'" mode=""></image>
							<image v-else-if="item.num==2" :src="imgUrl+'comprehensiveScoreImg/two.png'" mode=""></image>
							<image v-else-if="item.num==3" :src="imgUrl+'comprehensiveScoreImg/three.png'" mode=""></image>
							<text v-else>{{item.num}}</text> 
						</view>
						<!-- 头像 -->
						<view class="headportrait">
							
						</view>
						<view class="name">
							{{item.name}}
						</view>
						
					</view>
					<!-- 评分 -->
					<view class="Ranking-item-right">
						{{item.score}}
					</view>
					
				</view>
				
				<!-- 106名次 -->
				<view class="last">
					<view class="Ranking-item-left">
						<!-- 名次 -->
						<view class="on-ranking">
							<text >106</text> 
						</view>
						<!-- 头像 -->
						<view class="headportrait">
							
						</view>
						<view class="name">
							哈哈哈哈
						</view>
						
					</view>
					<!-- 评分 -->
					<view class="Ranking-item-right">
						3.78分
					</view>
					
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
				imgUrl: this.$store.state.imgUrl,
				rankingData:[
					{
						num:1,
						name:"陈俊",
						headportrait:"",
						score:"4.78分"
					},
					{
						num:2,
						name:"陈俊",
						headportrait:"",
						score:"4.78分"
					},
					{
						num:3,
						name:"陈俊哈",
						headportrait:"",
						score:"4.78分"
					},
					{
						num:4,
						name:"陈俊哈",
						headportrait:"",
						score:"4.78分"
					},
					{
						num:5,
						name:"陈俊哈",
						headportrait:"",
						score:"4.78分"
					},
					{
						num:6,
						name:"陈俊哈",
						headportrait:"",
						score:"4.78分"
					},
					{
						num:7,
						name:"陈俊哈",
						headportrait:"",
						score:"4.78分"
					},
					{
						num:8,
						name:"陈俊哈",
						headportrait:"",
						score:"4.78分"
					},
					{
						num:9,
						name:"陈俊哈",
						headportrait:"",
						score:"4.78分"
					},
					{
						num:10,
						name:"陈俊哈",
						headportrait:"",
						score:"4.78分"
					}
				]
	
			}
		},
		methods: {
	
		}
	}
</script>

<style lang="less">
	.Ranking{
		width: 536rpx;
		background: #FFFFFF;
		border-radius: 40rpx;
		margin: 0 auto;
		.Ranking-item{
			display: flex;
			justify-content: space-between;
			align-items: center;
			width: 100%;
			height: 72rpx;
			margin-top: 32rpx;
			
			
			
		}
		.last{
			box-sizing: border-box;
			display: flex;
			justify-content: space-between;
			align-items: center;
			width: 100%;
			height: 120rpx;
			background: #F0FCF5;
			border-radius: 16rpx;
			margin-top: 30rpx;
			padding: 0 24rpx 0 20rpx;
		}
		
		
		// 左边名次
		.Ranking-item-left{
			display: flex;
			align-items: center;
			column-gap: 24rpx;
			
			.on-ranking{
				width: 48rpx;
				height: 66rpx;
				text-align: center;
				line-height: 66rpx;
				font-size: 28rpx;
				font-weight: 900;
				color: #88DDAC;
				image{
					width: 100%;
					height: 100%;
				}
				
			}
			.headportrait{
				width: 72rpx;
				height: 72rpx;
				border-radius: 50%;
				background: pink;
			}
			.name{
				
			}
		}
		// 评分
		.Ranking-item-right{
			font-size: 28rpx;
			font-weight: 800;
			color: #FFB83C;
		}
	}
	
	
</style>
