<template>
	<!-- 提现记录 -->
	<view class="incomeDetails">
		<!-- 已结算和待结算 -->
			<picker mode="date" fields="month" @change="choiceMonth">
		<view class="Settledtobesettled">
		
				<view class="Settledtobesettled-right">
					<view class="Settledtobesettled-right-one">
						<image :src="imgUrl+'images/mineImg/bz.png'" mode=""></image>
					</view>
					<view class="Settledtobesettled-right-two">
						<!-- 	<picker mode="date"  fields="month" @change="choiceMonth" > -->
						<view class="choicemonth">
							<view class="">
								{{month?month:"本"}}月
							</view>
						</view>
						<!-- </picker> -->
					</view>
					<view class="Settledtobesettled-right-three">
						<image :src="imgUrl+'images/indexImg/slices_two/jiantou.png'" mode=""></image>
					</view>
				</view>
			
		</view>
		</picker>

		<!-- 收入列表全部 -->
		<view class="incoomeDetailsList-all">
			<view class="incoomeDetailsList-item" v-for="(item,index) in  withdrawListData" :key="item.order_sn">
				<view class="orderStatus">
					申请提现
				</view>
				<view class="orderNumber">
					订单号：{{item.sn}}
				</view>
				<view class="orderTime">
					时间：{{item.create_time}}
				</view>
				<view class="type-icon" :style="'backgroundImage:url('+imgUrl+item.typeIcon+')'">
					{{item.text}}
				</view>
				<view class="money">
					+{{item.money}}
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import getTime from "../../common/getTime.js"
	import cnpTabs from "../components/cnpTabs/cnpTabs.vue"

	import {
		withdrawList
	} from "./api"
	export default {
		components: {
			cnpTabs,
			// tmMonthCalendar,
			// tmButton
		},
		async onReady() {

			let date = new Date()
			this.month = (date.getFullYear()) + "-" + (date.getMonth() + 1 > 9 ? date.getMonth() + 1 : "0" + (date
				.getMonth() + 1)) //初始化本月时间
			this.getWithdrawList(this.month)



		},
		data() {
			return {
				imgUrl: this.$store.state.imgUrl,
				withdrawListData: [],
				show: false,
				current: 0, //订单状态
				yearmonth: "", //年月
				month: "", //月
				page: 1,
				incomeListData: [], //全部收入明细
				TobesettledData: [], //待结算收入明细
				CompletedData: [], //已完成
				list1: [{
					name: '全部',
				}, {
					name: '待结算',
				}, {
					name: '待完成'
				}, ],
				list: [{
						order_sn: "O2022012452555599", //编号
						create_time: "2022-02-07 09:02", //日期
						technician_money: "50.00" //金额
					},
					{
						order_sn: "202212245457", //编号
						create_time: "2022-02-07 09:02", //日期
						technician_money: "0.00" //金额
					}
				],

			}
		},
		methods: {
			async changeCurrent(item) {

				this.current = item.index
				let obj = {}
				obj.month = this.month
				obj.status = this.current
				obj.page = this.page
				switch (this.current) {
					case 0:
						this.incomeListData = await incomeList(obj)
						// console.log(this.incomeListData)
						break;
					case 1:
						this.TobesettledData = await incomeList(obj)
						this.TobesettledData.data.list.forEach(item => {
							item.typeIcon = 'dwc.png'
							item.typeText = "待完成"
							item.type = "订单服务完成"
						})
						break;
					case 2:
						this.CompletedData = await incomeList(obj)
						this.CompletedData.data.list.forEach(item => {
							item.typeIcon = 'images/mineImg/djs.png';
							item.typeText = "已完成"
							item.type = "订单服务完成"
						})
						break;
					default:
						break;
				}

			},
			changeShowMonth() {
				this.show = true
			},
			async getMonth({
				month,
				year
			}) {
				let newmonth = month > 9 ? month : '0' + month
				this.month = year + "-" + newmonth
				// this.month = month

				let obj = {}
				obj.month = this.month
				obj.status = this.current
				obj.page = this.page
				this.CompletedData = await incomeList(obj)
			},
			// 选择月份
			choiceMonth(e) {
				//通过e.detail.value获取值，获取的是自定义数据的下标
				this.month = e.detail.value
				this.getWithdrawList(this.month)
				console.log(this.month)
			},


			//获取提现记录数据
			getWithdrawList(month) {
				withdrawList({
					month
				}).then(res => {
					console.log(res)
					this.withdrawListData = res.data.list
					this.withdrawListData.forEach(item => {
						if (item.status == 1) {
							item.typeIcon = 'dwc.png'
							item.text = "待审核"
						} else if (item.status == 2) {
							item.text = "已打款"
							item.typeIcon = 'images/mineImg/djs.png'
						}
					})
				})

			},
		}
	}
</script>

<style lang="less">
	.incomeDetails {
		.Settledtobesettled {
			box-sizing: border-box;
			padding: 0 24rpx;
			width: 686rpx;
			height: 88rpx;
			background: #FFFFFF;
			border-radius: 16rpx;
			margin: 0 auto;
			margin-top: 24rpx;
			display: flex;
			justify-content: space-between;
			align-items: center;

			.Settledtobesettled-right {
				position: relative;
				display: flex;
				width: 686rpx;
				margin-right: 12rpx;
				font-size: 28rpx;
				font-weight: 400;
				color: #262626;
				align-items: center;

				.Settledtobesettled-right-one {
					width: 32rpx;
					height: 32rpx;


					image {
						width: 100%;
						height: 100%;

					}
				}

				.Settledtobesettled-right-two {
					margin: 0 12rpx;
				}

				.Settledtobesettled-right-three {
					width: 19rpx;
					height: 50rpx;
					position: absolute;
					right: 0;

					image {
						width: 100%;
						height: 100%;

					}
				}
			}
		}

		.incoomeDetailsList-item {
			position: relative;
			box-sizing: border-box;
			width: 686rpx;
			height: 184rpx;
			background: #FFFFFF;
			border-radius: 16rpx;
			margin: 0 auto;
			margin-top: 24rpx;
			padding-left: 24rpx;
			border: 2rpx solid rgba(0, 0, 0, 0);

			.orderStatus {
				font-size: 32rpx;
				font-weight: 500;
				color: #000000;
				margin-top: 24rpx;
			}

			.orderNumber {
				font-size: 24rpx;
				font-weight: 400;
				color: #666666;
				margin-top: 16rpx;
			}

			.orderTime {
				font-size: 24rpx;
				font-weight: 400;
				color: #666666;
				margin-top: 8rpx;
			}

			.type-icon {
				width: 106rpx;
				height: 48rpx;
				position: absolute;
				right: 0;
				top: 0;
				font-size: 20rpx;
				font-weight: 500;
				color: #F8F8F8;
				text-align: center;
				line-height: 48rpx;
				background-size: 100% 100%;

			}

			.money {
				position: absolute;
				right: 24rpx;
				bottom: 24rpx;
				font-size: 48rpx;
				font-weight: bold;
				color: #000000;
			}
		}
	}
</style>
