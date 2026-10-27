<template>
	<view class="flex-col page">
		<view class="flex-col group">
			<view class="flex-col">
				<view class="justify-between section_1">
					<text class="text_1">头像</text>
					<view class="flex-row">
						<image @click="upload"
							:src="avatar ? avatar : 'https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844656934004060.png'"
							class="image_1" />
						<image
							src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844611732717437.png"
							class="image_2 image_3" />
					</view>
				</view>
				<view class="section_2 justify-between" @click="showPopup = true">
					<text class="text_2">昵称</text>
					<view class="flex-row group_3">
						<text>{{ username }}</text>
						<image
							src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844611732717437.png"
							class="image_2 right" />
					</view>
				</view>
				<view class="section_2 justify-between" @click="show = true">
					<text class="text_4">性别</text>
					<view class="flex-row group_4">
						<text>{{ sex == 1 ? '男' : sex == 2 ? '女' : '未知' }}</text>
						<image
							src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844611732717437.png"
							class="image_2 right" />
					</view>
				</view>
				<view class="section_2 justify-between" v-if="false">
					<text class="text_6">登录密码</text>
					<view class="flex-row group_5">
						<text>设置</text>
						<image
							src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844611732717437.png"
							class="image_2 right" />
					</view>
				</view>
				<view class="justify-between section_3" @click="GOSJ()">
					<text class="text_8">手机号</text>
					<view style="display: flex;align-items: center;">
						<text class="text_9">{{ mobile.slice(0,3) + '****' + mobile.slice(7) }}</text>
						<image
							src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844611732717437.png"
							class="image_2 image_3" />
					</view>
				</view>


				<view class="section_2 justify-between view_3" @click="shim()">
					<text>实名认证</text>
					<image
						src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844611732717437.png"
						class="image_2 image_7" />
				</view>


				<view class="section_2 justify-between view_3" @click="toPath('service')">
					<text>服务协议</text>
					<image
						src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844611732717437.png"
						class="image_2 image_7" />
				</view>
				<view class="section_2 justify-between view_4" @click="toPath('privacy')">
					<text>隐私政策</text>
					<image
						src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844611732717437.png"
						class="image_2 image_8" />
				</view>
				<view class="section_2 justify-between view_5" @click="toPath('about')">
					<text>关于我们</text>
					<image
						src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844611732717437.png"
						class="image_2 image_9" />
				</view>
				<view class="section_2 justify-between view_5" @click="zhuXiao">
					<text style="color:red;">注销账号</text>
					<image
						src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844611732717437.png"
						class="image_2 image_9" />
				</view>
			</view>
			<view class="flex-col items-center button" style="background: red;" @click="logout">
				<text>退出登陆</text>
			</view>
		</view>

		<!-- 设置昵称  @close="close" @open="open"-->
		<u-popup :show="showPopup" mode="center" :round="10">
			<view class="nickname">
				<view class="title">修改昵称</view>
				<u--input placeholder="请输入昵称" border="surround" v-model="nickname"></u--input>
				<view class="btn">
					<view @click="showPopup = false">取消</view>
					<view @click="sub">确认</view>
				</view>
			</view>
		</u-popup>

		<!-- 性别选择 -->
		<u-picker :show="show" :columns="columns" keyName="title" @confirm="confirm" @cancel="show = false"></u-picker>
	</view>
</template>

<script>
	import {
		uploadImage
	} from '@/config/utils'
	export default {
		data() {
			return {
				nickname: "",
				"show": false,
				showPopup: false,
				columns: [
					[{
						title: "未知",
						value: 0
					}, {
						title: "男",
						value: 1
					}, {
						title: "女",
						value: 2
					}]
				],
				"username": "",
				"mobile": "",
				"is_vip": 0,
				"overdue_time": "",
				"avatar": "0",
				"sex": 0,
				"balance": null,
				"collect": 1,
				"coupon": 2,
				"news": 0,
				"unpaid": 8,
				"unpaid_order": null
			};
		},
		onLoad() {
			this.init();
		},
		methods: {
			GOSJ() {
				console.log("点击去往手机号修改")
				uni.navigateTo({
					url: 'iphone'
				})
			},
			shim() {
				console.log("点击去往实名认证")
				uni.navigateTo({
					url: 'sfzh'
				})
			},
			zhuXiao() {
				var that = this;

				uni.showModal({
					title: '提示',
					content: '注销后无法还原，是否继续？',
					success: function(res) {
						if (res.confirm) {

							var guid = uni.getStorageSync('guid');
							if (!guid) {
								uni.showToast({
									title: '您好，请联系平台客服进行账号注销'
								})

								setTimeout(function() {
									uni.navigateBack();
								}, 1000)
							} else {
								that.$http.post('api/user/deleteUser', {
									'guid': guid
								}).then(res => {
									console.log('注销结果', res)

									uni.showToast({
										title: '注销成功'
									})
									setTimeout(function() {
										uni.clearStorage();
										uni.reLaunch({
											url: '/pages/user/login'
										})
									}, 1000)

								})
							}


						} else if (res.cancel) {
							console.log('用户点击取消');
						}
					}
				});
			},
			init() {
				// 获取个人信息
				this.$http.post('api/user/userInfo').then(res => {
					console.log(res)
					for (let i in res) {
						if (i === 'unpaid_order') {
							if (res[i] && JSON.stringify(res[i]) !== '{}') {
								this[i] = res[i]
							}
						} else {
							this[i] = res[i];
						}

					}
				})
			},
			upload() {
				uni.chooseImage({
					count: 1,
					success: (res) => {
						let {
							tempFilePaths
						} = res;
						tempFilePaths = tempFilePaths[0];
						uploadImage(tempFilePaths).then(res => {
							console.log(res)
							this.$http.post('api/user/editUser', {
								field: 'avatar',
								value: res
							}).then(data => {
								this.avatar = res;
								uni.showToast({
									icon: 'success',
									title: '修改成功'
								})
							})
						})
					}
				})
			},
			confirm(e) {
				let {
					value
				} = e;
				value = value[0];
				this.$http.post('api/user/editUser', {
					field: 'sex',
					value: value.value
				}).then(data => {
					this.sex = value.value;
					uni.showToast({
						icon: 'success',
						title: '修改成功'
					})
					this.show = false;
				})
			},
			sub() {
				if (!this.nickname) {
					uni.showToast({
						icon: 'none',
						title: '请先输入昵称'
					})
					return;
				}
				this.$http.post('api/user/editUser', {
					field: 'nickname',
					value: this.nickname
				}).then(data => {
					uni.showToast({
						icon: 'success',
						title: '修改成功'
					});
					this.username = this.nickname;
					this.showPopup = false;
				})
			},
			logout() {
				uni.showModal({
					content: '是否确认退出登录？',
					success: (res) => {
						console.log(res)
						let {
							confirm
						} = res;
						if (confirm) {
							uni.clearStorage();
							uni.reLaunch({
								url: '/pages/user/login'
							})
						}
					}
				})
			},
			toPath(title) {
				uni.navigateTo({
					url: '/bundle/pages/web/web?title=' + title
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

		.nickname {
			width: 600rpx;
			padding: 20rpx;

			.title {
				text-align: center;
				padding: 0 0 20rpx;
			}

			.btn {
				display: flex;
				margin-top: 20rpx;

				view {
					flex: 1;
					height: 80rpx;
					border-radius: 20rpx;
					display: flex;
					align-items: center;
					justify-content: center;
					box-sizing: border-box;
					border: 1rpx solid #E1E1E1;
					font-size: 28rpx;
				}

				view:last-of-type {
					margin-left: 20rpx;
					background-color: rgb(24, 181, 103);
					color: white;
				}
			}
		}

		.group {
			padding-bottom: 408rpx;
			flex: 1 1 auto;
			overflow-y: auto;

			.button {
				margin-top: 64rpx;
				padding: 18rpx 0;
				align-self: center;
				color: rgb(255, 255, 255);
				font-size: 32rpx;
				font-weight: 500;
				line-height: 44rpx;
				white-space: nowrap;
				background-color: rgb(24, 181, 103);
				border-radius: 40rpx;
				width: 600rpx;
			}

			.section_1 {
				padding: 22rpx 32rpx 24rpx;
				color: rgb(0, 0, 0);
				font-size: 32rpx;
				font-weight: 500;
				line-height: 44rpx;
				white-space: nowrap;
				background-color: rgb(255, 255, 255);

				.text_1 {
					align-self: center;
				}

				.image_1 {
					border-radius: 50%;
					width: 112rpx;
					height: 112rpx;
				}

				.image_3 {
					margin-left: 16rpx;
					align-self: center;
				}
			}

			.section_2 {
				padding: 28rpx 32rpx;
				background-color: rgb(255, 255, 255);

				.text_2 {
					color: rgb(0, 0, 0);
					font-size: 32rpx;
					font-weight: 500;
					line-height: 44rpx;
					white-space: nowrap;
				}

				.group_3 {
					color: rgb(102, 102, 102);
					font-size: 32rpx;
					line-height: 44rpx;
					white-space: nowrap;
				}

				.text_4 {
					color: rgb(0, 0, 0);
					font-size: 32rpx;
					font-weight: 500;
					line-height: 44rpx;
					white-space: nowrap;
				}

				.group_4 {
					color: rgb(102, 102, 102);
					font-size: 32rpx;
					line-height: 44rpx;
					white-space: nowrap;
				}

				.text_6 {
					color: rgb(0, 0, 0);
					font-size: 32rpx;
					font-weight: 500;
					line-height: 44rpx;
					white-space: nowrap;
				}

				.group_5 {
					color: rgb(102, 102, 102);
					font-size: 32rpx;
					line-height: 44rpx;
					white-space: nowrap;
				}

				.image_7 {
					margin: 6rpx 0;
				}

				.image_8 {
					margin: 6rpx 0;
				}

				.image_9 {
					margin: 6rpx 0;
				}

				.right {
					margin: 6rpx 0 6rpx 16rpx;
				}
			}

			.section_3 {
				margin-top: 24rpx;
				padding: 28rpx 30rpx 28rpx 32rpx;
				background-color: rgb(255, 255, 255);

				.text_8 {
					color: rgb(0, 0, 0);
					font-size: 32rpx;
					font-weight: 500;
					line-height: 44rpx;
					white-space: nowrap;
				}

				.text_9 {
					color: rgb(102, 102, 102);
					font-size: 32rpx;
					line-height: 44rpx;
					white-space: nowrap;
				}
			}

			.view_3 {
				color: rgb(0, 0, 0);
				font-size: 32rpx;
				font-weight: 500;
				line-height: 44rpx;
				white-space: nowrap;
			}

			.view_4 {
				color: rgb(0, 0, 0);
				font-size: 32rpx;
				font-weight: 500;
				line-height: 44rpx;
				white-space: nowrap;
			}

			.view_5 {
				color: rgb(0, 0, 0);
				font-size: 32rpx;
				font-weight: 500;
				line-height: 44rpx;
				white-space: nowrap;
			}

			.image_2 {
				width: 32rpx;
				height: 32rpx;
			}
		}
	}
</style>
