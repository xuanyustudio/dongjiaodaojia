<!-- 公共头部 -->
<template>
	<view class="commonHead">
		<view class="iconLeft">

		</view>
		<view class="describe">
			{{describe}}
		</view>
	</view>
</template>

<script>
	export default {
		props: {
			describe: {
				type: String
			}
		},
		data() {
			return {}
		}
	}
</script>

<style lang="less" scoped>
	.commonHead {
		display: flex;
		width: 100%;
		height: 50rpx;
		margin-top: 20rpx;
		margin-bottom: 9rpx;

		.iconLeft {
			width: 6rpx;
			height: 30rpx;
			background: linear-gradient(180deg, #0089FF 0%, rgba(0, 137, 255, 0) 100%);
			margin-left: 20rpx;
			border-radius: 3rpx;
		}

		.describe {
			width: 144rpx;
			height: 50rpx;
			font-size: 36rpx;
			font-family: PingFang SC;
			font-weight: 300;
			line-height: 50rpx;
			color: #000000;
			opacity: 1;
			margin-left: 20rpx;
		}


	}
</style>
