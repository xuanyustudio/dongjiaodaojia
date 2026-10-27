<template>
	<u-popup :show="showPopup" :round="20" mode="bottom" :closeable='true' @close="close" @open="open">
		<view class="popup">
			<view class="title">
				<text class="h1">支付</text>
				<view class="price">
					<text>￥</text>{{ money }}
				</view>
			</view>

			<view class="type">
				<view v-for="(item,index) in payList" :key="index" @click="payIndex = index">
					<view class="type-1" v-if="item.show">
						<image class="icon" :src="item.icon" mode=""></image>
						<text class="name">{{ item.name }}支付{{ !index ? `(${balance}元)` : '' }}</text>
						<image class="select"
							:src="payIndex === index ?  require('../../static/icon/s.png') : require('../../static/icon/no-s.png')"
							mode=""></image>
					</view>
					
				</view>
			</view>
			<view class="btn" @click="submit">
				立即支付
			</view>
		</view>
	</u-popup>
</template>

<script>
	export default {
		props: {
			money: {
				default: 0
			},
			balance:{
				default: -1
			}
		},
		data() {
			return {
				showPopup: false,
				payList: [{
						name: '余额',
						value: 1,
						icon: "https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844612123080341.png",
						disabled: false,
						pay_type: 'balance',
						show:false
					}, 
					{
						name: '微信',
						value: 2,
						icon: "https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844672536278668.png",
						disabled: false,
						pay_type: 'wxpay',
						show:true
					}
					// #ifndef H5
					,{
						name: '支付宝',
						icon: require('@/static/icon/ali.png'),
						value: 3,
						disabled: false,
						pay_type: 'alipay',
						show:true
					},
					// #endif
				],
				payIndex: 0,
				
			}
		},
		created(){
			// 获取余额
			if( this.balance >= this.money ){
				this.payList[0].show = true;
			}
		},
		methods: {
			close() {
				this.hide();
			},
			open() {},
			submit() {
				this.$emit('pay', {
					pay_way: this.payList[this.payIndex].value,
					pay_type: this.payList[this.payIndex].pay_type,
				});
				this.showPopup = false;
			},
			hide() {
				this.showPopup = false;
			},
			show() {
				this.showPopup = true;
			}
		}
	}
</script>

<style lang="scss" scoped>
	.popup {
		padding: 24rpx 32rpx 94rpx;

		.title {
			display: flex;
			align-items: flex-end;
			margin-bottom: 44rpx;

			.h1 {
				color: rgba(80, 80, 80, 1);
				font-size: 28rpx;
				margin-right: 18rpx;
				transform: translateY(-6rpx);
			}

			.price {
				display: flex;
				color: rgba(243, 151, 53, 1);
				font-size: 48rpx;
				font-weight: bold;
				align-items: flex-end;

				text {
					font-size: 28rpx;
					font-weight: normal;
					transform: translateY(-6rpx);
				}
			}
		}

		.type {
			&-1 {
				background-color: rgba(255, 255, 255, 1);
				border-radius: 24rpx;
				display: flex;
				align-items: center;
				height: 96rpx;
				margin-bottom: 24rpx;
				padding: 0 24rpx;
				.icon {
					width: 40rpx;
					height: 40rpx;
					margin-right: 12rpx;
				}

				.name {
					color: rgba(0, 0, 0, 1);
					font-size: 28rpx;
					flex: 1;
				}

				.select {
					width: 44rpx;
					height: 44rpx;
				}
			}
		}

		.btn {
			height: 88rpx;
			background-color: rgba(24, 181, 103, 1);
			border-radius: 88rpx;
			display: flex;
			align-items: center;
			justify-content: center;
			color: rgba(255, 255, 255, 1);
			font-size: 32rpx;
			margin-top: 60tpx;
		}
	}
</style>
