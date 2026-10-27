<template>
	<!-- 卡片头部 -->
	<view class="cardHeader">
		<view class="img">
			<image :src="imgSrc" mode=""></image>
		</view>
		<view class="content">
			<slot></slot>
		</view>
	</view>
</template>

<script>
	export default{
		props:{
			imgSrc:{
				type:String
			}
		},
		data() {
			return {
				// imgUrl: this.$store.state.imgUrl
			}
		},
		methods: {
			
		}
	}
</script>

<style lang="less">
	.cardHeader{
		width: 600rpx;
		background: #FFFFFF;
		border-radius: 40rpx;
		margin: 0 auto;
		.img{
			width: 256rpx;
			height: 88rpx;
				margin: 0 auto;
			image{
				width: 100%;
				height: 100%;
			}
		}
		.content{
			padding: 0 32rpx 40rpx 32rpx;
		}
	}
</style>
