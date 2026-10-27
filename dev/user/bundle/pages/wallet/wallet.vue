<template>
	<view class="flex-col page">
		<view class="flex-col group">
			<view class="flex-col section_1">
				<view class="flex-col section_2">
					<view class="flex-col">
						<text class="text_1">账户余额(元)</text>
						<text class="text_2">{{ balance.toFixed(2) }}</text>
					</view>
					<view class="justify-between group_2" >
						<view class="flex-col" @click="toPath('/bundle/pages/recharge_record/recharge_record')">
							<text>充值记录</text>
						</view>
						<view class="section_3"> </view>
						<view class="flex-col" @click="toPath('/bundle/pages/consume_record/consume_record')">
							<text>消费明细</text>
						</view>
						<view class="section_3"> </view>
						<view class="flex-col" @click="toPath('/bundle/pages/refund_record/refund_record')">
							<text>退款记录</text>
						</view>
						<view class="section_3"> </view>
						<view class="flex-col" @click="toPath('/bundle/pages/refund_record/yongjin')">
							<text>佣金记录</text>
						</view>
					</view>
				</view>
			</view>
			<view class="flex-col section_4">
				<view class="flex-col">
					<view class="flex-row group_4">
						<view class="section_5"> </view>
						<text class="text_6">余额充值</text>
					</view>
					<view class="grid">
						<view class="flex-col items-center grid-item" v-for="(item,index) in moneyList" :key="index"
							:class="{'active':moneySelect==index}" @click="choice(index)">
							<view class="group_5">
								<text class="text_7">￥</text>
								<text class="text_8">{{ item.price }}</text>
							</view>
							
							<view class="pos">
								送{{ item.give }}
							</view>
						</view>

					</view>
					<view class="flex-col group_7">
						<view class="flex-row section_11">
							<text class="text_21">￥</text>

							<text class="text_22">
								<input type="number" placeholder="输入自定义金额" v-model="money" /></text>
						</view>
						<text class="text_23">充值说明<br />1、本次充值仅限于在平台上消费，无法跨地区跨站点使用。<br />2、若遇到充值未到账，请联系客服。</text>
					</view>
				</view>
				<view class="flex-col items-center button" @click="comfirm">
					<text>充值</text>
				</view>
			</view>
		</view>
		<!--支付弹出-->
		<u-popup :show="payShow" @close="close" @open="open" round="20" :closeable="true">
			<view class="popup">
				<view class="flex-col section_8">
					<view class="flex-col">
						<view class="justify-between section_9">
							<view class="flex-row">
								<text class="text_23">支付</text>
								<view class="group_10">
									<text class="text_24">￥</text>
									<text class="text_25">{{ price }}</text>
								</view>
							</view>

						</view>
						<view class="flex-col group_11">
							<view class="section_10"> </view>
							<view class="flex-col list">
								<u-radio-group placement="cloums" v-model="pay_way"  @change="groupChange">
									<view class="list-item justify-between" :key="i" v-for="(item, i) in payList">
										<view class="flex-row">
											<image :src="item.icon" class="image_2" />
											<text class="text_26">{{item.name}}</text>
										</view>
										<u-radio shape="circle" :value="item.value" :name="item.value">{{item.value}}
										</u-radio>
									</view>
								</u-radio-group>
							</view>
						</view>
					</view>
					<view class="flex-col items-center button" @click="topay">
						<text>立即支付</text>
					</view>
				</view>
			</view>
		</u-popup>
	</view>
</template>

<script>
	import { pay } from '@/config/utils'
	export default {
		data() {
			return {
				balance: 0,
				moneySelect: 0,
				payShow: false,
				moneyList: [],
				payList: [
					{
						name: '微信',
						value: 2,
						icon: "https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844672536278668.png",
						disabled: false
					}
					// #ifndef H5
					,{
						name: '支付宝',
						icon: require('../../../static/icon/ali.png'),

						value: 3,
						disabled: false
					},
					// #endif
				],
				pay_way: 2,
				price: '',
				money:'',
				id: null,          
			};
		},
		
		onLoad() {
			this.init();
		},
		// 下拉刷新
		 onPullDownRefresh() {
		      this.init();
		        setTimeout(function () {
		            uni.stopPullDownRefresh();  
		        }, 500);
		},
		watch:{
			money(val){
				if( val ){
					this.moneySelect = null;
				}
			}
		},
		methods: {
			init() {
				// 获取账户余额
				this.$http.post('api/recharge/index').then(res => {
					this.balance = Number(res.balance)
				});
				// 充值列表
				this.$http.post('api/recharge/rechargeList').then(res =>{
					console.log(res,"请求的接口充值列表")
					let { list } = res;
					this.moneyList = list;
				})
			},
			choice(e) {
				this.moneySelect = e;
				let index = e + 1;
				let price = '';
				price = index ===  4 ? 600 : index === 5 ? 800 : index === 6 ? 1000 : index * 100;
			},
			onKeyInput(e) {
				console.log(e.target.value)
			},
			close() {
				this.payShow = false
			},
			open(){
				console.log(11);
			},
			comfirm() {
				if( this.moneySelect === null ){
					this.price = this.money;
					this.id === null;
				}else{
					this.price = this.moneyList[this.moneySelect].price;
					
				}
				this.payShow = true;
			},
			groupChange(e) {
				console.log(e)
			},
			toPath(url){
				uni.navigateTo({
					url,
				})
			},
			topay(){
				let obj = {}
				if( this.moneySelect !== null ){
					this.id = this.moneyList[this.moneySelect].id;
				}
				if( this.id ){
					obj.id = this.id;
				}else{
					obj.price = this.price;
				}
				
				var brow = 0;
				
				// #ifdef H5
				
				brow = 1;
				
				// #endif
				
				this.$http.post('api/recharge/createOrder',obj).then(res =>{
					this.$http.post('api/pay/pay',{
						order_sn: res,
						pay_way: this.pay_way,
						form: 'recharge',
						brow:brow
					}).then(res1 =>{
						this.payShow = false
						console.log(res1,"res1 ")
						// 单支付宝支付 pay_way
						pay(res1, this.pay_way === 2 ? 'wxpay' : 'alipay').then(res2 => {
							// uni.showToast({
							// 	title:'充值成功'
							// })
							
							uni.startPullDownRefresh({
								
							})
						});
						// plus.runtime.openURL(res.codeUrl, function(res) {  
						//                     console.log(res,"888888888888888888");  
						// });
						

					})
				})
				
			}

		}
	};
</script>

<style scoped lang="scss">
	page {
		background-color: #FFFFFF;
		width: 100%;
		height: 100vh;
		overflow-y: auto;

		.popup {
			height: 600rpx;

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

		.group {
			flex: 1 1 auto;
			overflow-y: auto;

			.section_1 {
				padding: 30rpx 0 40rpx;
				background-color: rgba(24, 181, 103, 0.2);

				.section_2 {
					margin-left: 32rpx;
					margin-right: 30rpx;
					padding: 40rpx 40rpx 28rpx;
					background-image: url('https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844669824572110.png');
					background-position: 0px 0px;
					background-size: 100% 100%;
					background-repeat: no-repeat;

					.group_2 {
						margin-top: 92rpx;
						color: rgb(255, 255, 255);
						font-size: 28rpx;
						line-height: 40rpx;
						white-space: nowrap;

						.section_3 {
							align-self: center;
							background-color: rgb(255, 255, 255);
							width: 2rpx;
							height: 18rpx;
						}
					}

					.text_1 {
						color: rgb(255, 255, 255);
						font-size: 32rpx;
						line-height: 44rpx;
						white-space: nowrap;
					}

					.text_2 {
						font-family: Price;
						margin-top: 8rpx;
						color: rgb(255, 255, 255);
						font-size: 60rpx;
						line-height: 72rpx;
						white-space: nowrap;
					}
				}
			}

			.section_4 {
				margin-top: -40rpx;
				padding: 40rpx 31rpx 100rpx 32rpx;
				background-color: rgb(255, 255, 255);
				border-radius: 40rpx 40rpx 0px 0px;
				position: relative;

				.button {
					margin-top: 268rpx;
					padding: 26rpx 0;
					color: rgb(255, 255, 255);
					font-size: 32rpx;
					font-weight: 500;
					line-height: 44rpx;
					white-space: nowrap;
					background-color: rgb(24, 181, 103);
					border-radius: 48rpx;
				}

				.group_4 {
					color: rgb(0, 0, 0);
					font-size: 32rpx;
					font-weight: 500;
					line-height: 44rpx;
					white-space: nowrap;

					.section_5 {
						margin: 6rpx 0;
						background-color: rgb(24, 181, 103);
						border-radius: 4rpx;
						width: 8rpx;
						height: 32rpx;
					}

					.text_6 {
						margin-left: 16rpx;
					}
				}

				.grid {
					margin-top: 32rpx;
					height: 216rpx;
					display: grid;
					grid-template-columns: repeat(3, 1fr);
					grid-row-gap: 26rpx;
					grid-column-gap: 26rpx;

					.grid-item {
						padding: 18rpx 0 20rpx;
						// 
						background-color: rgba(24, 181, 103, 0.2);
						border-radius: 12rpx;
						position: relative;
						.pos{
							position: absolute;
							right: 20rpx;
							bottom: 80rpx;
							color: white;
							font-size: 24rpx;
						}
						&.active {
							background-color: rgb(24, 181, 103);
						}

						.group_5 {
							white-space: nowrap;

							.text_7 {
								color: rgb(255, 255, 255);
								font-size: 28rpx;
								line-height: 34rpx;
							}

							.text_8 {
								font-family: Price;
								color: rgb(255, 255, 255);
								font-size: 48rpx;
								line-height: 58rpx;
							}
						}
					}

					.grid-item_1 {
						padding: 18rpx 0 20rpx;
						background-color: rgba(24, 181, 103, 0.08);
						border-radius: 12rpx;

						.group_6 {
							white-space: nowrap;

							.text_9 {
								color: rgba(24, 181, 103, 0.6);
								font-size: 28rpx;
								line-height: 34rpx;
							}

							.text_11 {
								color: rgba(24, 181, 103, 0.6);
								font-size: 48rpx;
								line-height: 58rpx;
							}
						}
					}
				}

				.group_7 {
					margin-top: 24rpx;

					.section_11 {
						padding: 32rpx 24rpx 30rpx;
						background-color: rgb(248, 248, 248);
						border-radius: 12rpx;

						.text_21 {
							color: rgb(24, 181, 103);
							font-size: 48rpx;
							line-height: 58rpx;
							white-space: nowrap;
						}

						.text_22 {
							margin: 6rpx 0 8rpx 16rpx;
							color: rgb(153, 153, 153);
							font-size: 32rpx;
							line-height: 44rpx;
							white-space: nowrap;
						}
					}

					.text_23 {
						margin-top: 40rpx;
						color: rgb(102, 102, 102);
						font-size: 24rpx;
						line-height: 40rpx;
						text-align: left;
						width: 634rpx;
					}
				}
			}
		}
	}
</style>
