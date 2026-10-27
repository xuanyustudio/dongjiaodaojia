<template>
	<view class="selectTime">
		<template v-for="(item,index) in selectTimeData">
			<view :key="index" @click="selectTime(item,index)" class='selectTime-item busyHour-active' v-if="item.busyHour">
				<view class="">
					{{item.startTime}}
				</view>
				<view v-if="item.busyHour" class="line line-busyHour-active">
					<view class="busy">
						<image :src="imgUrl+'images/mineImg/busy.png'" mode=""></image>
					</view>

				</view>
				<!-- <view v-else :class="!item.active?'line':'line line-active'">
				</view>
				<view class="">
					{{item.endTime}}
				</view> -->
			</view>
			<view @click="selectTime(item,index)" :key="index" v-else
				:class="!item.active?' selectTime-item':'selectTime-item selectTime-item-active' ">
				<view class="">
					{{item.startTime}}
				</view>

				<!-- <view :class="!item.active?'line':'line line-active'">
				</view>
				<view class="">
					{{item.endTime}}
				</view> -->
			</view>

		</template>

	</view>
</template>

<script>
	export default {
		// props:{
		// 	riqi:{}
		// },
		data() {
			return {
				imgUrl: this.$store.state.imgUrl,
				list: [
					{startTime: "00:00", endTime: "00:30", active: false, busyHour: false },
					// {startTime: "00:30", endTime: "01:00", active: false, busyHour: false },
					{startTime: "01:00", endTime: "01:30", active: false, busyHour: false },
					// {startTime: "01:30", endTime: "02:00", active: false, busyHour: false },
					{startTime: "02:00", endTime: "02:30", active: false, busyHour: false },
					// {startTime: "02:30", endTime: "03:00", active: false, busyHour: false },
					{startTime: "03:00", endTime: "03:30", active: false, busyHour: false },
					// {startTime: "03:30", endTime: "04:00", active: false, busyHour: false },
					{startTime: "04:00", endTime: "04:30", active: false, busyHour: false },
					// {startTime: "04:30", endTime: "05:00", active: false, busyHour: false },
					{startTime: "05:00", endTime: "05:30", active: false, busyHour: false },
					// {startTime: "05:30", endTime: "06:00", active: false, busyHour: false },
					{startTime: "06:00", endTime: "06:30", active: false, busyHour: false },
					// {startTime: "06:30", endTime: "07:00", active: false, busyHour: false },
					{startTime: "07:00", endTime: "07:30", active: false, busyHour: false },
					// {startTime: "07:30", endTime: "08:00", active: false, busyHour: false },
					{startTime: "08:00", endTime: "08:30", active: false, busyHour: false },
					// {startTime: "08:30", endTime: "09:00", active: false, busyHour: false },
					{startTime: "09:00", endTime: "09:30", active: false, busyHour: false },
					// {startTime: "09:30", endTime: "10:00", active: false, busyHour: false },
					{startTime: "10:00", endTime: "10:30", active: false, busyHour: false },
					// {startTime: "10:30", endTime: "11:00", active: false, busyHour: false },
					{startTime: "11:00", endTime: "11:30", active: false, busyHour: false },
					// {startTime: "11:30", endTime: "12:00", active: false, busyHour: false },
					{startTime: "12:00", endTime: "13:00", active: false, busyHour: false },
					// {startTime: "12:30", endTime: "13:00", active: false, busyHour: false },
					{startTime: "13:00", endTime: "13:30", active: false, busyHour: false },
					// {startTime: "13:30", endTime: "14:00", active: false, busyHour: false },
					{startTime: "14:00", endTime: "14:30", active: false, busyHour: false },
					// {startTime: "14:30", endTime: "15:00", active: false, busyHour: false },
					{startTime: "15:00", endTime: "15:30", active: false, busyHour: false },
					// {startTime: "15:30", endTime: "16:00", active: false, busyHour: false },
					{startTime: "16:00", endTime: "16:30", active: false, busyHour: false },
					// {startTime: "16:30", endTime: "17:00", active: false, busyHour: false },
					{startTime: "17:00", endTime: "17:30", active: false, busyHour: false },
					// {startTime: "17:30", endTime: "18:00", active: false, busyHour: false },
					{startTime: "18:00", endTime: "18:30", active: false, busyHour: false },
					// {startTime: "18:30", endTime: "19:00", active: false, busyHour: false },
					{startTime: "19:00", endTime: "19:30", active: false, busyHour: false },
					// {startTime: "19:30", endTime: "20:00", active: false, busyHour: false },
					{startTime: "20:00", endTime: "20:30", active: false, busyHour: false },
					// {startTime: "20:30", endTime: "21:00", active: false, busyHour: false },
					{startTime: "21:00", endTime: "21:30", active: false, busyHour: false },
					// {startTime: "21:30", endTime: "22:00", active: false, busyHour: false },
					{startTime: "22:00", endTime: "22:30", active: false, busyHour: false },
					// {startTime: "22:30", endTime: "23:00", active: false, busyHour: false },
					{startTime: "23:00", endTime: "23:30", active: false, busyHour: false },
					// {startTime: "23:30", endTime: "00:00", active: false, busyHour: false }
				],
				selectTimeData:[],
				riqi:'',
				time:'',
				zt:''
			}
		},
		onLoad() {
			this.init2()
		},
		methods: {
			init(a){
				this.riqi = a.detailed_date
				this.selectTimeData = []
				let list = this.list,
					s = null,
					e = null
				for(let i in list){
					list[i].active=false
					list[i].busyHour=false
					if(a.start==list[i].startTime){
						s = i;
					}
					// if(a.end==list[i].endTime){
					// 	e = i
					// };
				}
				setTimeout(()=>{
					for(let ii in list){
						// && ii <= Number(e)
						if(ii >= Number(s)){
							this.selectTimeData.push(list[ii])
						}
					}
					this.init2()
				})
			},
			init2(){
				uni.showLoading({title:'加载中'})
				this.$db.http('index/busyHour',{date: this.riqi}).then(res => {
					if(res.code==1){
						uni.hideLoading()
						let list = res.data
						let time = this.selectTimeData
						for(let a in time){ time[a].busyHour = false}
						if(list.length){
							for(let i in list){
								for(let ii in time){
									if(list[i].type==2 && list[i].hour==time[ii].startTime){
										time[ii].busyHour = true
									}
									if(list[i].type==1 && list[i].hour==time[ii].startTime){
										time[ii].active = true
									}
								}
							}
						}else{
							for(let ii in time){
								time[ii].busyHour = false
							}
						}
					}
				})
			},
			selectTime(item,i) {
				console.log(1)
				if(item.active){ uni.showToast({title:'服务时间段，不可设置',icon:'none'}); return}
				
				let data = {
					date: this.riqi,
					hour: item.startTime
				}
				uni.showLoading({title:'加载中'})
				this.$db.http('index/setBusyHour',data).then(res => {
					if(res.code==1) {
						if(this.selectTimeData[i].busyHour) uni.showToast({title:'已取消',icon:'none'});
						else uni.showToast({title:'设置成功'});
						this.selectTimeData[i].busyHour = this.selectTimeData[i].busyHour ? false : true
					}else{
						uni.showToast({title:res.msg,icon:'none'})
					}
				})
			}
		}
	}
</script>

<style lang="less">
	.selectTime {
		display: flex;
		flex-wrap: wrap;
		flex-shrink: 0;
		padding:8rpx 0 20rpx 16rpx;

		.selectTime-item {
			position: relative;
			width: 124rpx;
			height: 160rpx;
			border: 2rpx solid #E4E4E4;
			background: #fff;
			border-radius: 16rpx;
			margin-left: 14rpx;
			margin-top: 16rpx;
			color: #262626;
			font-size: 28rpx;
			font-weight: 400;
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;

			// .line {
			// 	width: 1rpx;
			// 	height: 30rpx;
			// 	background: #262626;
			// 	margin: 4rpx 0;
			// }

			.line-active {
				background: #46D17E;
			}

			.line-busyHour-active {
				background: #FFB83C;

				.busy {
					position: absolute;
					bottom: 0;
					right: -1rpx;
					width: 32rpx;
					height: 26rpx;

					image {
						width: 100%;
						height: 100%;
					}
				}
			}
		}

		.selectTime-item-active {
			width: 120rpx;
			height: 160rpx;
			border: 2rpx solid #46D17E;
			background: #E8FAEF;
			color: #46D17E;
		}

		.busyHour-active {
			border-color:#FFB83C;
			background: #FFFAEF;
			color: #FFB83C;
		}
	}
</style>
