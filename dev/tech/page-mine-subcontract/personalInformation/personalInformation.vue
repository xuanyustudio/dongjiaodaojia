<template>
	<!-- 个人信息 -->
	<view class="information">
		<!-- 提示 -->
		<view class="tips">
			<view class="tips-header">
				<view class="tips-header-icon"><!-- <image src="" mode=""></image> --></view>
				<view class="tips-header-title">个人信息认证</view>
			</view>
			<view class="tips-text">需要填写以下信息，以进行资质审查，所有填写的信息仅用于认证，平台绝不外泄。</view>
		</view>
		<view class="setup">
			<view class="setup-item" v-for="(item,index) in setupData" :key="item.title" @tap="tourl(item.path)">
				<view class="setup-item-left">
					<view class="setup-item-left-title">{{item.title}}</view>
					<view class="setup-item-left-text">{{item.text}}</view>
				</view>
				<view class="setup-item-right">
					<view class="buttom" v-if="item.border==null">去上传</view>
					
					<view class="buttom toFinishStatus" v-if="item.border==1">已认证</view>
					
					<view class="buttom-noneBorder" style="color:red" v-if="item.border==2">未通过，请重新上传</view>
					<u-icon name="arrow-right" color="red" size="18" v-if="item.border==2"></u-icon>
					
					<view class="buttom-noneBorder" style="color:#666" v-if="item.border==0">认证中</view>
					<u-icon name="arrow-right" color="#666" size="18" v-if="item.border==0"></u-icon>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {getMyIndex} from "./api.js"
	export default {
		data() {
			return {
				setupData:[
					{
						title:"身份认证",
						text:"请上传本人的正反面身份证照片",
						border:null,
						path:"/page-mine-subcontract/identityAuthentication/identityAuthentication"
					},
					{
						title:"头像/相册",
						text:"请上传本人头像和相册",
						border:null,
						path:"/pages/mine/avatar"
					},
					{
						title:"资质信息",
						text:"请上传从业资格证书照片",
						border:null,
						path:"/page-mine-subcontract/qualificationCertification/qualificationCertification"
					},
					
					{
						title:"健康信息",
						text:"请上传有效期内的健康证照片",
						border:null,
						path:"/page-mine-subcontract/healthInformation/healthInformation"
					},
					{
						title:"基本资料",
						text:"身高/个人简介等基本信息",
						border:null,
						path:"/page-mine-subcontract/essentialInformation/essentialInformation"
					},
				]

			}
		},
		onShow() {
			this.getIndex()
		},
		methods: {
			// 获取个人信息
			getIndex() {
				uni.showLoading({title:'加载中'})
				let token = uni.getStorageSync("token")
				getMyIndex({openid: token}).then(res => {
					console.log(res,"打印的子")
					if(res.code==1){
						uni.hideLoading()
						let data = res.data
						let info = res.data.info
						this.setupData.forEach(item => {
							if (item.title == "身份认证") {
								const idcard = data.technician_identity
								if(idcard && idcard.front && idcard.back) item.border = idcard.status
								else item.border = null
							}
							if (item.title == "头像/相册") {
								if(data.technician_album) item.border = data.technician_album.status
								else item.border = null
							}
							if (item.title == "资质信息") {
								if(data.qualifications) item.border = data.qualifications.status
								else item.border = null
							}
							if (item.title == "健康信息") {
								if(data.technician_healthy) item.border = data.technician_healthy.status
								else item.border = null
							}
							if (item.title == "基本资料") {
								if(data.technician_data) item.border = data.technician_data.status
								else item.border = null
							}
						})
						this.$db.set('infoAndOther',res.data)
					}
				})
			},
			tourl(e) {
				uni.navigateTo({
					url: e
				})
			}
		}
	}
</script>

<style lang="less">
	.information {
		width: 100%;
		height: 100%;
		background: #FFFFFF;

		.tips {
			height: 192rpx;
			background: #FFFFFF;
			border: 1rpx solid #fff;

			.tips-header {
				margin-top: 8rpx;
				height: 76rpx;
				display: flex;
				align-items: center;
				padding: 0 32rpx;

				.tips-header-icon {
					width: 36rpx;
					height: 36rpx;
					border-radius: 50%;
					background: linear-gradient(180deg, #46D17E 0%, #3EC735 100%);
				}

				.tips-header-title {
					font-size: 32rpx;
					font-weight: bold;
					color: #262626;
					margin-left: 16rpx;

				}

			}

			.tips-text {
				font-size: 28rpx;
				font-weight: 400;
				color: #666666;
				padding: 0 32rpx;
			}
		}

		.setup {
			.setup-item {
				box-sizing: border-box;
				width: 100%;
				height: 140rpx;
				display: flex;
				justify-content: space-between;
				align-items: center;
				padding: 0 32rpx;
				border-top: #d6d7d9 solid 1rpx;

				.setup-item-left {
					.setup-item-left-title {
						font-size: 32rpx;
						font-weight: bold;
						color: #262626;
					}

					.setup-item-left-text {
						font-size: 28rpx;
						font-weight: 400;
						color: #666666;
						margin-top: 10rpx;
					}

				}

				.setup-item-right {
					display: flex;
					justify-content: space-between;
					align-items: center;

					.buttom {
						width: 142rpx;
						height: 56rpx;
						background: #FFFFFF;
						border: 4rpx solid #0ea17d;
						border-radius: 48rpx;
						text-align: center;
						line-height: 56rpx;
						font-size: 30rpx;
						font-weight: bold;
						color: #0ea17d;
						&.toFinishStatus{
							color: #E4E4E4;
							border: 4rpx solid #E4E4E4;
						}
					}

					.buttom-noneBorder {
						font-size: 28rpx;
						font-weight: 400;
						color: #46D17E;
						margin-right: 13rpx;
					}

				}
			}
		}
	}
</style>
