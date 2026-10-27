<template>
	<view class="busyHour">
		<view class="tips">
			温馨提示：设置后该时间段不能接单
		</view>
		<!-- 选择星期几 -->
		<view class="week">
			<selectDayofWeek @send="getSonValue"></selectDayofWeek>
		</view>
		<!-- 设置取消忙时 -->
		<view class="Setcancel">
			<view class="Setcancel-left">
				点击设置/取消忙时
			</view>
			<view class="Setcancel-right">
				<view class="serviceTiem">
					服务时段
				</view>
				<view class="cancel">
					忙时/可取消
				</view>
			</view>
			
		</view>
		<!-- 选择时间 -->
		<view class="selectTime">
			<Selecttime ref="mainindex"/>
		</view>
	</view>
</template>

<script>
	import Selecttime from "./components/selectTime.vue";
	import selectDayofWeek from "./components/selectdayofweek.vue";
	
	export default {
		components:{
			selectDayofWeek,
			Selecttime,
		},
		data() {
			return {
				
			}
		},
		onShow() {
			// this.init()
		},
		methods: {
			getSonValue(res){
				console.log(res,"selectDayofWeek打印的res")
				this.$refs.mainindex.init(res);
			},
			// init(){
			// 	uni.showLoading({title:'加载中'})
			// 	this.$db.http('index/busyHour',{date: '20220822'}).then(res => {
			// 		if(res.code==1){
			// 			uni.hideLoading()
			// 		}
			// 	})
			// }
		}
	}
</script>

<style lang="less">
	page{
		background: #Fff;
	}
	.busyHour{
		width: 100%;
		height: 100%;
		background: #fff;
		border: 1rpx solid rgba(0,0,0,0,);
		// 提示
		.tips{
			width: 686rpx;
			height: 64rpx;
			background: #E8FAEF;
			border-radius: 16rpx;
			margin: 0 auto;
			font-size: 28rpx;
			font-weight: 400;
			color: #46D17E;
			line-height: 64rpx;
			text-indent: 20rpx;
			margin-top: 24rpx;
		}
		.Setcancel{
			width: 686rpx;
			height: 62rpx;
			background: #F8F8F8;
			border-radius: 16rpx;
			margin:  0 auto;
			display: flex;
			align-items: center;
			justify-content: space-between;
			margin-top: 40rpx;
			.Setcancel-left{
				font-size: 28rpx;
				font-weight: 400;
				color: #666666;
				margin-left: 24rpx;
			}
			.Setcancel-right{
				display: flex;
				font-size: 24rpx;
				font-weight: 400;
				.serviceTiem{
					color: #46D17E;
					&::before{
						content: '';
						display: inline-block;
						width: 20rpx;
						height: 20rpx;
						background: #46D17E;
						border-radius: 50%;
						margin-right: 5rpx;
					}
				}
			
				.cancel{
					color: #FFB83C;
					margin-right: 24rpx;
					&::before{
						content: '';
						margin-left: 24rpx;
						display: inline-block;
						width: 20rpx;
						height: 20rpx;
						background: #FFB83C;
						border-radius: 50%;
							margin-right: 5rpx;
					}
					
				}
				
			}
		}
		
	}

</style>
