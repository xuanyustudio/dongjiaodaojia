<template>
	<!-- 拍照组件 -->
	<view class="healthInformation">
		<view class="healthInformation-wrap">
			<view class="healthInformation-header">
				<text>请上传您的{{cardType}}</text>
			</view>
			<!-- 点击拍照 -->
			<view class="photograph">
				<view @click="photograph" class="img" v-show="!imageUrl">
					<image :src="imgUrl+'pz.png'" mode=""></image>
				</view>
				<view class="photograph-text" v-show="!imageUrl">
					点击拍照/上传{{cardType}}
				</view>
				<image class="newImage" @click="photograph" v-if="imageUrl" :src="imageUrl" mode="aspectFit"></image>
			</view>
			<view class="healthInformation-bottom">
				<!-- 上传本人有效期内的健康证彩色高清实拍照片 -->
				{{tips}}
			</view>
		</view>

		<!-- 提交 -->
		<view class="submit" @click="submit">
			提交
		</view>

	</view>
</template>

<script>
	import {
		submitCertificates,
		getMyIndex
	} from "./api.js"
	export default {
		props: {
			cardType: {
				type: String //证件类型

			},
			tips: {
				type: String //提示
			},
			// 技师认证信息
			technician: {
				type: Object //提示
			}
		},
		data() {
			return {
				imageUrl: '',
				imgUrl: this.$store.state.imgUrl
			}
		},
		methods: {
			// 点击拍照
			photograph() {
				uni.chooseImage({
					sizeType: "compressed",
					count: 1,
					success: (res) => {
						console.log('图片路径' + res.tempFilePaths)
						const uploadTask = uni.uploadFile({
							url: _this.$cfg['httpUrl']+'index/uploadImage',
							filePath: res.tempFilePaths[0], //录音结束后返回的临时路径
							name: 'file', // 文件对应的 key值对象名称
							header: {
								'content-type': 'multipart/form-data',
								'token': uni.getStorageSync('token')
							},
							success: (res) => {
								console.log(JSON.parse(res.data).data)
								this.imageUrl = JSON.parse(res.data).data
							},
							fail: (res) => {}
						})


					}
				});


			},
			//提交审核
			submit() {
				let certificates = this.imageUrl
				submitCertificates({
					certificates
				}).then(res => {
					console.log(res)
					this.getIndex()

				})
			},
			// 获取个人信息
			getIndex() {
				let that = this
				let token = uni.getStorageSync("token")
				getMyIndex({
					openid: token
				}).then(res => {
					//设置个人信息和资质和其他
					console.log(res)
					uni.setStorage({
						key: "infoAndOther",
						data: res.data
					})

				})
			}



		}
	}
</script>

<style lang="less">
	.healthInformation {
		margin-top: 24rpx;

		.healthInformation-wrap {
			width: 686rpx;
			margin: 0 auto;
			background: #fff;
			border-radius: 16rpx;
			padding: 0 24rpx;
			box-sizing: border-box;

			.healthInformation-header {
				height: 88rpx;
				font-size: 32rpx;
				font-family: PingFang SC;
				font-weight: bold;
				line-height: 88rpx;
				color: #262626;
			}

			.photograph {

				width: 100%;
				height: 320rpx;
				display: flex;
				flex-direction: column;
				justify-content: center;
				align-items: center;
				border-top: 2rpx solid #f4f4f4;
				border-bottom: 2rpx solid #f4f4f4;
				background: #E8FAEF;

				.img {
					width: 112rpx;
					height: 112rpx;

					image {
						width: 100%;
						height: 100%;
					}
				}

				.photograph-text {
					font-size: 28rpx;
					font-weight: 500;
					color: #46D17E;
					margin-top: 24rpx;
				}

				.newImage {
					width: 100%;
					height: 100%;
				}
			}

			.healthInformation-bottom {
				height: 88rpx;
				font-size: 24rpx;
				font-weight: 400;
				line-height: 88rpx;
				color: #999999;
			}
		}

		.submit {
			width: 686rpx;
			height: 96rpx;
			background: #46D17E;
			border-radius: 50rpx;
			text-align: center;
			line-height: 96rpx;
			font-size: 32rpx;
			font-weight: 400;
			color: #FFFFFF;
			opacity: 1;
			margin: 0 auto;
			margin-top: 48rpx;
		}
	}
</style>
