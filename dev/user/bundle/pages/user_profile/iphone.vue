<template>
	<view>
		<view class="a">
			<view class="ser">
				<view class="ss">
					<view style="font-size: 16rpx;">手机号</view>
					<input type="text">
				</view>
				<view class="ss">
					<view style="font-size: 16rpx;">验证码</view>
					<view class="s">
						<input type="text">
						<view  class="ssss"style="font-size: 16rpx;">获取验证码</view>
					</view>
				</view>
			</view>
		</view>
		<view class="l" @click="sd()">确定</view>
	</view>
</template>

<script>
	export default {
		data(){
			return{
				
			}
			
		},
		methods:{
				sd(){
					console.log("用户点击了㾡")
				}
			}
	}
</script>

<style scoped lang="scss">
	.l{
		background-color:#0ba27f ;
		width: 90%;
		height: 90rpx;
		border-radius: 50rpx;
		margin: 40rpx auto;
		display: flex;
		align-items: center;
		justify-content: space-evenly;
		color: white;
	}
	page{
		background-color: #f6f6f6;
	}
	.a{
		background-color: white;
		width:90%;
		height: 250rpx;
		margin: 30rpx auto;
		display: flex;
		    align-items: center;
		    justify-content: space-evenly;
	}
	.ser{
		display: flex;
		width: 95%;
		height: 85%;
		flex-direction: column;
		    justify-content: space-between;
	}
	.ss{
		width: 100%;
		height: 47%;
		border-bottom: 1px solid black;
		display: flex;
		    flex-direction: column;
		    justify-content: space-between;
		
	}
	.s{
		display: flex;
		width: 100%;
		height: 100%;
		    align-items: center;
		    justify-content: space-between;
	}
	.ssss{
		width: 140rpx;
		display: flex;
		align-items: center;
		height: 40rpx;
		border: 1px solid black;
		justify-content: space-evenly;

	}
</style>