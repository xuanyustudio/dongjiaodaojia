<template>
	<view class="flex-col page">
		<view class="flex-col">
			<view class="flex-col group_1">
				<view class="flex-col">
					<view class="flex-row">
						<view class="flex-col group_4">
							<image :src="avatar" class="image_1" />
						</view>
						<view class="right-group flex-col items-end view">
							<view class="flex-col section_1">
								<view class="flex-col text-wrapper">
									<text class="text">{{ is_vip ? '已开通' : '未开通' }}</text>
								</view>
							</view>
							<text class="text_1">{{ username }}</text>
							<text class="text_2" v-if="!is_vip">开通会员 享专属权益</text>
							<text class="text_2" v-else>会员到期：{{overdue_time}}</text>
						</view>
					</view>
					<image
						src="../../../static/my/vip-2.png"
						class="image_2" />
				</view>
				<view class="center-group flex-col view_1">
					<text class="text_3">VIP会员套餐</text>
					<view class="center-group  view_2">
						<u-scroll-list :indicator="false">
							<view class="section_2 flex-col" v-for="(item,index) in dataList"
								:class="{'active':selectValue==index}" @click="changeSelect(index)">
								<view class="flex-col group_5" style="text-align: center;">
									<text class="text_4">{{ item.name }}</text>
									<view class="center-group_1" >
										<text class="text_6">￥</text>
										<text class="text_8">{{ item.price }}</text>
									</view>
									<view class="right-group flex-col items-center">
										<view class="group_6">
											<text class="text_10">￥</text>
											<text class="text_12">{{ item.original_price }}</text>
										</view>
										<view class="section_3"> </view>
									</view>
								</view>
								<view class="bottom-text-wrapper flex-col items-center">
									<text>立省{{ Number(item.original_price - item.price).toFixed(2) }}元</text>
								</view>
							</view>


						</u-scroll-list>
					</view>
				</view>

				<view class="flex-col group_11">
					<text class="text_28">VIP尊享特权</text>
					<view class="center-section flex-row view_18" v-for="(item,index) in rights" :key="index">
						<image
							:src="item.icon"
							class="image_3" />
						<view class="right-group_1 flex-col">
							<text class="text_29">{{ item.name }}</text>
							<text class="text_31">{{ item.profile }}</text>
						</view>
					</view>
					<!-- <view class="center-section flex-row view_18">
						<image
							src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844628055788480.png"
							class="image_3" />
						<view class="right-group_1 flex-col">
							<text class="text_29">平台折扣</text>
							<text class="text_31">平台下单享受9.5折</text>
						</view>
					</view> -->
				</view>
				
				<view class="ios"></view>
				
			</view>
			<view class="justify-between section_8">
				<view class="group_12">
					<text class="text_35">￥</text>
					<text class="text_36">{{ money }}</text>
				</view>
				<view class="flex-col items-center text-wrapper_2" @click="submit">
					<text>立即开通</text>
				</view>
			</view>
		</view>
		<view class="section_9"> </view>
		<zPay ref="pay" :money="money" @pay="topay"></zPay>
	</view>
</template>

<script>
	import zPay from '@/components/z-pay/index.vue';
	import { pay } from '@/config/utils'
	export default {
		data() {
			return {
				selectValue: 1,
				list: [],
				avatar: '',
				is_vip:'',
				balance: 0,
				username: '',
				dataList: [],
				money: 0,
				payShow: false,
				payList: [{
						name: '微信',
						value: 2,
						icon: "https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844672536278668.png",
						disabled: false
					},
					{
						name: '支付宝',
						icon: require('../../../static/icon/ali.png'),
				
						value: 3,
						disabled: false
					}
				],
				pay_way: 2,
				rights: []
			};
		},
		components:{
			zPay
		},
		onLoad() {
			this.init();
		},
		methods: {
			changeSelect(e) {
				this.selectValue = e;
				this.money = this.dataList[this.selectValue].price;
			},
			init() {
				// 首页
				this.$http.post('api/user/userInfo').then(res => {
					for (let i in res) {
						this[i] = res[i];
					}
				})
				this.$http.post('api/member/memberList').then(res =>{
					let { list ,rights } = res;
					console.log(res)
					this.dataList = list;
					this.rights = rights;
					this.money = this.dataList[this.selectValue].price;
				})
			},
			async submit(){
				this.$refs.pay.show();
			},
			close() {
				this.payShow = false
			},
			open(){
				console.log(11);
			},
			groupChange(e){},
			async topay(e){
				// 下单
				let { pay_way,pay_type } = e;
				let order_sn = await this.$http.post('api/member/createOrder',{
					id: this.dataList[this.selectValue].id,
				});
				let data = await this.$http.post('api/pay/pay',{
					order_sn,
					pay_way,
					form: 'member'
				})
				// .then(res =>{
				// 	// console.log(res,'175175175');
				// 	// return
				// 	plus.runtime.openURL(res, function(res1) {
				// 	                    console.log(res1,"888888888888888888");  
				// 	});
				// })
				pay(data, pay_type).then(res =>{
					this.init();
				});
			},
		}
	};
</script>

<style scoped lang="scss">
	.page {
		background-color: #f8f8f8;
		background-image: linear-gradient(153.2deg,
				rgb(81, 81, 81) 0%,
				rgb(81, 81, 81) -15.28%,
				rgb(36, 36, 36) 114.1%,
				rgb(36, 36, 36) 100%);
		width: 100%;
		min-height: 100vh;
		.ios{
			padding-bottom: 300rpx;
		}
		.popup {
			height: 600rpx;
			background-color: white;
			.section_8 {
				padding-bottom: 92rpx;
				background-color: rgb(248, 248, 248);
				border-radius: 40rpx 40rpx 0px 0px;
		
				.button {
					margin: 80rpx 32rpx 0;
					padding: 22rpx 0;
					color: rgb(255, 255, 255);
					font-size: 32rpx;
					font-weight: 500;
					line-height: 44rpx;
					white-space: nowrap;
					background-color: rgb(24, 181, 103);
					border-radius: 44rpx;
				}
		
				.section_9 {
					padding: 20rpx 32rpx 22rpx 34rpx;
					background-color: rgb(255, 255, 255);
					border-radius: 40rpx 40rpx 0px 0px;
		
					.image_1 {
						margin: 8rpx 0 6rpx;
						width: 44rpx;
						height: 44rpx;
					}
		
					.text_23 {
						margin-top: 18rpx;
						color: rgb(80, 80, 80);
						font-size: 28rpx;
						line-height: 40rpx;
						white-space: nowrap;
					}
		
					.group_10 {
						margin-left: 16rpx;
						white-space: nowrap;
						height: 58rpx;
		
						.text_24 {
							color: rgb(243, 151, 53);
							font-size: 28rpx;
							line-height: 34rpx;
						}
		
						.text_25 {
							color: rgb(243, 151, 53);
							font-size: 48rpx;
							line-height: 58rpx;
						}
					}
				}
		
				.group_11 {
					padding-bottom: 190rpx;
					position: relative;
		
					.section_10 {
						background-image: linear-gradient(180deg,
								rgb(255, 255, 255) 0%,
								rgb(255, 255, 255) 0%,
								rgb(248, 248, 248) 100%,
								rgb(248, 248, 248) 100%);
						height: 48rpx;
					}
		
					.list {
						position: absolute;
						left: 32rpx;
						right: 32rpx;
						bottom: 0;
		
						.list-item {
							padding: 26rpx 24rpx;
							color: rgb(0, 0, 0);
							font-size: 28rpx;
							font-weight: 500;
							line-height: 40rpx;
							white-space: nowrap;
							background-color: rgb(255, 255, 255);
							border-radius: 24rpx;
		
							&:last-of-type {
								margin-top: 24rpx;
							}
		
							.image_4 {
								width: 44rpx;
								height: 44rpx;
							}
		
							.image_2 {
								width: 40rpx;
								height: 40rpx;
							}
		
							.text_26 {
								margin-left: 14rpx;
							}
						}
					}
				}
			}
		}
		
		.section_9 {
			background-color: rgb(26, 26, 26);
			height: 68rpx;
		}

		.group_1 {
			padding-left: 32rpx;
			padding-bottom: 40rpx;

			.view_1 {
				margin-top: 54rpx;
			}

			.group_11 {
				margin-top: 56rpx;

				.text_28 {
					color: rgb(252, 226, 197);
					font-size: 32rpx;
					font-weight: 500;
					line-height: 44rpx;
					white-space: nowrap;
				}

				.center-section {
					padding: 24rpx 24rpx;
					background-color: rgb(95, 95, 95);
					border-radius: 24rpx;

					.image_3 {
						width: 96rpx;
						height: 96rpx;
					}

					.right-group_1 {
						margin: 8rpx 0 10rpx 24rpx;
						flex: 1 1 auto;

						.text_29 {
							color: rgb(252, 226, 197);
							font-size: 28rpx;
							font-weight: 500;
							line-height: 40rpx;
							white-space: nowrap;
						}

						.text_31 {
							margin-top: 4rpx;
							color: rgba(252, 226, 197, 0.45);
							font-size: 24rpx;
							line-height: 34rpx;
							white-space: nowrap;
						}
					}
				}

				.view_16 {
					margin-right: 32rpx;
					margin-top: 16rpx;
				}

				.view_18 {
					margin-right: 32rpx;
					margin-top: 24rpx;
				}
			}

			.image_2 {
				margin-right: 32rpx;
				margin-top: 40rpx;
				width: 91.5vw;
				height: 38.5vw;
			}

			.center-group {
				overflow-x: hidden;

				.text_3 {
					color: rgb(252, 226, 197);
					font-size: 32rpx;
					font-weight: 500;
					line-height: 44rpx;
					white-space: nowrap;
				}

				.view_2 {
					margin-top: 32rpx;
				}

				.section_2 {
					padding: 0 32rpx 22rpx;
					margin-right: 30rpx;
					background-color: rgb(95, 95, 95);
					border-radius: 24rpx;
					width: 200rpx;
					height: 256rpx;

					&.active {
						background-image: linear-gradient(137.9deg,
								rgb(248, 216, 179) 0%,
								rgb(248, 216, 179) -3.03%,
								rgb(223, 181, 129) 104.83%,
								rgb(223, 181, 129) 100%);
						border-radius: 24rpx;
						width: 200rpx;
						height: 256rpx;
						border: solid 4rpx rgb(185, 144, 113);

						.group_5 {
							
						}

						.bottom-text-wrapper {
							margin-right: 8rpx;
							margin-top: 20rpx;
							padding-bottom: 2rpx;
							color: rgb(224, 182, 131);
							font-size: 18rpx;
							line-height: 34rpx;
							height: 45rpx;
							white-space: nowrap;
							padding: 5rpx 0;
							background-image: linear-gradient(102.9deg,
									rgb(52, 52, 52) 0%,
									rgb(52, 52, 52) -3.03%,
									rgb(52, 52, 52) 104.83%,
									rgb(52, 52, 52) 100%);
							border-radius: 18rpx;
							width: 136rpx;
						}

						.group_10 {
							padding: 30rpx 6rpx 0;

							.text_21 {
								align-self: center;
							}

							.view_11 {
								align-self: center;
							}
						}

						.text_4 {
							color: rgb(52, 52, 52);
							font-size: 28rpx;
							line-height: 40rpx;
							white-space: nowrap;
						}

						.center-group_1 {
							margin-top: 8rpx;
							white-space: nowrap;

							.text_6 {
								color: rgb(52, 52, 52);
								font-size: 28rpx;
								line-height: 34rpx;
							}

							.text_8 {
								font-family: Price;
								color: rgb(52, 52, 52);
								font-size: 52rpx;
								line-height: 62rpx;
							}
						}




					}

					.group_5 {
						padding-top: 22rpx;
					}

					.bottom-text-wrapper {

						margin-right: 8rpx;
						margin-top: 20rpx;
						padding-bottom: 2rpx;
						color: rgb(57, 57, 57);
						font-size: 20rpx;
						line-height: 34rpx;
						height: 45rpx;
						white-space: nowrap;
						padding: 5rpx 0;
						background-image: linear-gradient(102.9deg,
								rgb(248, 216, 179) 0%,
								rgb(248, 216, 179) -3.03%,
								rgb(223, 181, 129) 104.83%,
								rgb(223, 181, 129) 100%);
						border-radius: 18rpx;
						width: 136rpx;
					}

					.group_10 {
						padding: 30rpx 6rpx 0;

						.text_21 {
							align-self: center;
						}

						.view_11 {
							align-self: center;
						}
					}

					.text_4 {
						color: rgb(252, 226, 197);
						font-size: 28rpx;
						line-height: 40rpx;
						white-space: nowrap;
					}

					.center-group_1 {
						margin-top: 8rpx;
						white-space: nowrap;

						.text_6 {
							color: rgb(252, 226, 197);
							font-size: 28rpx;
							line-height: 34rpx;
						}

						.text_8 {
							color: rgb(252, 226, 197);
							font-size: 52rpx;
							line-height: 62rpx;
						}
					}
				}

				.section_4 {
					margin-left: 20rpx;
					padding: 0 20rpx 18rpx 28rpx;
					background-image: linear-gradient(137.9deg,
							rgb(248, 216, 179) 0%,
							rgb(248, 216, 179) -3.03%,
							rgb(223, 181, 129) 104.83%,
							rgb(223, 181, 129) 100%);
					border-radius: 24rpx;
					width: 200rpx;
					height: 256rpx;
					border: solid 4rpx rgb(185, 144, 113);

					.group_7 {
						padding: 26rpx 12rpx 0;

						.text_15 {
							align-self: center;
							color: rgb(52, 52, 52);
							font-size: 28rpx;
							line-height: 40rpx;
							white-space: nowrap;
						}

						.group_8 {
							margin-top: 8rpx;
							align-self: center;
							white-space: nowrap;

							.text_16 {
								color: rgb(52, 52, 52);
								font-size: 28rpx;
								line-height: 34rpx;
							}

							.text_17 {
								color: rgb(52, 52, 52);
								font-size: 52rpx;
								line-height: 62rpx;
							}
						}
					}

					.text-wrapper_1 {
						margin-right: 8rpx;
						margin-top: 20rpx;
						padding-bottom: 2rpx;
						color: rgb(224, 182, 131);
						font-size: 24rpx;
						line-height: 34rpx;
						white-space: nowrap;
						background-color: rgb(52, 52, 52);
						border-radius: 18rpx;
						width: 136rpx;
					}
				}

				.view_10 {
					margin-left: 24rpx;
				}

				.section_6 {
					margin-left: 24rpx;
					margin-right: -20rpx;
					padding: 92rpx 0 22rpx 32rpx;
					color: rgb(252, 226, 197);
					font-size: 28rpx;
					line-height: 34rpx;
					white-space: nowrap;
					background-color: rgb(95, 95, 95);
					border-radius: 24rpx 0 0 24rpx;
					width: 66rpx;
					height: 256rpx;

					.text_27 {
						align-self: center;
					}

					.section_7 {
						margin-top: 72rpx;
						background-image: linear-gradient(102.9deg,
								rgb(248, 216, 179) 0%,
								rgb(248, 216, 179) -3.03%,
								rgb(223, 181, 129) 104.83%,
								rgb(223, 181, 129) 100%);
						border-radius: 18rpx 0 0 18rpx;
						height: 36rpx;
					}
				}
			}

			.group_4 {
				margin-top: 116rpx;
				margin-right: 20rpx;

				.image {
					margin-left: 16rpx;
					width: 16rpx;
					height: 28rpx;
				}

				.image_1 {
					// margin-top: 70rpx;
					border-radius: 50%;
					width: 112rpx;
					height: 112rpx;
				}
			}

			.view {
				margin-top: 0;
				flex: 1 1 auto;
			}

			.right-group {
				margin-top: 0rpx;
				position: relative;

				.section_1 {
					padding: 120rpx 0 76rpx;
					color: rgb(248, 216, 179);
					font-size: 24rpx;
					line-height: 34rpx;
					white-space: nowrap;
					background-image: url('https://project-user-resource-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844808662870883.png');
					background-position: 0px 0px;
					background-size: 100% 100%;
					background-repeat: no-repeat;
					width: 459rpx;

					.text-wrapper {
						margin-left: 77rpx;
						width: 100rpx;
						text-align: center;
						padding: 2rpx 0 4rpx;
						border-radius: 8rpx;
						background-image: url('https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844631212306194.png');
						background-size: 100% 100%;
						background-repeat: no-repeat;

						.text {
							margin: 0 8rpx;
						}
					}
				}

				.text_1 {
					color: rgb(255, 255, 255);
					font-size: 32rpx;
					font-weight: 500;
					line-height: 44rpx;
					white-space: nowrap;
					position: absolute;
					left: 0;
					bottom: 74rpx;
				}

				.text_2 {
					color: rgb(252, 226, 197);
					font-size: 28rpx;
					line-height: 40rpx;
					white-space: nowrap;
					position: absolute;
					left: 0;
					bottom: 26rpx;
				}

				.group_6 {
					font-weight: 500;
					line-height: 34rpx;
					white-space: nowrap;

					.text_10 {
						color: rgba(52, 52, 52, 0.31);
						font-size: 24rpx;
						font-weight: 500;
						line-height: 34rpx;
					}

					.text_12 {
						color: rgba(52, 52, 52, 0.31);
						font-size: 24rpx;
						line-height: 28rpx;
					}
				}

				.section_3 {
					background-color: rgb(151, 151, 151);
					width: 69rpx;
					height: 2rpx;
					position: absolute;
					left: 50%;
					top: 60%;
					transform: translate(-50%, -50%);
				}

				.group_9 {
					font-weight: 500;
					line-height: 34rpx;
					white-space: nowrap;

					.text_18 {
						color: rgba(52, 52, 52, 0.31);
						font-size: 24rpx;
						font-weight: 500;
						line-height: 34rpx;
					}

					.text_19 {
						color: rgba(52, 52, 52, 0.31);
						font-size: 24rpx;
						line-height: 28rpx;
					}
				}

				.section_5 {
					background-color: rgb(174, 148, 116);
					width: 69rpx;
					height: 2rpx;
					position: absolute;
					left: 50%;
					top: 50%;
					transform: translate(-50%, -50%);
				}
			}
		}

		.section_8 {
			position: fixed;
			width: 100vw;
			bottom: 0;
			padding: 46rpx 32rpx;
			background-image: linear-gradient(180deg,
					rgb(84, 84, 84) 0%,
					rgb(84, 84, 84) 0%,
					rgb(25, 25, 25) 100%,
					rgb(25, 25, 25) 100%);

			.group_12 {
				align-self: center;
				white-space: nowrap;
				height: 62rpx;

				.text_35 {
					color: rgb(252, 226, 197);
					font-size: 28rpx;
					line-height: 34rpx;
				}

				.text_36 {
					font-family: Price;
					color: rgb(252, 226, 197);
					font-size: 52rpx;
					line-height: 62rpx;
				}
			}

			.text-wrapper_2 {
				padding: 22rpx 0;
				color: rgb(41, 41, 41);
				font-size: 32rpx;
				font-weight: 500;
				line-height: 44rpx;
				white-space: nowrap;
				background-image: linear-gradient(107.6deg,
						rgb(248, 216, 179) 0%,
						rgb(248, 216, 179) -3.03%,
						rgb(223, 181, 129) 104.83%,
						rgb(223, 181, 129) 100%);
				border-radius: 42rpx;
				width: 240rpx;
				height: 88rpx;
			}
		}
	}
</style>
