<template>
	<view class="selectdayofweek">
		<!--  -->
		<view :class="CurrenIndex==i?'selectdayofweek-item selectdayofweek-item-active':'selectdayofweek-item' "@click="handleCurrenIndex(item,i)"  v-for="(item,i) in weekaData" :key="item.date">
			<view class="day">
				{{item.week}}
			</view>
			<view class="date">
				{{item.date}}
			</view>

		</view>
		<view style="padding-left:24rpx;"></view>
	</view>


</template>

<script>
	import {
		busyDate
	} from "../api.js"
	export default {
		data() {
			return {
				weekaData:[],
				CurrenIndex:0
			}
		},
		mounted() {
			this.aaaa()
		},
		methods: {
			switchDay(day) {
				switch (day) {
					case "1": return "周一"; break;
					case "2": return "周二"; break;
					case "3": return "周三"; break;
					case "4": return "周四"; break;
					case "5": return "周五"; break;
					case "6": return "周六"; break;
					case "0": return "周日"; break;
					default: break;
				}
			},
			aaaa(){
				this.$db.http('index/busyDate').then(res => {
					console.log(res,"自组见的res")
					if(res.code==1){
						uni.hideLoading()
						let list = res.data
						for(let i in list){
							list[i].week = this.switchDay(list[i].week)
						}
						this.weekaData = list
						this.$emit("send", list[0])
					}
				})
			},
			handleCurrenIndex(item,i){
				if(this.CurrenIndex!=i){
					this.CurrenIndex = i
					this.$emit("send", item)
				}
			}
		}
	}
</script>

<style lang="less">
	.selectdayofweek {
		display: flex;
		height: 130rpx;
		margin-top: 24rpx;
		overflow-x: auto;
	}

	.selectdayofweek-item {
		height: calc(100% - 4rpx);
		border: 2rpx solid #F8F8F8;
		width: 176rpx;
		border-radius: 16rpx;
		background: #F8F8F8;
		flex-shrink: 0;
		margin-left: 24rpx;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;

		.day {
			font-size: 36rpx;
			font-weight: bold;
		}

		.date {
			font-size: 28rpx;
			margin-top: 4rpx;
			font-weight: 400;
		}
	}

	.selectdayofweek-item-active {
		border-color:#46D17E;
		background: #E8FAEF;
		color: #46D17E;

	}
</style>
