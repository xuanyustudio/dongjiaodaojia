<template>
	<!-- 服务时间 -->
	<view class="serviceTime">
		<view class="serviceTime-wrap">
			<view class="serviceTime-header">
				<view class="serviceTime-header-text">工作时间</view>

				<view class="serviceTime-header-time" style="display: flex;flex:1;padding-left:60rpx;text-align: center;">
					<picker mode="time" :value="time" start="00:00" end="23:59" @change="bindStartTimeChange" style="flex: 1;">
						{{startTime}}
					</picker>
					<view>-</view>
					<picker mode="time" :value="time" start="00:00" end="23:59" @change="bindEndTimeChange" style="flex: 1;">
						{{endTime}}
					</picker>
				</view>
			</view>
			<u-line></u-line>

			<view class="serviceTime-day">
				<view @click="handleWeek(index)" :class="item.active? 'day-item day-item-active':'day-item'"
					v-for="(item,index) in dayData" :key="item.day">
					{{item.day}}
				</view>
			</view>
		</view>
		<!-- 提交 -->
		<view class="submit" @click="submit">提交</view>

	</view>
</template>

<script>
	import {
		setServiceDate
	} from "./api.js"
	export default {
		data() {
			return {
				startTime: "",
				endTime: "",
				week: "",
				time: 'hh:mm',
				openid: uni.getStorageSync("token"),
				dayData: [
					{day: "周一",active:false},
					{day: "周二",active:false},
					{day: "周三",active:false},
					{day: "周四",active:false},
					{day: "周五",active:false},
					{day: "周六",active:false},
					{day: "周日",active:false}
				]
			}
		},
		onShow() {
			this.init()
		},
		methods: {
			init(){
				uni.showLoading({title:'加载中'})
				this.$db.http('my.Index/index').then(res => {
					if(res.code==1){
						uni.hideLoading()
						let info = res.data.info || {}
						this.startTime = info.start_time || ''
						this.endTime = info.end_time || ''
						this.week = info.week || ''
						this.dayData.forEach(item => { item.active = false })
						if (this.week) {
							this.week.split(',').forEach(v => {
								const n = Number(v)
								if (n === 0) this.dayData[6].active = true
								else if (n >= 1 && n <= 6) this.dayData[n - 1].active = true
							})
						}
					}
				})
			},
			setServicedate() {
				setServiceDate().then(res => {
					console.log(res)
				})
			},
			bindStartTimeChange(e) {
				this.startTime=e.target.value
			},
			bindEndTimeChange(e) {
				let s = Number(this.startTime.replace(':','')),
					end = Number(e.target.value.replace(':',''))
				if(s >= end){ uni.showToast({title: '下班时间需大于'+this.startTime,icon:'none'}); return}
				this.endTime=e.target.value
			},
			handleWeek(index) {
				this.dayData[index].active = this.dayData[index].active ? false : true
				this.quzhi(this.dayData)
			},
			quzhi(a){
				let b = []
				for(let i in a){
					if(a[i].active){
						i = i == 6 ? 0 : Number(i)+1
						b.push(i)
					}
				}
				this.week = b.toString()
			},
			// 提交修改服务时间
			submit() {
				let s = Number(this.startTime.replace(':','')),
					end = Number(this.endTime.replace(':',''))
				if(s >= end){ uni.showToast({title: '下班时间需大于'+this.startTime,icon:'none'}); return}
				let obj = {}
				obj.week = this.week
				obj.start_time = this.startTime
				obj.end_time = this.endTime
				obj.openid = this.openid
				setServiceDate(obj).then(res => {
					if(res.code ==1){
						uni.showToast({title:"提交成功",icon:"none"})
						setTimeout(()=>{
							uni.navigateBack()
						},1000)
					}
				})
			}
		}
	}
</script>

<style lang="less">
	.serviceTime {
		.serviceTime-wrap {
			width: 686rpx;
			margin: 0 auto;
			margin-top: 24rpx;
			background: #fff;
			border-radius: 16rpx;

			.serviceTime-header {
				box-sizing: border-box;
				width: 100%;
				height: 96rpx;
				display: flex;
				justify-content: space-between;
				align-items: center;
				padding: 0 24rpx;

				.serviceTime-header-text {
					font-size: 32rpx;
					font-weight: 400;
					color: #666666;
				}

				.serviceTime-header-time {
					font-size: 32rpx;
					font-weight: 400;
					color: #000000;
				}

			}

			.serviceTime-day {
				height: 256rpx;
				width: 100%;
				display: grid;
				grid-template-columns: 142rpx 142rpx 142rpx 142rpx;
				grid-template-rows: 96rpx 96rpx;
				justify-content: center;
				align-content: center;
				gap: 24rpx;
				text-align: center;
				line-height: 96rpx;

				.day-item {
					background: #F8F8F8;
					border-radius: 16rpx;
					font-size: 32rpx;
					font-weight: 400;
					color: #000000;
					border: 2rpx solid #F8F8F8;
				}

				.day-item-active {
					background: rgba(70, 209, 126, 0.12);
					border-color: #46D17E;
					border-radius: 16rpx;
					color: #46D17E;
				}
			}
		}

		.submit {
			width: 686rpx;
			height: 88rpx;
			background: #0DA17D ;
			border-radius: 16rpx;
			margin: 0 auto;
			margin-top: 48rpx;
			text-align: center;
			line-height: 88rpx;
			font-weight: 500;
			color: #FFFFFF;
			font-size: 32rpx;
		}
	}
</style>
