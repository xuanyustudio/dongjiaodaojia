<template>
	<view class="grid">
		<uni-grid ref="grid" :column="4" :showBorder="false" :highlight="false" @change="changeItem($event)">
			<uni-grid-item v-for="(item,index) in gridData" :key="item.text" :index="item.id">

				<button open-type='contact'  class="grid-item" v-if="item.id==6">
					<image :src="item.icon" mode=""></image>
					<text class="text">{{item.text}}</text>
				</button>
				<view v-else  class="grid-item">

					<image :src="item.icon" mode=""></image>
					<text class="text">{{item.text}}</text>

				</view>
			</uni-grid-item>

		</uni-grid>
	</view>

</template>

<script>
	export default {
		props:{
			phone:{
				type:String,
			}
		},
		data() {
			return {
				imgUrl: this.$store.state.imgUrl,
				gridData: [{
						icon: "../../static/index/j.png",
						path: "/page-index-subcontract/incomeDetails/incomeDetails",
						text: "收入明细",
						id: 0

					},
					{
						icon: "../../static/index/g.png",
						path: "/page-index-subcontract/orderStatistics/orderStatistics",
						text: "订单统计",
						id: 1

					},
					{
						icon: "../../static/index/h.png",
						path: "/page-index-subcontract/myWallet/myWallet",
						text: "收入提现",
						id: 2

					},
					{
						icon: "../../static/index/i.png",
						path: "/page-index-subcontract/withdrawalAccount/withdrawalAccount",
						text: "提现账户",
						id: 3

					},
					{
						icon: "../../static/index/e.png",
						path: "/page-mine-subcontract/evaluate/evaluate",
						text: "用户评价",
						id: 4

					},
					{
						icon: "../../static/index/a.png",
						path: "/page-index-subcontract/signIn/signIn",
						text: "服务区域",
						id: 5

					},
					{
						icon: "../../static/index/d.png",
						path: "",
						text: "平台客服",
						id: 6

					},
				],
				imgArray: [{
						src: "images/indexImg/slices_three/lb.png",
					},
					{
						src: "images/indexImg/slices_three/lb.png",
					},
					{
						src: "images/indexImg/slices_three/lb.png",
					}
				],
			}
		},
		methods: {
			resize() {
				this.$nextTick(() => {
					this.$refs.grid && this.$refs.grid.init(true)
				})
			},
			changeItem(e) {
				let token = this.$db.get('token')
				let index = e.detail.index
				if (token) {
					if (index == 6) {
						// console.log(this.$store.state.phones,"this.phone1");
						uni.makePhoneCall({
							phoneNumber: this.phone //仅为示例 this.servicePhone是你要拨打的电话号码
						});
					}else {
						uni.navigateTo({
							url: this.gridData[index].path
						})
					}
					
				} else {
					uni.navigateTo({
						url: "/pages/login/login"
					})
				}
			},
		}
	}
</script>

<style lang="less" scoped>
	.grid {
		margin: 0 30rpx;
		height: 306rpx;
		background: #FFFFFF;
		border-radius: 24rpx;

		.grid-item {
			text-align: center;
			background-color: #FFFFFF;
			display: flex;
			flex-flow: column;
			align-items: center;
			width: 100%;
			height: 150rpx;
		}

		image {
			width: 64rpx;
			height: 64rpx;
			margin: 0 auto;
			margin-top: 25rpx;
		}

		.text {
			font-size: 24rpx;
			font-family: SourceHanSansCN-Light;
			color: #33342E;
			margin-top: 10rpx;
			white-space: nowrap;
		}
	}
</style>
