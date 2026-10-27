<template>
<!-- 	<view class="nodata">
		<image :src="require('@/static/images/nodata.png')" mode=""></image>
		<text>{{ toast }}</text>
	</view> -->
	<view>
		<view class="views">
			<view class="viewsss">
				<view class="boxer">
					<view class="sertx">
					<view class="left"><image src="../../static/icons/dian22.png"></image><view style="color:#12AB51;padding-left: 10rpx;">未付款</view></view>
					<view class="right">
						<view class="rigtht">私人健康管理导师：党真真</view>
						<view><image style="width: 40rpx;height: 40rpx;" src="../../static/icons/cha.png"></image></view>
					</view>
					</view>
				</view>
				<view class="ser">
					<view style="font-weight: 600;font-size: 30rpx;">颈肩疏通 x1</view>
					<view style="display: flex;    justify-content: space-between;">
						<view style="font-size: 20rpx;">服务时间2022-5-21 11:30：00</view>
						<view style="color:#D30202 ;font-weight: 600;" >￥168.00</view>
					</view>
				</view>
				<view class="bitom">
					<view class="erxzxx">
						<view class="lefetert">再次购买</view>
						<view class="lefetert" style="color: white;background-color: #12AB51;">立即付款</view>
					</view>
				</view>
				
			</view>
			
			
			<view class="viewsss" v-for="(item,index) in 3" :key="index">
				<view class="boxer">
					<view class="sertx">
					<view class="left"><image src="../../static/icons/dian22.png"></image><view style="color:#12AB51;padding-left: 10rpx;">未付款</view></view>
					<view class="right">
						<view class="rigtht">私人健康管理导师：党真真</view>
						<view><image style="width: 40rpx;height: 40rpx;" src="../../static/icons/cha.png"></image></view>
					</view>
					</view>
				</view>
				<view class="ser">
					<view style="font-weight: 600;font-size: 30rpx;">颈肩疏通 x1</view>
					<view style="display: flex;    justify-content: space-between;">
						<view style="font-size: 20rpx;">服务时间2022-5-21 11:30：00</view>
						<view style="color:#D30202 ;font-weight: 600;" >￥168.00</view>
					</view>
				</view>
				<view class="bitom">
					<view class="erxzxxs">
						<view class="lefetert">再次购买</view>
						
					</view>
				</view>
				
			</view>
		</view>
	</view>


</template>

<script>
	export default {
		props:{
			toast:{
				
			}
		},
		data(){
			return {
				
			}
		}
	}
</script>

<style lang="scss" scoped>
	.lefetert{
		border:1px saddlebrown solid;
		width: 45%;
	height: 100%;
	font-size: 20rpx;
display: flex;
		align-content: center;
	    justify-content: space-evenly;
		border-radius: 15rpx;
	}
	.erxzxx{
		width: 40%;
		height: 100%;
		display: flex;
		justify-content: space-between;
	}
	.erxzxxs{
		width: 40%;
		height: 100%;
		display: flex;
		justify-content: flex-end;
	}
	.bitom{
		width: 97%;
		height: 40rpx;
	display: flex;
		    margin: 0rpx 0 0 0;
		justify-content: flex-end;
	}
	.ser{
		width: 97%;
		height: 120rpx;
		margin: 15rpx auto;
		display: flex;
		    flex-direction: column;
			justify-content: space-between;
			
	}
	.views{
		width:98% ;
		
		margin: -20rpx auto;
	}
	.viewsss{
		width: 100%;
		height: 270rpx;
		margin:  30rpx auto;
		border-radius: 20rpx;
		background-color: white;
		
	}
	.boxer{
		background-color: #EFFFF6;
		width: 100%;
		display: flex;
		height: 50rpx;
		justify-content: space-between;
		
		
	}
	.left{
		width: 25%;
		height: 100%;
		display: flex;
		font-size: 20rpx;
		align-items: center;
		image{
			width: 30rpx;height: 30rpx;
		}
	}
	.right{
		
		display: flex;
		align-items: center;
	}
	.rigtht{
		width: 100%;
		height: 100%;
		display: flex;
		font-size: 20rpx;
		align-items: center;
		color:#12AB51;
		image{
			width: 30rpx;height: 30rpx;
		}
	}
	
	.sertx{
		width: 97%;
		margin: 0 auto;
		height: 100%;
		display: flex;
		justify-content: space-between;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	// .nodata{
	// 	display: flex;
	// 	flex-direction: column;
	// 	align-items: center;
	// 	justify-content: center;
	// 	image{
	// 		width: 256rpx;
	// 		height: 256rpx;
	// 	}
	// 	text{
	// 		color: rgba(80, 80, 80, 1);
	// 		font-size: 28rpx;
	// 		margin-top: 24rpx;
	// 	}
	// }
</style>
