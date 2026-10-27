<template>
	<view class="flex-col page">
		<uni-nav-bar title="" backgroundColor="linear-gradient(to right, #12AB51 , #12AB51 )"></uni-nav-bar>
		<view class="flex-col group">

			<view class="flex-col group_3">
				<view class="flex-coler  section_1" style="height:350rpx;">
					<view class="flex-rows">
						<view class="image_3">
							<u-avatar size="55"
								:src="avatar ? avatar : 'https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844656934004060.png'">
							</u-avatar>
						</view>
						<view class="flex-cols group_2" id="ser">
							<view class="text username" style="color: black;font-weight: 500;">{{ nickname }}</view>
							<view class="text_1">TEL：{{ mobile }}</view>
						</view>
					</view>
					<view class="flex-row equal-division">
						<view class="flex-colers equal-division-item" @click="toPath('/bundle/pages/wallet/wallet')">
							<text class="text_5 price-font">{{ Number(balance)}}</text>
							<view class="text_4">
								<image src="@/static/icons/youhui.png"></image>余额
							</view>
						</view>
						<view class="flex-colers equal-division-item_1" @click="toPath('/bundle/pages/coupon/coupon')">
							<view class="flex-row group_5">
								<text class="text_7 price-font">{{ coupon }}</text>
								<text class="text_8">张</text>
							</view>
							<view class="text_6">
								<image src="@/static/icons/youhui.png"></image>优惠券
							</view>
						</view>
					</view>
				</view>
				<view class="flex-col section_3">
					<view class="flex-row justify-bw group_6">
						<text class="text_9">我的订单</text>
						<view class="flex-row group_7" @click="toOrder('0')">
							<text>全部订单</text>
							<image src="@/static/my/left.png" class="image_8" />
						</view>
					</view>
					<view class="flex-row equal-division_1 justify-sa">
						<view class="equal-division-item_2 flex-col items-center" @click="toOrder('1')">
							<image src="@/static/icons/df.png" class="image_9" />
							<text class="text_11">待付款</text>
						</view>
						<view class="equal-division-item_2 flex-col items-center" @click="toOrder('2')">
							<image src="@/static/icons/dq.png" class="image_9" />
							<text class="text_11">待确认</text>
						</view>
						<view class="equal-division-item_2 flex-col items-center" @click="toOrder('3')">
							<image src="@/static/icons/jx.png" class="image_9" />
							<text class="text_11">进行中</text>
						</view>
						<view class="equal-division-item_2 flex-col items-center" @click="toOrder('4')">
							<image src="@/static/icons/yw.png" class="image_9" />
							<text class="text_11">已完成</text>
						</view>
						<view class="equal-division-item_2 flex-col items-center" @click="toOrder('5')">
							<image src="@/static/icons/dd.png" class="image_9" />
							<text class="text_11">已取消</text>
						</view>
					</view>
					<!-- 我的页面待支付注释 -->
					<!-- <view class="flex-col section_4" v-if="unpaid && end_t !== '已过期'">
						<view class="justify-bw flex-row">
							<text class="text_17">订单待支付</text>
							<text class="text_18">支付剩余时间{{ end_t }}</text>
						</view>
						<view class="flex-col group_14">
							<text class="text_19">{{ unpaid_order.service_name }}</text>
							<text class="text_20">服务时间：{{ unpaid_order.service_time }}</text>
						</view>
						<view class="flex-row justify-bw">
							<view class="group_16">
								<text class="text_21">￥</text>
								<text class="text_22 price-font1">{{ unpaid_order.pay_money }}</text>
							</view>
							<view class="flex-col items-center text-wrapper_1 text_c" @click="nextPay(unpaid_order.order_sn)">
								<text >立即支付</text>
							</view>
						</view>
					</view> -->
				</view>
				<view class="flex-col section_5">
					<text class="text_24">常用功能</text>
					<view class="grid">
						<view class="grid-item flex-col items-center"
							@click="toPath('/bundle/pages/user_ollection/user_ollection')">
							<image src="@/static/icons/shou.png" class="image_9 center" />
							<text>我的收藏</text>
						</view>
						<view class="grid-item flex-col items-center" @click="toPath('/bundle/pages/wallet/wallet')">
							<image src="@/static/icons/qian.png" class="image_9 center" />
							<text>我的钱包</text>
						</view>
						<view class="grid-item flex-col items-center"
							@click="toPath('/bundle/pages/feedback/feedback')">
							<image src="@/static/icons/xie.png" class="image_9 center" />
							<text>意见反馈</text>
						</view>
						<view class="grid-item flex-col items-center"
							@click="toPath('/bundle/pages/user_address/user_address')">
							<image src="@/static/icons/ding.png" class="image_9 center" />
							<text>地址管理</text>
						</view>
						<view class="grid-item flex-col items-center" @click="toPath('/bundle/pages/settled/settled')">
							<image src="@/static/icons/yong.png" class="image_9 center" />
							<text>技师入驻</text>
						</view>
						<!-- @click="call" -->
						<view class="grid-item flex-col items-center" @click="rightBtn">
							<image src="@/static/icons/liao.png" class="image_9 center" />
							<text>联系客服</text>
						</view>
						<view v-if="config && config.showYaoQing" class="grid-item flex-col items-center"
							@click="toPath('/bundle/pages/share/share')">
							<image src="@/static/icons/yong1.png" class="image_9 center" />
							<text>邀请好友</text>
						</view>
					</view>
				</view>

				<!-- 客服弹窗 -->
				<uni-popup ref="popupKf" type="center" :mask-click="false">
					<view class="wx">
						<view class="wx_kf">
							联系客服</br>
							手机号：<text style="color: #55aaff;">{{mobiles}}</text>
						</view>
						<view class="wx_fz" @click="copybtn">
							复制
						</view>
						<view class="wx_close" @click="closePopup()">
							<image src="@/static/images/close_btn.png" mode=""></image>
						</view>
					</view>
				</uni-popup>
				<!-- <image
					src="https://yiyanyuese.oss-cn-beijing.aliyuncs.com/images/H5/share.png"
					class="image_21" /> -->
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				"nickname": "",
				"mobile": "",
				"is_vip": 1,
				"overdue_time": "",
				"avatar": "",
				"sex": 0,
				"balance": 0,
				"collect": 0,
				"coupon": 0,
				"news": 0,
				"unpaid": 0,
				"unpaid_order": null,
				end_t: '',
				mobiles: '',
				config: []
			};
		},
		onShow() {
			this.getConfig();
			this.init();

		},
		methods: {
			rightBtn() {
				this.$refs.popupKf.open()
			},
			copybtn() {
				uni.setClipboardData({
					data: this.mobiles,
					success: function() {
						// console.log('success');
					}
				});
			},
			closePopup() { //關閉客服彈窗
				this.$refs.popupKf.close()
			},
			getConfig() {
				this.config = {
					showYaoQing: true,
					showVip: true,
					showJsInfo: true,
					showBj: true
				}
			},
			// 获取客服数据
			async getCustomerService() {
				var that = this
				await uni.request({
					url: 'http://127.0.0.1:8088/public/index.php/api/user/customerService',
					method: "GET",
					success(res) {
						// console.log(res);
						if (res.statusCode == 200) {
							that.serviceData = res.data
						} else {
							console.log(res.errMsg);
						}

					}
				})
			},
			showtime(endtime) {
				var nowtime = new Date(), //获取当前时间
					endtime = new Date(endtime.replace(/-/g, '/')); //定义结束时间
				var lefttime = endtime.getTime() - nowtime.getTime(), //距离结束时间的毫秒数
					leftd = Math.floor(lefttime / (1000 * 60 * 60 * 24)), //计算天数
					lefth = Math.floor(lefttime / (1000 * 60 * 60) % 24), //计算小时数
					leftm = Math.floor(lefttime / (1000 * 60) % 60), //计算分钟数
					lefts = Math.floor(lefttime / 1000 % 60); //计算秒数
				if (lefttime <= 0) {
					return '已过期'
				}
				return this.add0(leftd) + "天" + this.add0(lefth) + ":" + this.add0(leftm) + ":" + this.add0(
				lefts); //返回倒计时的字符串
			},
			//导航栏图标事件
			onNavigationBarButtonTap: function(e) {
				let {
					index
				} = e;
				if (index === 1) {
					uni.navigateTo({
						url: "/bundle/pages/message/message"
					})
				}
				if (index === 0) {
					uni.navigateTo({
						url: "/bundle/pages/user_profile/user_profile"
					})
				}
				console.log(index);
			},
			add0(a) {
				a = Number(a)
				return a < 10 ? '0' + a : a;
			},
			init() {
				// 获取个人信息
				this.$http.post('api/user/userInfo').then(res => {
					for (let i in res) {
						console.log(res, "会员信息")
						if (i === 'unpaid_order') {
							if (res[i] && JSON.stringify(res[i]) !== '{}') {
								this[i] = res[i]
							}
						} else if (i === 'guid') {
							console.log('GUID的值', res[i])
							if (res[i])
								uni.setStorageSync('guid', res[i])
						} else {
							this[i] = res[i];

						}

					}
					
					if(!this.nickname || !this.mobile){
						uni.showToast({
							title:"账号已失效,请重新登陆",
							icon:"none"
						})
						
						uni.reLaunch({
							url: '/pages/user/login'
						})
					}
					
					if (this.unpaid_order) {
						console.log(this.showtime(this.unpaid_order.service_time))
						this.end_t = this.showtime(this.unpaid_order.service_time);
						let timer = null;
						if (this.end_t !== '已过期') {
							timer = setInterval(() => {
								if (this.end_t === '已过期') {
									clearInterval(timer);
									return;
								}
								this.end_t = this.showtime(this.unpaid_order.service_time)
							}, 1000)
						}
					}
				});
				// 获取客服信息 api/user/customerService
				this.$http.post('api/user/customerService').then(res => {
					console.log(res, 'xxxx')
					let {
						mobile
					} = res;
					this.mobiles = mobile;
				})

			},
			nextPay(sn) {
				uni.navigateTo({
					url: '/bundle/pages/order_details/order_details?order_sn=' + sn
				})
			},
			toPath(url) {
				uni.navigateTo({
					url
				})
			},
			toOrder(index) {
				uni.setStorageSync('tabsIndex', index);
				uni.reLaunch({
					url: '/pages/order/order'
				})
			},
			call() {
				// if( this.mobiles ){
				// 	uni.showModal({
				// 		content:'是否拨打客服电话',
				// 		success: (res) => {
				// 			if( res.confirm ){
				// 				uni.makePhoneCall({
				// 					phoneNumber:this.mobiles
				// 				});
				// 			}
				// 		}
				// 	})
				// 	return;
				// }
				// uni.showToast({
				// 	icon:'none',
				// 	title:'暂无客服联系号码'
				// })
				console.log("点击去往客服")
				uni.navigateTo({
					url: 'kefu'
				})
			}
		}
	};
</script>

<style scoped lang="scss">
	.wx {
		position: relative;
		width: 500rpx;
		height: 380rpx;
		background-color: #fff;
		border-radius: 20rpx;
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: space-around;
		padding: 30rpx 0;

		.wx_img {
			image {
				width: 265rpx;
				height: 265rpx;
				border: 2rpx solid #17b361;
				pointer-events: none;
				/* 禁止长按图片保存 */
			}
		}

		.wx_kf {
			text-align: center;
			font-size: 34rpx;
			font-family: Source Han Sans CN;
			font-weight: 400;
			color: #17b361;
			line-height: 88rpx;
		}

		.wx_fz {
			width: 266rpx;
			height: 88rpx;
			background: #17b361;
			border-radius: 44rpx;
			font-size: 30rpx;
			font-family: Source Han Sans CN;
			font-weight: 400;
			color: #FFFFFF;
			line-height: 88rpx;
			text-align: center;
		}

		.wx_close {
			position: absolute;
			top: 18rpx;
			right: 18rpx;

			image {
				width: 30rpx;
				height: 30rpx;
			}
		}
	}

	.flex-rows {
		display: flex;
		flex-direction: column;
		align-items: center;
		height: 20rpx;
		z-index: 10;
		margin: -80rpx 0 0 0;
	}

	.flex-cols {
		// display: flex;
		flex-direction: column;
		align-items: center;
	}

	.page .group .group_3 .section_1[data-v-0be17cc6] {
		padding-bottom: 13px;
		background-color: white;
		border-radius: 13px;
		height: 450rpx;
	}

	.flex-coler {
		display: flex;
		flex-direction: column;
		justify-content: flex-end;
	}

	.flex-colers {
		display: flex;
		flex-direction: column;
		justify-content: flex-end;
		align-items: center;
	}

	.items-center {
		display: flex;
		align-items: center;
		justify-content: space-evenly;
	}

	#ser {
		width: 90%;
		height: 100rpx;
		display: flex;
	}















	.page {
		width: 100%;
		height: 100%;

		.group {
			background: linear-gradient(to right, #12AB51, #18B567);
			background-repeat: no-repeat;
			background-size: 100% 250rpx;
			padding: 50rpx 32rpx 30rpx;
			flex: 1 1 auto;

			.group_3 {
				margin-top: 70rpx;

				.section_1 {
					padding-bottom: 24rpx;
					background-color: rgb(255, 255, 255);
					border-radius: 24rpx;

					.section_2 {
						padding: 24rpx 24rpx 20rpx;
						height: 0rpx;
						border-radius: 24rpx 24rpx 0px 0px;

						.group_4 {
							margin-bottom: 6rpx;
							color: rgb(226, 189, 128);
							font-size: 28rpx;
							line-height: 40rpx;
							white-space: nowrap;

							.image {
								width: 48rpx;
								height: 48rpx;
							}

							.text_2 {
								margin: 4rpx 0 4rpx 16rpx;
							}
						}

						.text-wrapper {
							padding: 10rpx 0 6rpx;
							color: rgb(38, 38, 38);
							font-size: 28rpx;
							line-height: 40rpx;
							white-space: nowrap;
							background-image: linear-gradient(124.3deg,
									rgb(255, 218, 157) 0%,
									rgb(255, 218, 157) -7.15%,
									rgb(231, 191, 124) 104.11%,
									rgb(231, 191, 124) 100%);
							border-radius: 27rpx;
							width: 168rpx;
							height: 56rpx;
						}
					}

					.equal-division {
						margin: 26rpx 25rpx 0 24rpx;

						.equal-division-item {
							padding: 24rpx 24rpx;
							flex: 1 1 307rpx;
							border-radius: 12rpx;
							height: 144rpx;
							position: relative;

							.text_4 {
								color: #666666;
								font-size: 15px;
								line-height: 22px;
								display: flex;
								flex-wrap: wrap;
								align-items: center;
								justify-content: space-between;
								width: 100rpx;

								image {
									width: 35rpx;
									height: 25rpx;
								}

							}

							.text_5 {
								margin-top: 8rpx;
								color: rgb(0, 0, 0);
								font-size: 40rpx;
								line-height: 48rpx;
								white-space: nowrap;
							}
						}

						.equal-division-item_1 {
							margin-left: 23rpx;
							padding: 24rpx 24rpx;
							flex: 1 1 307rpx;
							border-radius: 12rpx;
							height: 144rpx;
							position: relative;

							.text_6 {
								color: #666666;
								font-size: 15px;
								line-height: 22px;
								display: flex;
								flex-wrap: wrap;
								align-items: center;
								justify-content: space-between;
								width: 130rpx;

								image {
									width: 35rpx;
									height: 25rpx;
								}

							}

							.group_5 {
								margin-top: 8rpx;

								.text_7 {
									color: rgb(0, 0, 0);
									font-size: 40rpx;
									line-height: 48rpx;
									white-space: nowrap;
								}

								.text_8 {
									margin-left: 6rpx;
									margin-top: 12rpx;
									color: rgb(153, 153, 153);
									font-size: 24rpx;
									line-height: 34rpx;
									white-space: nowrap;
								}
							}
						}

						.image_5 {
							width: 106rpx;
							height: 104rpx;
							position: absolute;
							right: -1rpx;
							bottom: 0;
						}
					}
				}

				.section_3 {
					margin-top: 24rpx;
					padding: 24rpx 0;
					background-color: rgb(255, 255, 255);
					border-radius: 24rpx;

					.group_6 {
						padding: 0 24rpx;

						.text_9 {
							color: rgb(0, 0, 0);
							font-size: 32rpx;
							font-weight: 500;
							line-height: 44rpx;
							white-space: nowrap;
						}

						.group_7 {
							color: rgb(102, 102, 102);
							font-size: 28rpx;
							line-height: 40rpx;
							white-space: nowrap;

							.image_8 {
								margin: 4rpx 0;
								width: 32rpx;
								height: 32rpx;
							}
						}
					}

					.equal-division_1 {
						margin-top: 22rpx;
						color: rgb(0, 0, 0);
						font-size: 24rpx;
						line-height: 34rpx;
						white-space: nowrap;
						padding: 0 21rpx;

						.equal-division-item_2 {

							padding: 10rpx 0;

							.text_11 {
								margin-top: 8rpx;
							}
						}
					}

					.section_4 {
						margin: 22rpx 24rpx 0;
						padding: 16rpx 22rpx 24rpx 24rpx;
						background-image: url('https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844703083187462.png');
						background-position: 0px 0px;
						background-size: 100% 100%;
						background-repeat: no-repeat;

						.group_14 {
							margin-top: 24rpx;

							.text_19 {
								color: rgb(0, 0, 0);
								font-size: 28rpx;
								font-weight: 500;
								line-height: 40rpx;
								white-space: nowrap;
							}

							.text_20 {
								margin-top: 4rpx;
								color: rgb(102, 102, 102);
								font-size: 24rpx;
								line-height: 34rpx;
								white-space: nowrap;
							}
						}

						.text_17 {
							color: rgb(102, 102, 102);
							font-size: 28rpx;
							font-weight: 500;
							line-height: 40rpx;
							white-space: nowrap;
						}

						.text_18 {
							margin-bottom: 4rpx;
							color: rgb(243, 151, 53);
							font-size: 24rpx;
							line-height: 34rpx;
							white-space: nowrap;
						}

						.group_16 {
							margin: 6rpx 0 4rpx;
							white-space: nowrap;
							height: 38rpx;

							.text_21 {
								color: rgb(255, 74, 74);
								font-size: 24rpx;
								line-height: 28rpx;
							}

							.text_22 {
								color: rgb(255, 74, 74);
								font-size: 32rpx;
								line-height: 38rpx;
							}
						}

						.text-wrapper_1 {
							padding: 8rpx 0 6rpx;
							color: rgb(255, 255, 255);
							font-size: 24rpx;
							line-height: 34rpx;
							white-space: nowrap;
							background-image: linear-gradient(116.3deg,
									rgb(53, 219, 160) 0%,
									rgb(53, 219, 160) -13.77%,
									rgb(24, 181, 103) 111.69%,
									rgb(24, 181, 103) 100%);
							border-radius: 24rpx;
							width: 128rpx;
							height: 48rpx;
						}
					}
				}

				.section_5 {
					margin-top: 24rpx;
					padding: 24rpx 0 20rpx;
					background-color: rgb(255, 255, 255);
					border-radius: 24rpx;

					.text_24 {
						margin-left: 24rpx;
						color: rgb(0, 0, 0);
						font-size: 32rpx;
						font-weight: 500;
						line-height: 44rpx;
						white-space: nowrap;
					}

					.grid {

						margin-top: 24rpx;
						color: rgb(0, 0, 0);
						font-size: 24rpx;
						line-height: 34rpx;
						white-space: nowrap;
						height: 280rpx;
						display: flex;
						flex-wrap: wrap;

						.grid-item {
							width: 25%;
							margin: 0;
						}
					}
				}

				.image_21 {
					margin-top: 24rpx;
					border-radius: 24rpx;
					width: 91.5vw;
					height: 21.5vw;
				}

				.image_9 {
					width: 60rpx;
					height: 60rpx;
				}
			}

			.image_3 {
				border: 5rpx solid #FFFFFF;
				border-radius: 50%;
				margin: -200rpx 0 0 0px;
			}

			.group_2 {
				margin: 10rpx 0 10rpx 16rpx;

				.text {
					color: rgb(0, 0, 0);
					font-size: 32rpx;
					font-weight: 500;
					line-height: 44rpx;
					white-space: nowrap;
				}

				.text_1 {
					margin-top: 2rpx;
					color: black;
					font-size: 28rpx;
					line-height: 40rpx;
					white-space: nowrap;
				}
			}
		}
	}

	.username {}
</style>
