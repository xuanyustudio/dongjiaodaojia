<template>
	<view class="nodata">
		<image :src="require('@/static/images/nodata.png')" mode=""></image>
		<text>{{ toast }}</text>
	</view>
</template>

<script>
	export default {
		props:{
			toast:{
				default:'暂无数据~'
			}
		},
		data(){
			return {
				
			}
		}
	}
</script>

<style lang="scss" scoped>
	.nodata{
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		image{
			width: 256rpx;
			height: 256rpx;
		}
		text{
			color: rgba(80, 80, 80, 1);
			font-size: 28rpx;
			margin-top: 24rpx;
		}
	}
</style>