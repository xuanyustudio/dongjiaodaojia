<template>
	<!-- 服务项目 -->
	<view class="serviceItems">
		<view class="servicetips">
			以下是您目前选择提供的服务项目
		</view>
		<!-- 项目-->
		<view class="serviceItems-wrap">
			<view class="service-item" v-for="(item,index) in serviceData" :key="item.id">
				<view class="service-item-img">
					<image :src="item.cover" style="width: 100%; height: 100%;" mode=""></image>
				</view>
				<view class="service-item-right">
					<view class="name">
						{{item.name}}
					</view>
					<view class="time">
						服务时间 : {{item.duration}}分钟
					</view>
					<view class="money">
						¥ {{item.price}}
					</view>
				</view>
				<view class="icon">
					<radio @click="tigger(item.id)" :value="item.id" :checked="flagObj[`${item.id}`]" />

				</view>
			</view>
		</view>

	</view>
</template>

<script>
	import {
		setService,
		getService
	} from "./api.js"
	export default {
		data() {
			return {
				flagObj: {
					"1": false,
					"2": false,
					"3": false,
					"4": false,
					"5": false,
					"6": false,
					"7": false,
					"8": false,
					"9": false,
					"10": false,
					"11": false,
					"12": false,
					"13": false,
					"14": false,
					"15": false,
					"16": false,
					"17": false,
					"18": false,
					"19": false,
					"20": false,
				},
				service_ids: uni.getStorageSync("infos").service,
				openid: uni.getStorageSync("token"),
				serviceData: [],
			}
		},
		onLoad() {
			this.getServiceItem()
			let serviceItem = uni.getStorageSync("infos").service.split(",")
			console.log(serviceItem)
			for (let key in this.flagObj) {
				serviceItem.forEach(item => {
					if (item == key) {
						this.flagObj[key] = true
					}
				})
			}
		},
		methods: {
			getSetService() {
				setService().then(res => {
					console.log(res)
				})
			},
			changeReadio(e) {
				console.log(e.detail)

			},
			tigger(index) {
				let service_ids = []
				this.flagObj[index] = !this.flagObj[index]
				for (let key in this.flagObj) {
					if (this.flagObj[key] == true) {
						service_ids.push(key)
					}
				}
				this.setServiceItem(service_ids.join(),this.openid)
				


			},
			// 获取服务项目
			getServiceItem() {
				getService().then(res => {
					console.log(res)
					this.serviceData = res.data
				})
			},
			// 设置服务项目
			setServiceItem(service_ids,openid) {
				console.log()
				setService({service_ids,openid}).then(res => {
					console.log(res)
				})
			},
		}
	}
</script>

<style lang="less">
	.serviceItems {
		width: 100%;
		height: 100%;

		.servicetips {
			width: 686rpx;
			height: 88rpx;
			background: #fff;
			font-size: 28rpx;
			font-weight: 400;
			color: #000000;
			line-height: 88rpx;
			text-indent: 24rpx;
			margin: 0 auto;
			margin-top: 24rpx;

		}

		.serviceItems-wrap {
			width: 686rpx;
			margin: 0 auto;

			.service-item {
				position: relative;
				width: 100%;
				height: 202rpx;
				display: flex;
				align-items: center;
				background: #fff;
				margin-top: 24rpx;
				border-radius: 16rpx;

				.service-item-img {
					width: 176rpx;
					height: 154rpx;
					background: #A8A8A8;
					border-radius: 12rpx;
					margin-left: 24rpx;
				}

				.service-item-right {
					margin-left: 24rpx;
					height: 154rpx;
					display: flex;
					flex-direction: column;
					justify-content: space-between;

					.name {
						font-size: 32rpx;
						color: #000000;
					}

					.time {
						font-size: 28rpx;
						color: #666666;
					}

					.money {
						font-size: 36rpx;
						color: #FFB83C;
					}
				}
			}
		}

		.icon {
			position: absolute;
			top: 50%;
			transform: translateY(-50%);
			right: 50rpx;
		}
	}
</style>
