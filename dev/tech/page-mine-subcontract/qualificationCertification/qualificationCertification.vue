<template>
	<!-- 健康信息 -->
	<view class="healthInformation">
		<view class="healthInformation">
			<view class="healthInformation-wrap">
				<view class="healthInformation-header"><text>{{tips}}</text></view>
				<!-- 点击拍照 -->
				<view class="photograph">
					<view @click="choiceImg('imageUrl')" class="img" v-show="!imageUrl"><image :src="imgUrl+'pz.png'" mode=""></image></view>
					<view class="photograph-text" v-show="!imageUrl">点击拍照/上传资格证</view>
					<image class="newImage" @click="choiceImg('imageUrl')" v-if="imageUrl" :src="imageUrl" mode="aspectFit"></image>
				</view>
				<view class="healthInformation-bottom">上传本人从业资格证书，姓名/照片所在页</view>
			</view>

			<!-- 提交 -->
			<view class="submit" @click="submit" v-if="zt==null || zt==2">提交</view>

		</view>
	</view>
</template>

<script>
	import {
		submitCertificates,
		getMyIndex
	} from "./api.js"
	export default {
		data() {
			return {
				zt: null, //状态
				tips: "请上传您的从业证书",
				img:"",
				imageUrl: "",
				imgUrl: this.$store.state.imgUrl
			}
		},
		onLoad() {
			this.getInfo()
		},
		methods: {
			getInfo(){
				uni.showLoading({title:'加载中'})
				this.$db.http('my.Index/index').then(res => {
					if(res.code==1){
						uni.hideLoading()
						let id = res.data.qualifications
						if(id){
							this.imageUrl = id.certificates
							this.zt = id.status
							if(id.status==0)this.tips='您的资料正在审核中……';
							if(id.status==1)this.tips='您的资料已认证通过';
							if(id.status==2)this.tips='您的资料未通过审核，请重新提交';
						}
						// this.data = res.data
					}
				})
			},
			
			//选择图片
			choiceImg(is) {
				let _this = this
				uni.chooseImage({
					success: function(res) {
						uni.showLoading({title:'图片上传中'})
						let a = res.tempFilePaths
						for(let i in a){
							_this.uploadImg(a[i],is)
						}
					}
				})
			},
			//上传图片
			uploadImg(e,is) {
				let _this = this
				uni.uploadFile({
					url: _this.$cfg['httpUrl']+'index/uploadImage',
					filePath: e,
					name: 'file',
					header:{
						token:_this.$db.get('token')
					},
					success: (res) => {
						var _data = JSON.parse(res.data);
						if(_data.code==1){
							_this[is] = _data.data
							uni.hideLoading()
						}
					}
				});
			},
			
			//提交审核
			submit() {
				if(!this.imageUrl){uni.showToast({title:'请上传证件照',icon:'none'}); return}
				let data = {
					certificates: this.imageUrl
				}
				uni.showLoading({title:'提交中'})
				this.$db.http('my.Index/setQualifications',data).then(res => {
					if(res.code==1){
						uni.showToast({title: "提交成功"})
						setTimeout(() => {
							uni.navigateBack()
						}, 1000)
					}
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
				background: #DEF4F0;

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
					color: #0ea17d;
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
			background: #0ea17d;
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
