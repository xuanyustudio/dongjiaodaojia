<template>
	<view class="flex-col page">
		<view class="flex-col group">
			<!-- <view class="flex-row section_1">
				<view class="flex-col text-wrapper">
					<view style="padding-left: 16rpx;">
						<u--input placeholder="输入兑换码" border="none" v-model="code"></u--input>
					</view>

				</view>
				<view class="flex-col items-center text-wrapper_1" @click="submit">
					<text>兑换</text>
				</view>
			</view> -->



			<view class="flex-col">
				<view class="flex-col section_2" style="flex-shrink:0;">
					<u-tabs :list="menu" lineWidth="60" lineColor="rgb(24, 181, 103)" @click="click"></u-tabs>
				</view>
				
				<view class="no" v-if="total === 0">
					<zNo></zNo>
				</view>
				<!--可用优惠券-->
				<view class="flex-col group_2" v-if="menuValue==0">
					<view class="section_4"> </view>
					<view class="flex-col list">
						<view class="list-item flex-row" :key="i" v-for="(item, i) in dataList" >
							<view class="left-group flex-col items-center" >
								<view class="top-group" v-if="!item.type">
									<text class="text_5">￥</text>
									<text class="text_7">{{ item.money }}</text>
								</view>
								<view class="top-group" v-if="item.type">
									<text class="text_7">{{ item.discount }}</text>
									<text class="text_5">折</text>
								</view>
								<text class="text_9" v-if="item.condition==0">无门槛</text>
								<text class="text_9" v-else>满{{ item.condition }}使用</text>
							</view> 
							<view class="right-section flex-col">
								<text class="text_11">店铺优惠券</text>
								<text class="text_13" v-if="item.use == 0">有效期至{{ item.end_time }}</text>
								<text class="text_133"  v-if="item.use==1">已使用</text>
								<text class="text_133"  v-if="item.chaoshi==1">已超时</text>
							</view>
						</view>
					</view>
				</view>
				<!-- 待使用优惠券 -->
				<view class="flex-col group_2" v-if="menuValue==1">
					<view class="section_4"> </view>
					<view class="flex-col list">
						<view class="list-item flex-row" :key="i" v-for="(item, i) in dataList">
							<view class="left-group flex-col items-center">
								<view class="top-group" v-if="!item.type">
									<text class="text_5">￥</text>
									<text class="text_7">{{ item.money }}</text>
								</view>
								<view class="top-group" v-if="item.type">
									<text class="text_7">{{ item.discount }}</text>
									<text class="text_5">折</text>
								</view>
								<text class="text_9" v-if="item.condition==0">无门槛</text>
								<text class="text_9" v-else>满{{ item.condition }}使用</text>
								
							</view> 
							<view class="right-section flex-col">
								<text class="text_11">店铺优惠券</text>
								<text class="text_13" v-if="item.use == 0">有效期至{{ item.end_time }}</text>
								<text class="text_133"  v-if="item.use==1">已使用</text>
								<text class="text_133"  v-if="item.chaoshi==1">已超时</text>
							</view>
						</view>
						
					</view>
				</view>
				<!--不可用优惠券-->
				<view class="flex-col group_3" v-if="menuValue==2">
					<view class="section_4"> </view>
					<view class="flex-col list">
						<view class="list-item flex-col" :key="i" v-for="(item, i) in dataList">
							<view class="top-section flex-row">
								<view class="left-group flex-col items-center">
									<view class="top-group" v-if="!item.type">
										<text class="text_5">￥</text>
										<text class="text_7">{{ item.money }}</text>
									</view>
									<view class="top-group" v-if="item.type">
										<text class="text_7">{{ item.discount }}</text>
										<text class="text_5">折</text>
									</view>
									<text class="text_9" v-if="item.condition==0">无门槛</text>
									<text class="text_9" v-else>满{{ item.condition }}使用</text>
								</view>
								<view class="right-section flex-col">
									<text class="text_11">店铺优惠券</text>
									<text class="text_13">有效期至{{ item.end_time }}</text>
								</view>
							</view>
							<view class="bottom-group flex-row">
								<image
									src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844648387456835.png"
									class="image_1" />
								<text class="text_15">优惠券已过期</text>
							</view>
						</view>
					</view>
				</view>
			</view>
		</view>
		
		
	</view>
</template>

<script>
	// 后台没有数据就引入xer.vue  ，后台接口给出数据就引入index.vue
	// import zNo from '@/components/z-no/index.vue'
	import zNo from '@/components/z-no/xer.vue'
	export default {
		data() {
			return {
				dataList: [],
				menu: [{
					name: '全部',
				}, {
					name: '待使用',
				},
				{
					name:'已过期'
				}],
				code: '',
				menuValue: null,
				page: 1,
				total: null,
			};
		},
		onLoad(){
			this.init();	
			this.menuValue = 0
			
		},
		onReachBottom(){
			if( this.total === this.dataList.length ){
				return;
			}
			this.page++;
			this.init();
		},
		components:{
			zNo
		},
		methods: {
			init(){
				this.$http.post('api/coupon/index',{
					use: this.menuValue+1,
					page: this.page
				}).then(res =>{
					console.log(res,"请求到的数据列表优惠券")
					let { list, total } = res;
					this.dataList.push(...list);
					this.total = total;
					
				})
			},
			click(item) {
				this.menuValue = item.index;
				this.page = 1;
				this.dataList = [];
				this.init();
				// this.page++;
			},
			// submit(){
			// 	if( !this.code ){
			// 		uni.showToast({
			// 			icon:'none',
			// 			title:'请输入正确的兑换码'
			// 		})
			// 		return;
			// 	}
			// 	// uni.showModal({
			// 	// 	content:'是否确认兑换优惠券',
			// 	// 	success: (res) => {
			// 	// 		if( res.confirm ){
			// 	// 			this.$http.post('api/coupon/exchange',{
			// 	// 				exchange: this.code
			// 	// 			}).then(res =>{
			// 	// 				uni.showToast({
			// 	// 					icon:'success',
			// 	// 					title:'兑换成功'
			// 	// 				});
			// 	// 				this.page = 1;
			// 	// 				this.dataList = [];
			// 	// 				this.init();
			// 	// 			}).catch(err =>{
			// 	// 				this.code = '';
			// 	// 			})
			// 	// 		}
			// 	// 	}
			// 	// })
				
			// },
		}
	};
</script>

<style scoped lang="scss">
	#serss{
		background-color: #e4e4e4;
	}
	.page {
		background-color: #f8f8f8;
		width: 100%;
		height: 100%;
		overflow-y: auto;
		.no{
			background-color: white;
			padding-top: 50rpx;
		}
		.group {
			flex: 1 1 auto;
			overflow-y: auto;

			.section_1 {
				padding: 18rpx 32rpx 14rpx;
				background-color: rgb(255, 255, 255);

				.text-wrapper {
					padding: 12rpx 0;
					flex: 1 1 auto;
					color: rgb(153, 153, 153);
					font-size: 28rpx;
					line-height: 40rpx;
					white-space: nowrap;
					background-color: rgb(244, 244, 244);
					border-radius: 16rpx;
					height: 64rpx;

					.text_1 {
						margin-left: 24rpx;
					}
				}

				.text-wrapper_1 {
					margin-left: 32rpx;
					padding: 12rpx 0;
					color: rgb(255, 255, 255);
					font-size: 28rpx;
					line-height: 40rpx;
					white-space: nowrap;
					background-color: rgb(24, 181, 103);
					border-radius: 16rpx;
					width: 128rpx;
					height: 64rpx;
				}
			}

			.section_2 {

				background-color: rgb(255, 255, 255);

				.group_1 {
					padding: 22rpx 0 16rpx;

					.text_3 {
						color: rgb(0, 0, 0);
						font-size: 28rpx;
						font-weight: 500;
						line-height: 40rpx;
						white-space: nowrap;
					}

					.text_4 {
						color: rgb(102, 102, 102);
						font-size: 28rpx;
						line-height: 40rpx;
						white-space: nowrap;
					}
				}

				.section_3 {
					margin-left: 56rpx;
					background-color: rgb(24, 181, 103);
					border-radius: 4rpx;
					width: 56rpx;
					height: 8rpx;
				}
			}

			.group_2 {
				padding-bottom: 1224rpx;
				position: relative;

				.section_4 {
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
					top: 20rpx;

					.list-item {
						padding-left: 44rpx;
						background-color: rgba(24, 181, 103, 0.12);
						border-radius: 24rpx;

						&:not(:first-of-type) {
							margin-top: 24rpx;
						}

						.left-group {
							align-self: center;
							width: 160rpx;
							.top-group {
								white-space: nowrap;

								.text_5 {
									color: rgb(24, 181, 103);
									font-size: 26rpx;
									line-height: 38rpx;
								}

								.text_7 {
									color: rgb(24, 181, 103);
									font-size: 50rpx;
									line-height: 76rpx;
								}
							}

							.text_9 {
								margin-top: 10rpx;
								color: rgb(80, 80, 80);
								font-size: 28rpx;
								line-height: 40rpx;
								white-space: nowrap;
							}
						}

						.right-section {
							margin-left: 46rpx;
							padding: 42rpx 40rpx;
							flex: 1 1 auto;
							background-image: url('https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844644139900541.png');
							background-size: 100% 100%;
							background-repeat: no-repeat;
							height: 176rpx;

							.text_11 {
								color: rgb(255, 255, 255);
								font-size: 32rpx;
								font-weight: 500;
								line-height: 44rpx;
								white-space: nowrap;
							}

							.text_13 {
								margin-top: 8rpx;
								color: rgb(255, 255, 255);
								font-size: 28rpx;
								line-height: 40rpx;
								white-space: nowrap;
							}
							.text_133{
								margin-top: 8rpx;
								color:#666666;;
								font-size: 28rpx;
								line-height: 40rpx;
								text-decoration:line-through;
								white-space: nowrap;
									
							}
						}
					}
				}
			}

			.group_3 {
				padding-bottom: 1224rpx;
				position: relative;

				.section_4 {
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
					top: 20rpx;

					.list-item {
						background-color: rgb(255, 255, 255);
						border-radius: 24rpx;

						&:not(:first-of-type) {
							margin-top: 24rpx;
						}

						.top-section {
							padding-left: 44rpx;
							background-color: rgb(228, 228, 228);
							border-radius: 24rpx 24rpx 0px 0px;

							.left-group {
								align-self: center;
								width: 160rpx;
								.top-group {
									white-space: nowrap;

									.text_5 {
										color: rgb(102, 102, 102);
										font-size: 32rpx;
										line-height: 38rpx;
									}

									.text_7 {
										color: rgb(102, 102, 102);
										font-size: 50rpx;
										line-height: 76rpx;
									}
								}

								.text_9 {
									margin-top: 10rpx;
									color: rgb(102, 102, 102);
									font-size: 28rpx;
									line-height: 40rpx;
									white-space: nowrap;
								}
							}

							.right-section {
								margin-left: 46rpx;
								padding: 42rpx 40rpx;
								flex: 1 1 auto;
								background-image: url('https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844648958490621.png');
								background-size: 100% 100%;
								background-repeat: no-repeat;
								height: 176rpx;

								.text_11 {
									color: rgb(255, 255, 255);
									font-size: 32rpx;
									font-weight: 500;
									line-height: 44rpx;
									white-space: nowrap;
								}

								.text_13 {
									margin-top: 8rpx;
									color: rgb(255, 255, 255);
									font-size: 28rpx;
									line-height: 40rpx;
									white-space: nowrap;
								}
							}
						}

						.bottom-group {
							padding: 14rpx 24rpx 16rpx;
							color: rgb(102, 102, 102);
			 			font-size: 24rpx;
							line-height: 34rpx;
							white-space: nowrap;

							.image_1 {
								width: 32rpx;
								height: 32rpx;
							}

							.text_15 {
								margin-left: 8rpx;
							}
						}
					}
				}
			}
		}
	}
</style>
