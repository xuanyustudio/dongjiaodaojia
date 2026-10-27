<template>
	<view class="flex-col page">
		<view class="flex-col group">
			<view class="flex-col">
				<view class="list-item justify-between" :key="i" v-for="(item, i) in dataList">
					<view class="flex-col">
						<text class="text_1">{{ item.type == 1 ? '下单' : item.type == 3 ? '开通会员' : '充值' }}消费</text>
						<text class="text_3">订单编号：{{ item.order_sn }}</text>
						<text class="text_5">订单时间：{{ item.create_time }}</text>
					</view>
					<text class="text_7">-{{ item.money }}</text>
				</view>
			</view>
		</view>
		<view class="none-img" v-if="total === 0">
			<image
				src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844662420684599.png"
				class="image_1" />
			<text class="text_1">暂无消费记录哦</text>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				dataList: [],
				page: 1,
				date: '',
				total: null
			};
		},
		onLoad(){
			let D = new Date();
			let Y  = D.getFullYear();
			let M = D.getMonth() + 1;
			M < 10 ? M = '0' + M : M = M;
			this.date = `${Y}-${M}`;
			this.init();
		},
		methods:{
			init(){
				this.$http.post('api/recharge/consumeList',{
					page: this.page,
					date: this.date
				}).then(res =>{
					let { list, total } = res;
					this.dataList.push(...list);
					this.total = total;
				})
			}
		}
	};
</script>

<style scoped lang="scss">
	.page {
		background-color: #f8f8f8;
		width: 100%;
		height: 100%;
		overflow-y: auto;
		.none-img {
			position: fixed;
			width: 100%;
			height: 100%;
			top: 0;
			left: 0;
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
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
			// padding-bottom: 592rpx;
			flex: 1 1 auto;
			overflow-y: auto;
			
			.list-item {
				padding: 32rpx 30rpx 32rpx 32rpx;
				background-color: rgb(255, 255, 255);

				.text_7 {
					align-self: center;
					color: rgb(243, 151, 53);
					font-size: 48rpx;
					line-height: 58rpx;
					white-space: nowrap;
				}

				.text_1 {
					color: rgb(0, 0, 0);
					font-size: 32rpx;
					font-weight: 500;
					line-height: 44rpx;
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
					margin-top: 8rpx;
					color: rgb(153, 153, 153);
					font-size: 28rpx;
					line-height: 40rpx;
					white-space: nowrap;
				}
			}
		}
	}
</style>
