<template>
	<view class="flex-col page">
		<view class="flex-col group">
			<view class="flex-col">
				<view class="list-item flex-col" :key="i" v-for="(item, i) in dataList">
					<text class="text_1">余额充值</text>
					<view class="justify-between">
						<text class="text_3">订单编号：{{ item.order_sn }}</text>
						<text class="text_5">+{{ item.pay_money }}</text>
					</view>
					<text class="text_7">订单时间：{{ item.create_time }}</text>
				</view>
			</view>
		</view>
		<view class="none-img" v-if="total === 0">
			<image
				src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844662420684599.png"
				class="image_1" />
			<text class="text_1">暂无充值记录哦</text>
		</view>
	</view>

</template>

<script>
	export default {
		data() {
			return {
				dataList: [],
				total: null,
				page: 1
			};
		},
		onLoad(){
			this.init();
		},
		onReachBottom(){
			if( this.dataList.length === this.total ){
				return;
			}
			this.page++;
			this.init();
		},
		methods:{
			init(){
				this.$http.post('api/recharge/orderList',{
					page: this.page
				}).then(res =>{
					let { list,total } = res;
					this.dataList.push(...list);
					this.total = total;
				})
			},
		}
	};
</script>

<style scoped lang="scss">
	page {
		background-color: #ffffff;
		width: 100%;
		height: 100%;

		.none-img {
			.image_1 {
				width: 200rpx;
				height: 200rpx;
				margin: 0 auto;
			}

			.text_1 {
				font-size: 30rpx;
				color: #797979;
				padding-top: 30rpx;
				display: flex;
				justify-content: center;
			}
		}

		.group {
			padding-bottom: 592rpx;
			flex: 1 1 auto;
			overflow-y: auto;

			.list-item {
				padding: 32rpx 30rpx 32rpx 32rpx;
				background-color: rgb(255, 255, 255);

				.text_1 {
					color: rgb(0, 0, 0);
					font-size: 32rpx;
					font-weight: 500;
					line-height: 44rpx;
					white-space: nowrap;
				}

				.text_7 {
					margin-top: 6rpx;
					color: rgb(153, 153, 153);
					font-size: 28rpx;
					line-height: 40rpx;
					white-space: nowrap;
				}

				.text_3 {
					margin-top: 16rpx;
					color: rgb(153, 153, 153);
					font-size: 28rpx;
					line-height: 40rpx;
					white-space: nowrap;
				}

				.text_5 {
					font-family: Price;
					color: rgb(24, 181, 103);
					font-size: 48rpx;
					line-height: 58rpx;
					white-space: nowrap;
				}
			}
		}
	}
</style>
