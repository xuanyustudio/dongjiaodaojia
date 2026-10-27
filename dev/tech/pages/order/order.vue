<template>
	<view class="order">
		<uni-nav-bar statusBar :fixed="true" class="navBar" :height="'88rpx'" :backgroundColor="'#fff'"
			style="font-size: 36rpx;" :color="'#000'" :border="false" title="订单"></uni-nav-bar>
		<!-- 搜索框 -->
		<view class="search">
			<searchBox @emitSN="changeSN" :order_sn="order_sn" />
		</view>
		<!-- 切换卡 -->
		<view class="tabs">
			<u-tabs :current='activeIndex' @click="changeIndex" :list="list1" lineWidth="30" lineColor="#46D17E" :activeStyle="{
					color: '#000',
					fontWeight: '500',
					transform: 'scale(1.02)'
				}" :inactiveStyle="{
					color: '#666',
					transform: 'scale(1)'
				}" itemStyle="padding-left: 31rpx; padding-right: 32rpx; height: 88rpx;font-size:28rpx">
			</u-tabs>
		</view>
		<view class="order-list">
			<view class="newOrder">
				<view class="text-center p-t50" v-if="orderData.length<1">
					<image :src="imgUrl+'no_order.png'" mode="widthFix" class="img" style="width:232rpx;"></image>
					<view class="f-s14 co_666 p-t10" v-if="token">接单要快 姿势要帅</view>
					<view class="p-t10 dd-flex" v-else><button type="default" class="regiter-bottom h30 f-s14 border-radius-100 p-l30 p-r30" @tap="navigateTo('/pages/login/login')">请登录</button></view>
				</view>
				<view class="newOrder-item" @click="jumpOrderDetails(item)" v-for="(item,index) in orderData" :key="item.id">
					<view class="newOrder-item-top">
						<!-- 订单号 -->
						<view class="orderNum" @click.stop="copy(item.order_sn)">
							订单号：{{item.order_sn}}
						</view>
						<!-- 订单类型 -->
						<view :class="['newOrder-item-top-orderType',{co_666:item.order_status==4},{co_999:item.order_status==6}]">
							{{item.order_status==1?'新订单':item.order_status==2?'待服务':item.order_status==3?'进行中':item.order_status==4?'已完成':item.order_status==5?'已评价':'已取消'}}
						</view>
					</view>
					<view class="newOrder-item-center">
						<!-- 服务类型 -->
						<view class="service-type">
							<!-- 类型 -->
							<view class="service-type-left">
								<view class="service-type-left-icon"
									:style="{backgroundImage: 'url( '+imgUrl + item.typeIcon + ')'}">
									<!-- {{item.name.substr(item.name.length-2)}} -->
									<!-- {{item.name===''?item.name.substr(item.name.length-2):item.name}} -->
									项目
								</view>
								<view class="service-type-left-text">
									{{item.service_name}}
								</view>

							</view>
							<!-- 预估收入 -->
							<view class="service-type-right">
								<view class="income">预估收入</view>
								<view class="money">{{item.technician_money}}</view>
							</view>
						</view>
						<!-- 服务时长 -->
						<view class="service-duration">
							<!-- 时长 -->
							<view class="service-duration-left">
								<view class="service-duration-left-icon">
									时长
								</view>
								<view class="service-duration-left-text">
									{{item.duration}}分钟
								</view>
							</view>
							<!-- 出行费 -->
							<view class="travelExpenses">
								（包含出行费{{item.fare}}元）
							</view>

						</view>
						<!-- 服务地址 -->
						<view class="service-address">
							<text>服务地址:</text><text>{{item.province}}{{item.city}}{{item.district}}{{item.address}}</text>
						</view>
						<!-- 服务时间 -->
						<view class="service-time">
							<text>服务时间:</text><text>{{item.appointment_time}}</text>
						</view>

					</view>
					<view class="newOrder-item-bottom">
						<!-- 客户头像 -->
						<view class="customerAvatar">
							<image :src="item.avatar" mode=""></image>

						</view>
						<!-- 客户尾号和信用分 -->
						<view class="phoneAndScore">
							<view class="phone">
								<!-- 尾号{{.mobile.substr(7)}} -->
								尾号{{item.mobile ? String(item.mobile).slice(-4) : ''}}
							</view>
							<view class="score">
								信用分{{item.praise}}
							</view>
						</view>
					</view>
				</view>
			</view>
			<!-- <view class="grace-loading" v-if="isLoadAll">{{ loadingTxt }}</view> -->
		</view>

	</view>
</template>

<script>
	import searchBox from "../../components/searchBox/searchBox.vue"
	import {
		orderList
	} from "./api.js"
	export default {
		components: {
			searchBox
		},
		data() {
			return {
				imgUrl: this.$store.state.imgUrl,
				activeIndex: 0,
				order_sn:'',
				orderSN: "",
				active:'',
				list1: [
					{name: '新订单',type: 1,ser:null},
					{name: '待服务',type: 2,ser:null},
					{name: '进行中',type: 3,ser:null},
					{name: '已取消',type: 6,ser:null},
					{name: '已完成',type: 4,ser:null},
				],
				orderData: [],//总数据
				token:'',
					totalPages: 1,  // 总页数
					// loadingTxt: '加载中...',
					isLoadAll: false,
					paramsData: {
						pageNum: 1,
						pageSize: 10,
					},
				

				
			}
		},
		// uni-app页面滚动到底部的事件，常用于上滑加载下一页数据
		onReachBottom() {
			this.paramsData.pageNum++;
			this.isLoadAll = true;
			this.getOrderList();
		},
		onLoad(option) {
			this.applyTitle(option && option.title)
		},
		onShow() {
			const pages = getCurrentPages()
			const cur = pages[pages.length - 1]
			if (cur && cur.options && cur.options.title !== undefined && cur.options.title !== '') {
				this.applyTitle(cur.options.title)
			}
			this.token = this.$db.get('token')
			if (!this.token) return
			this.paramsData.pageNum = 1
			this.orderData = []
			this.getOrderList()
		},
		methods: {
			applyTitle(title) {
				const n = Number(title)
				if (!isNaN(n) && n >= 0 && n < this.list1.length) {
					this.activeIndex = n
				}
			},
			copy(text){
				uni.setClipboardData({
					data:text,
					success() {
						uni.showToast({
							title:'已复制到粘贴板'
						})
					}
				})
			},
			changeIndex(item) {
				console.log(item.index,'250')
				if (this.activeIndex == item.index) return false;
				this.activeIndex = item.index
				this.paramsData.pageNum=1
				this.orderData =[]
				this.getOrderList(this.list1[item.index].type)
			},
			//获取订单列表
			getOrderList(order_status) {
				const tab = this.list1[this.activeIndex] || this.list1[0]
				let data = {
					order_status: order_status || tab.type,
					page: this.paramsData.pageNum
				}
				if(this.order_sn) data.order_sn = this.order_sn
				orderList(data).then(res => {
					const body = res.data || {}
					const list = Array.isArray(body.list) ? body.list : (Array.isArray(body) ? body : [])
					if (this.paramsData.pageNum === 1) this.orderData = []
					this.orderData = [...this.orderData, ...list]
					this.orderData.forEach(item => {
						item.typeIcon = 'images/orderImg/ordertype2.png'
					})
				})
			},
			//获取搜索的订单号
			changeSN(sn) {
				this.orderSN = sn
				this.order_sn = sn
				this.getOrderList(this.list1[this.activeIndex].type)
			},
			//跳转订单详情
			jumpOrderDetails(item){
				// console.log(item,item.order_status)
				if(item.order_status == 1 || item.order_status == 2 || item.order_status == 6){
					this.navigateTo(`/page-index-subcontract/orderDetail/index?order_sn=${item.order_sn}`)
				}else if(item.order_status == 3){
					this.navigateTo(`/page-index-subcontract/orderIng/index?order_sn=${item.order_sn}`)
				}else if(item.order_status == 4){
					this.navigateTo(`/page-index-subcontract/orderOver/index?order_sn=${item.order_sn}`)
				}
			},
			navigateTo(e){
				uni.navigateTo({
					url: e
				})
			}
		}
	}
</script>

<style lang="less">
	page{ background:#fff;}
	.order {
		width: 100%;height: 100%;
		.search {text-align: center;background: #fff;}
		.tabs {
			height: 88rpx;
			background: #fff;
			display: flex;
			align-items: center;
		}
	}

	// 新订单
	.newOrder {
		.newOrder-item {
			// height: 472rpx;
			background: #FFFFFF;
			// margin-top: 24rpx;
			border-bottom:24rpx solid #f8f8f8;
			padding: 0 30rpx;
			&:first-child{ border-top:24rpx solid #f8f8f8;}
			// 每项订单头部
			.newOrder-item-top {
				// width: 686rpx;
				height: 86rpx;
				border-bottom: 3rpx solid #F4F4F4;
				display: flex;
				align-items: center;
				justify-content: space-between;
				font-size: 28rpx;
				font-family: PingFang SC;
				font-weight: 400;

				// 订单号
				.orderNum {
					color: #666666;
				}

				.newOrder-item-top-orderType {
					font-family: PingFang SC;
					color: #46D17E;
				}
			}

			.newOrder-item-center {
				// width: 686rpx;
				// height: 220rpx;
				// margin:0 30rpx;
				border-bottom: 1px solid #F4F4F4;
				padding-bottom:20rpx;
				// 服务类型
				.service-type {
					display: flex;
					align-items: center;
					justify-content: space-between;
					height: 48rpx;
					// width: 100%;
					padding-top: 26rpx;

					.service-type-left {
						display: flex;

						.service-type-left-icon {
							display: flex;
							align-items: center;
							justify-content: center;
							width: 120rpx;
							height: 40rpx;
							font-size: 24rpx;
							font-weight: 500;
							text-align: center;
							color: #FFFFFF;
							// background: url(../../static/images/orderImg/ordertype.png);
							background-size: 100% 100%;
						}

						.service-type-left-text {
							font-size: 28rpx;
							font-weight: bold;
							color: #000;
							margin-left: 10rpx;
						}
					}

					.service-type-right {
						display: flex;
						align-items: center;

						.income {
							font-size: 24rpx;
							font-weight: 400;
							color: #666666;
						}

						.money {
							font-size: 40rpx;
							font-weight: bold;
							color: #000000;
							margin-left: 14rpx;
						}
					}
				}

				// 时长
				.service-duration {
					display: flex;
					align-items: center;
					justify-content: space-between;
					height: 37rpx;
					margin-top: 13rpx;

					.service-duration-left {
						display: flex;
						align-items: center;

						.service-duration-left-icon {
							display: flex;
							align-items: center;
							justify-content: center;
							width: 72rpx;
							height: 36rpx;
							background: #262626;
							border-radius: 8rpx;
							color: #FFFFFF;
							font-weight: 400;
							font-size: 24rpx;
						}

						.service-duration-left-text {
							margin-left: 10rpx;
							font-size: 28rpx;
							font-weight: 400;
							color: #262626;
						}
					}

					.travelExpenses {
						font-size: 24rpx;
						font-weight: 400;
						color: #999999;
					}

				}

				.service-address {
					height: 34rpx;
					font-size: 24rpx;
					font-weight: 400;
					color: #666666;
					margin-top: 26rpx;
				}

				.service-time {
					font-size: 24rpx;
					font-weight: 400;
					color: #666666;
					margin-top: 12rpx;
				}
			}

			.newOrder-item-bottom {
				width: 100%;
				height: 136rpx;
				display: flex;
				align-items: center;

				.customerAvatar {
					width: 88rpx;
					height: 88rpx;
					border-radius: 50%;
					border: 2rpx red solid;

					image {
						width: 100%;
						height: 100%;
						border-radius: 50%;
					}
				}

				.phoneAndScore {
					margin-left: 16rpx;

					.phone {
						font-size: 24rpx;
						font-weight: bold;
						color: #000000;
					}

					.score {
						font-size: 24rpx;
						font-weight: 400;
						color: #666666;
						margin-top: 6rpx;
					}
				}
			}

		}
	}
</style>
