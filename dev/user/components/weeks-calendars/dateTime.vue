<template>
	<view class="date-time">
		<view class="time-box"
		 :class="[{'active':dataAll.start === item.time || dataAll.end === item.time}, {'pitch-on':index > s && index < e}, {'new-show': item.newShow}]"
		 v-for="(item, index) in timeData" @click="clickTime(item)" :key="index">
			<view class="time-txt">{{item.time}}</view>
			<image class="img" v-if="dataAll.start === item.time || dataAll.end === item.time" src="data:image/png;base64,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" mode=""></image>
			<view class="time-maths" v-else>{{item.timeMaths}}</view>
		</view>
	</view>
</template>

<script>
export default{
	data() {
		return {
			s: 0,
			e: 0,
			dataAll: {
				start: null,
				end: null
			},
			timeData: [
				{'time': '00:30', 'timeMaths': "可预约"},
				{'time': '1:00', 'timeMaths': "可预约"},
				{'time': '1:30', 'timeMaths': "可预约"},
				{'time': '2:00', 'timeMaths': "可预约"},
				{'time': '2:30', 'timeMaths': "可预约"},
				{'time': '3:00', 'timeMaths': "可预约"},
				{'time': '3:30', 'timeMaths': "可预约"},
				{'time': '4:00', 'timeMaths': "可预约"},
				{'time': '4:30', 'timeMaths': "可预约"},
				{'time': '5:00', 'timeMaths': "可预约"},
				{'time': '5:30', 'timeMaths': "可预约"},
				{'time': '6:00', 'timeMaths': "可预约"},
				{'time': '6:30', 'timeMaths': "可预约"},
				{'time': '7:00', 'timeMaths': "可预约"},
				{'time': '7:30', 'timeMaths': "可预约"},
				{'time': '8:00', 'timeMaths': "可预约"},
				{'time': '8:30', 'timeMaths': "可预约"},
				{'time': '9:00', 'timeMaths': "可预约"},
				{'time': '9:30', 'timeMaths': "可预约"},
				{'time': '10:00', 'timeMaths': "可预约"},
				{'time': '10:30', 'timeMaths': "可预约"},
				{'time': '11:00', 'timeMaths': "可预约"},
				{'time': '11:30', 'timeMaths': "可预约"},
				{'time': '12:00', 'timeMaths': "可预约"},
				{'time': '12:30', 'timeMaths': "可预约"},
				{'time': '13:00', 'timeMaths': "可预约"},
				{'time': '13:30', 'timeMaths': "可预约"},
				{'time': '14:00', 'timeMaths': "可预约"},
				{'time': '14:30', 'timeMaths': "可预约"},
				{'time': '15:00', 'timeMaths': "可预约"},
				{'time': '15:30', 'timeMaths': "可预约"},
				{'time': '16:00', 'timeMaths': "可预约"},
				{'time': '16:30', 'timeMaths': "可预约"},
				{'time': '17:00', 'timeMaths': "可预约"},
				{'time': '17:30', 'timeMaths': "可预约"},
				{'time': '18:00', 'timeMaths': "可预约"},
				{'time': '18:30', 'timeMaths': "可预约"},
				{'time': '19:00', 'timeMaths': "可预约"},
				{'time': '19:30', 'timeMaths': "可预约"},
				{'time': '20:00', 'timeMaths': "可预约"},
				{'time': '20:30', 'timeMaths': "可预约"},
				{'time': '21:00', 'timeMaths': "可预约"},
				{'time': '21:30', 'timeMaths': "可预约"},
				{'time': '22:00', 'timeMaths': "可预约"},
				{'time': '22:30', 'timeMaths': "可预约"},
				{'time': '23:00', 'timeMaths': "可预约"},
				{'time': '23:30', 'timeMaths': "可预约"},
				{'time': '24:00', 'timeMaths': "可预约"},
				{'time': '24:30', 'timeMaths': "可预约"},
			]
		}
	},
	props: {
		startTime: {
			type: String,
			default: '8:00'
		},
		endTime: {
			type: String,
			default: '20:00'
		},
		disableTime: {
			default: []
		}
	},
	mounted() {
		/* 获取展示的时间段 */
		let startTimeIndex = this.timeData.findIndex((item)=>{
			return item.time == this.startTime
		})
		let endTimeIndex = this.timeData.findIndex((item)=>{
			return item.time == this.endTime
		})
		this.timeData = this.timeData.slice(startTimeIndex, endTimeIndex+1)
		/* end */
		/* 判断已被预约的 */
		this.disableTime.forEach((item)=>{
			let s = this.timeData.findIndex((it)=>{
				return it.time == item.startTime
			})
			let e = this.timeData.findIndex((it)=>{
				return it.time == item.endTime
			})
			if(s !== -1) {
				this.timeData = this.timeData.map((item, index)=>{
					if(index >= s && index <= e) {
						return {time: item.time, timeMaths: '已预约', newShow: true}
					}else {
						return item
					}
				})
			}
		})
	},
	methods:{
		clickTime(item) {
			if(item.newShow) {
				uni.showToast({
				    title: '当前时间已有预约，请重新预约',
				    duration: 3000,
					icon: 'none'
				});
				return
			} 
			if(this.dataAll.start === item.time) {
				this.dataAll.start = null;
				this.dataAll.end = null;
				this.$emit('dataAll', this.dataAll)
				this.s = 0;
				this.e = 0;
				return
			}else if(this.dataAll.end === item.time) {
				this.dataAll.end = null;
				this.s = 0;
				this.e = 0;
				return
			}else if(this.dataAll.start === null) {
				this.dataAll.start = item.time;
				this.$emit('dataAll', this.dataAll)
				return
			}else if(this.dataAll.start !== null && this.dataAll.end === null) {
				this.dataAll.end = item.time;
			}else if(this.dataAll.end !== null && this.dataAll.start !== null) {
				this.dataAll.start = null;
				this.dataAll.end = null;
				this.s = 0;
				this.e = 0;
				this.clickTime(item)
				return
			}
			this.s = this.timeData.findIndex((item)=>{
						return item.time == this.dataAll.start
					})
			this.e = this.timeData.findIndex((item)=>{
						return item.time == this.dataAll.end
					})
			if(this.s > this.e) {
				this.dataAll.start = null;
				this.dataAll.end = null;
				this.clickTime(item)
			}
			if(this.timeDataShow()) {
				uni.showToast({
					title: '当前时间段已有预约，请重新预约',
					duration: 3000,
					icon: 'none'
				});
				this.dataAll.start = null;
				this.dataAll.end = null;
				this.s = 0;
				this.e = 0;
				this.$emit('dataAll', this.dataAll)
			}
			
		},
		timeDataShow() {
			for(let i = 0; i < this.timeData.length; i++) {
				if(this.s <= i && this.e >= i) {
					if(this.timeData[i].newShow) {
						return true
					}
				}
			}
		}
	}
}
</script>

<style lang="less" scoped>
	.date-time{
		margin: 30rpx 32rpx;
	}
	.time-box{
		line-height: 72rpx;
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 0 24rpx;
		.time-txt{
			color: #333333;
			font-size: 34rpx;
		}
		.time-maths{
			color: #BCCADC;
			font-size: 28rpx;
		}
		.img{
			width: 40rpx;
			height: 40rpx;
		}
	}
	.time-box.active {
		background-color: #EEF3FF;
		border-radius: 20rpx;
		.time-txt{
			color: #376EF1;
			font-size: 38rpx;
		}
		.time-maths{
			color: #BCCADC;
			font-size: 28rpx;
		}
	}
	.time-box.pitch-on{
		background-color: #F9FBFF;
		.time-txt{
			color: #B2C9FF;
			font-size: 34rpx;
		}
	}
	.time-box.new-show{
		background-color: #F6F6F6;
		// border-radius: 20rpx;
		.time-txt{
			color: #78839E;
			font-size: 32rpx;
		}
		.time-maths{
			color: #78839E;
			font-size: 32rpx;
		}
	}
</style>
