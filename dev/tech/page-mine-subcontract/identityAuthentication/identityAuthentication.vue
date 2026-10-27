<template>
	<!-- 拍照组件 -->
	<view class="healthInformation">
		<view class="healthInformation-wrap">
			<view class="healthInformation-header" v-if="tips ==''">
				<text>请使用 <text style="color: #FFB83C;">本人</text> 有效的二代身份证</text>
			</view>
			<view class="healthInformation-header" v-else>
				{{tips}}
			</view>
			<!-- 点击拍照 -->
			<view class="photograph" @click="choiceImg('positive')">
				<image v-show="!positive" class="sfrzz" :src="imgUrl+'sfrzz.png'" mode=""></image>
				<view class="img" v-show="!positive"><image :src="imgUrl+'pz.png'" mode=""></image></view>
				<view class="photograph-text" v-show="!positive">点击拍照/上传人像面</view>
				<image class="newImage" v-if="positive" :src="positive" mode="aspectFit"></image>
			</view>
			<view class="photograph" @click="choiceImga('verso')" style="margin-top: 24rpx;">
				<image v-show="!verso" class="sfrzz" :src="imgUrl+'sfrzf.png'" mode=""></image>
				<view class="img" v-show="!verso"><image :src="imgUrl+'pz.png'" mode=""></image></view>
				<view class="photograph-text" v-show="!verso">点击拍照/上传国徽面</view>
				<image class="newImage" v-if="verso" :src="verso" mode="aspectFit"></image>
			</view>
			<view class="healthInformation-bottom">注：拍摄时确保身份证边框完整、字体清晰、亮度均匀</view>
		</view>
		
		<view class="bg_fff border-radius-5 f-s14" style="margin:30rpx 30rpx 0;">
			<view class="dd-flex p-l15 p-r15">
				<text>身份证姓名</text>
				<input type="text" v-model="realname" class="flex1 text-right f-s14" placeholder="输入身份证姓名" placeholder-class=""  style="height:88rpx;">
			</view>
			<view class="dd-flex p-l15 p-r15 border-gray">
				<text>身份证证件号</text>
				<input type="text" v-model="cardsn" class="flex1 text-right f-s14" placeholder="输入身份证件号" placeholder-class=""  style="height:88rpx;">
			</view>
		</view>

		<!-- 提交 -->
		<view class="submit" @click="submit" v-if="zt != 1 && !savedPhoto">提交</view>
	</view>
</template>

<script>
	import { submitCertificates,getMyIndex } from "./api.js"
	export default {
		data() {
			return {
				data:'',
				zt: null, //状态
				tips: "",
				img: "",
				imageUrl: "",
				imgUrl: this.$store.state.imgUrl,
				positive: "", //正面
				verso: "" ,// 反面
				savedPhoto: false, //服务器里是否已有正反面
				realname:'',//身份证姓名
				cardsn:''//身份证件号
			}
		},
		onLoad() {
			this.getInfo()
			// this.getInformation()
		},
		methods: {
			getInfo(){
				uni.showLoading({title:'加载中'})
				this.$db.http('my.Index/index').then(res => {
					if(res.code==1){
						uni.hideLoading()
						let id = res.data.technician_identity
						if(id){
							this.positive = id.front
							this.verso = id.back
							this.realname = id.realname
							this.cardsn = id.cardsn
							this.zt = id.status
							this.savedPhoto = id.status != 2 && !!(id.front && id.back)
							if(id.status==0 && id.front && id.back)this.tips='您的资料正在审核中……';
							if(id.status==0 && (!id.front || !id.back))this.tips='身份证照片未上传，请补传正反面后提交';
							if(id.status==1)this.tips='您的资料已认证通过';
							if(id.status==2){
								this.tips='您的资料未通过审核，请重新提交';
								this.positive = ""
								this.verso = "" 
							}
						}
						// this.data = res.data
					}
				})
			},
			
		//选择图片
		choiceImg(is) {
			if(this.zt==1){
				uni.showToast({title:'资料已认证，不能修改', icon:'none'})
				return
			}
			console.log('正面')
			if(this.zt==0||this.zt==2||this.zt==null){
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
			}
			
			
		
		},
		//选择图片
		choiceImga(is) {
			if(this.zt==1){
				uni.showToast({title:'资料已认证，不能修改', icon:'none'})
				return
			}
			console.log('反面')
			
			if(this.zt==0||this.zt==2 || this.zt==null){
					let _this = this
			uni.chooseImage({
				success: function(res) {
					uni.showLoading({title:'图片上传中'})
					let a = res.tempFilePaths
					for(let i in a){
						_this.uploadImga(a[i],is)
					}
				}
			})
			}
		
		},
			//上传图片
			uploadImg(e,is) {
				let _this = this
				//http://am1.appldkj.com/public/index.php/technician/
				uni.uploadFile({
					url: _this.$cfg['httpUrl']+'index/uploadImage',
					filePath: e,
					name: 'file',
					header:{
						token:_this.$db.get('token')
					},
					success: (res) => {
						var _data = JSON.parse(res.data);
						console.log(_data)
						console.log(is)
						if(_data.code==1){
								_this[is] = _data.data
								uni.hideLoading()
						}
						
					}
				});
			},
			//上传图片
			uploadImga(e,is) {
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
						console.log(_data)
						console.log(is)
						if(_data.code==1){
								_this[is] = _data.data
								uni.hideLoading()
						}
						
					}
				});
			},
			ocr(e){
				console.log("调用ocr")
				let _this=this
				wx.serviceMarket.invokeService({
					service: 'wx79ac3de8be320b71', // '固定为服务商OCR的appid，非小程序appid',
					api: 'OcrAllInOne',
					data: {
						img_url: e,
				        data_type: 3,
				        ocr_type: 1,
					},
				}).then(res => {
					console.log(res,"成功")
					_this.realname = res.data.idcard_res.name.text
					_this.cardsn = res.data.idcard_res.id.text
					_this.positive = e
					_this.verso =e
					uni.hideLoading()
				}).catch(err => {
					console.log(err,"失败")
					_this.positive = ''
					_this.realname = ''
					_this.cardsn = ''
					// _this.verso =''
					uni.showToast({title:'请上传的正确的证件照',icon:'none'})
				})
				
			},
			
			//提交审核
			submit() {
				if(!this.positive){uni.showToast({title:'请上传身份证人像面',icon:'none'}); return}
				if(!this.verso){uni.showToast({title:'请上传身份证国徽面',icon:'none'}); return}
				if(!this.realname){uni.showToast({title:'请填写身份证姓名',icon:'none'}); return}
				if(!this.cardsn){uni.showToast({title:'请填写身份证证件号',icon:'none'}); return}
				let data = {
					front: this.positive,
					back: this.verso,
					realname: this.realname,
					cardsn: this.cardsn
				}
				uni.showLoading({title:'提交中'})
				submitCertificates(data).then(res => {
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
				position: relative;
				width: 100%;
				height: 320rpx;
				display: flex;
				flex-direction: column;
				justify-content: center;
				align-items: center;
				border-top: 2rpx solid #f4f4f4;
				border-bottom: 2rpx solid #f4f4f4;
				background: #F4F4F4;

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
					color: #18B567;
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
			background: #18B567;
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

	.sfrzz {
		position: absolute;
		width: 534rpx;
		height: 209rpx;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
	}
</style>
