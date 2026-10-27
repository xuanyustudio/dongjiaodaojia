<template>
	<view>
		<view class="a">
			<view class="b"><image src="../../static/logo/wx.png"></image></view>
			<view class="c">
				<view class="d">e客</view>
				<view class="e">
					<view style="align-items: center;display:flex;20rpx auto; 90%;height: 90%; font-size:20rpx;width: 90%;">您好，我是享享到家的客服e客，请问有什 么可以帮您？</view>
				</view>
			</view>
		</view>
		<view class="a" style="    margin: 52px 0 0 61px;">
			<view class="c">
				<view class="d" style="display: flex;justify-content: flex-end;">客户</view>
				<view class="e">
					<view style="align-items: center;display:flex;20rpx auto; 90%;height: 90%; font-size:20rpx;width: 90%;">您好，hoifahlifghbnigsh'kgapgkgg ojfosj</view>
				</view>
			</view>
			<view class="b"><image src="../../static/logo/wx.png"></image></view>
		</view>
		
		<view class="g">
			<view class="bxoe">
				<view class="s">
					<input style="padding-left: 20rpx;" v-model="ser" type="text" placeholder="输入你想咨询的问题" placeholder-style="color:#99999">
					<image src="../../static/icons/biao.png"></image>
				</view>
				<view class="ssss"><image src="../../static/icons/jia.png"></image></view>
				<view @click="get" style="width: 130rpx;"><button type="default">发送</button></view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data(){
			return{
				ser:""
			}
		},
		methods:{
			get(){
				if(this.ser == ""){
					uni.showToast({
						title:"输入框不能为空",
						icon:'none'
					
					})
				}else{
					uni.showToast({
						title:"暂不支持行为",
						icon:'none'
					
					})
				}
			}
		}
	}
</script>

<style scoped lang="scss">
	.ssss{
		width: 10%;
		height: 100%;
		margin: 15rpx auto;
		image{
			width: 50rpx;
			height: 50rpx;
		}
	}
	.s{
		width: 70%;
		height: 100%;
		background-color: #F5F5F5;
		border-radius: 15rpx;
		display: flex;
		align-items: center;
		justify-content: space-between;
		image{
			width: 50rpx;
			height: 50rpx;
		}
	}
	.bxoe{
		width: 95%;
		height: 80rpx;
		margin: 100rpx auto;
		display: flex;
		justify-content: space-between;
	}
	.g{
		width: 100%;
		height: 300rpx;
		background-color: white;
		position: fixed;
		bottom: 0;
		
		
	}
	.d{
		font-size: 30rpx;
		font-weight: 600;
		color: #999999;
	}
	page{
		background-color: #f6f6f6;
	}
	.a{
		width: 80%;
		height: 200rpx;
		margin: 100rpx 0 0 40rpx;
		display: flex;
		justify-content: space-between;
	}
	.b{
		border-radius: 50%;
		width: 80rpx;
		height: 80rpx;
		image{
			width: 100%;
			height: 100%;
			border-radius: 50%;
		}
	}
	.c{
		    width: 84%;
		    height: 87%;
		display: flex;
		    flex-direction: column;
		    justify-content: space-between;
	}
	.e{
		width: 100%;
		height: 70%;
		border-radius: 70rpx;
		background-color: white;
		display: flex;
		    justify-content: space-evenly;
	}
</style>