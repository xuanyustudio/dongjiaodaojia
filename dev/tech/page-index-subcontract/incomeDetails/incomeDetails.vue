<template>
	<view class="incomeDetails">
		<view class="tabs" style="color:#d30202 ;">
			<cnpTabs @changeCurrent="changeCurrent" :list="list1" itemWidth="200rpx" />
		</view>
		<!-- 已结算和待结算 -->
		<view class="Settledtobesettled">
			<view class="Settledtobesettled-left">
				<view class="Settled">
					已结算 ¥{{jinE.complete||'0.00'}}
				</view>
				<view class="settled">
					待结算 ¥{{jinE.ongoing||'0.00'}}
				</view>

			</view>
			<view class="Settledtobesettled-right">
				<view class="Settledtobesettled-right-one">
					<image :src="imgUrl+'images/mineImg/bz.png'" mode=""></image>
				</view>
				<view class="Settledtobesettled-right-two">
					<picker mode="date"  fields="month" @change="choiceMonth" >
						<view class="choicemonth">
							<view class="">
							{{month?month:"本"}}月
							</view>
						</view>
					</picker>
				</view>
				<view class="Settledtobesettled-right-three">
					<image :src="imgUrl+'images/indexImg/slices_two/jiantou.png'" mode=""></image>
				</view>
			</view>
		</view>

		<view class="incoomeDetailsList-all">
			<view class="incoomeDetailsList-item" v-for="(item,index) in  list" :key="item.order_sn">
				<view class="orderStatus">订单服务</view>
				<view class="orderNumber">
					订单号：{{item.order_sn}}
				</view>
				<view class="orderTime">
					时间：{{item.create_time}}
				</view>
				
				<view class="type-icon" :style="'backgroundImage:url('+imgUrl+'dwc.png)'" v-if="item.order_status<4">待完成</view>
				<view class="type-icon" :style="'backgroundImage:url('+imgUrl+'images/mineImg/djs.png)'" v-if="item.order_status==4">已完成</view>
				<view class="type-icon" style="color:#ccc" v-if="item.order_status==6">已取消</view>
				<view class="money">
					+{{item.technician_money}}
				</view>
			</view>
		</view>
		
		<view class="p-t5"></view>
		<u-loadmore :status="status" />
		<view class="p-t5"></view>
	</view>
</template>

<script>
	import getTime from "../../common/getTime.js"
	import cnpTabs from "../components/cnpTabs/cnpTabs.vue"
	import {incomeList,incomeStatistics} from "./api.js"
	export default {
		components: {cnpTabs},
		
		data() {
			return {
				imgUrl: this.$store.state.imgUrl,
				
				show: false,
				current: 0, //订单状态
				yearmonth: "", //年月
				month: "", //月
				
				jinE:{},
				
				list1: [
					{name:'全部', type:0},
					{name:'待结算', type:1},
					{name:'已结算', type:2}
				],
				
				list: [],
				
				page: 1,
				status:'loading'
			}
		},
		onLoad(e) {
			// this.getIncomeList(1,0)
			let date = new Date()
			this.month = (date.getFullYear()) + "-" + (date.getMonth() + 1 > 9 ? date.getMonth() + 1 : "0" + (date.getMonth() + 1)) //初始化本月时间
			this.init2()
			this.init()
		},
		onReachBottom() {
			if(this.status == 'loading'){
				this.init()
			}
		},
		methods: {
			init(){
				let data = {
					month: this.month,
					status: this.list1[this.current].type,
					page: this.page
				}
				uni.showLoading({title:'加载中'})
				this.$db.http('index/incomeList',data).then(res => {
					if(res.code==1){
						uni.hideLoading()
						let _list = res.data.list
						if(this.page>1){
							this.list = this.list.concat(_list)
						}else{
							this.list = _list
						}
						if(res.data.total <= this.page){
							this.status = 'nomore'
							return
						}
						this.page++
					}
				})
			},
			
			init2(){
				this.$db.http('index/incomeStatistics',{month:this.month}).then(res => {
					if(res.code==1){
						for (let i in res.data) {
							this.jinE[i] = this.getFloatStr(res.data[i])
						}
					}
				})
			},
			
			getFloatStr(num) {
				num += '';
				num = num.replace(/[^0-9|\.]/g, ''); //清除字符串中的非数字非.字符
			
				if (/^0+/) //清除字符串开头的0
				num = num.replace(/^0+/, '');
				if (!/\./.test(num)) //为整数字符串在末尾添加.00
				num += '.00';
				if (/^\./.test(num)) //字符以.开头时,在开头添加0
				num = '0' + num;
				num += '00';        //在字符串末尾补零
				num = num.match(/\d+\.\d{2}/)[0];
				return num;
			},
			
			changeCurrent(item) {
				this.current = item.index
				this.list = []
				this.page = 1
				this.status = 'loading'
				this.init()
			},
			
			changeShowMonth() {
				this.show = true
			},
			
			async getMonth({month,year}){
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
				this.list = []
				this.page = 1
				this.status = 'loading'
				this.init2()
				this.init()
			}
		}
	}
</script>

<style lang="less">
	.incomeDetails {
		.Settledtobesettled {
			width: 686rpx;
			height: 88rpx;
			background: #FFFFFF;
			border-radius: 16rpx;
			margin: 0 auto;
			margin-top: 24rpx;
			display: flex;
			justify-content: space-between;
			align-items: center;

			.Settledtobesettled-left {
				display: flex;
				align-items: center;

				height: 100%;
				font-size: 24rpx;

				.Settled {
					color: #666666;
					margin-left: 24rpx;

				}

				.settled {
					margin-left: 16rpx;
					color: #65D893;
				}
			}

			.Settledtobesettled-right {
				display: flex;
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
