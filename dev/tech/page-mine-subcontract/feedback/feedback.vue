<template>
	<!-- 意见反馈 -->
	<view class="Feedback">
		<!-- 反馈类别 -->
		<view class="Feedback-type">
			<view class="Feedback-type-top" style="display: flex;color: #D30202 ;font-weight: 600;justify-content: space-around;
    padding-left: 110rpx;">
				意见反馈
				<view class="feedbackRecord" @click="goFeedbackRecord">
					反馈记录
					<view style="width: 60rpx ;height: 4rpx ;background-color: #D30202;border-radius: 20rpx;"></view>
				</view>
			</view>
			
			<view class="type">
				<view v-for="(item,index) in typeArr" @click="changeType(index)" :key="item"
					:class="feedbackType==index?'highLight':''">
					{{item}}
				</view>
			</view>
		</view>
		<!-- 反馈内容 -->
		<view class="Feedback-con">
			<view class="Feedback-con-top" style="font-weight: 600;">
				问题说明
			</view>
			<view class="con">
				<textarea :value="content" @input="changeContent" placeholder="请输入15字以上的描述，我们才能更好给您提供帮助" />
			</view>
			<!-- 反馈照片 -->
			<view class="imgBox">
				<view class="img" v-for="(item,index) in imgArr" :key="item">
					<image :src="item" mode="aspectFit"></image>
					<view class="del" @tap="del(index)">删除</view>
				</view>
				<!-- 点击选择照片 -->
				<view class="choice" @click="choiceImg">
					<image :src="imgUrl+'sctp.png'" mode=""></image>
				</view>

			</view>

		</view>
		<!-- 联系方式 -->
		<view class="phone">
			<view class="phone-text" style="font-weight: 600 ;">
				联系方式
			</view>
			<textarea @input="changeMobile" :value="mobile" placeholder="留下您的联系方式方便我们联系您哦~" />
		</view>
		<!-- 提交反馈 -->
		<view class="submit" @click="handSubmit">
			提交反馈
		</view>
		<!-- 查看反馈记录 -->
		<!-- <view class="feedbackRecord" @click="goFeedbackRecord">
			查看反馈记录
		</view> -->
		<view class="p-t20"></view>
	</view>
</template>

<script>
	import {
		feedbackAdd
	} from "./api.js"
	export default {
		data() {
			return {
				imgUrl: this.$store.state.imgUrl,
				imgArr: [],
				image: [], //提交到后台的图片数组
				feedbackType: 0,
				mobile: "",
				content: "",
				typeArr: ["BUG", "建议", "投诉", "吐槽"]
			}
		},
		onLoad() {
			this.mobile = this.$db.get('info').mobile
		},
		methods: {
			//改变反馈类别
			changeType(index) {
				this.feedbackType = index
				console.log(this.typeArr[index])
			},
			//选择图片
			choiceImg() {
				let _this = this
				uni.chooseImage({
					success: function(res) {
						uni.showLoading({title:'图片上传中'})
						let a = res.tempFilePaths
						for(let i in a){
							_this.imgArr.push(a[i])
							_this.uploadImg(a[i])
						}
					}
				})
			},
			//上传图片
			uploadImg(e) {
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
							_this.image.push(_data.data)
							if(_this.image.length == _this.imgArr.length){
								uni.hideLoading()
							}
						}
					}
				});
			},
			del(e){
				this.imgArr.splice(e, 1);
				this.image.splice(e, 1);
			},
			//监听修改反馈内容
			changeContent(e) {
				this.content = e.detail.value
			},
			//监听修改联系方式
			changeMobile(e) {
				this.mobile = e.detail.value
			},
			// 提交反馈
			handSubmit() {
				let obj = {
					openid: this.$db.get('openid'),
					content: this.content,
					type: this.typeArr[this.feedbackType],
					image: this.image,
					mobile: this.mobile
				}
				uni.showLoading({title: '加载中'})
				feedbackAdd(obj).then(res => {
					uni.hideLoading()
					if(res.code == 1){
						uni.showToast({title:res.msg,icon:"none"})
						setTimeout(()=>{
							uni.navigateBack()
						},1000)
					}
				})

			},
			// 跳转反馈记录
			goFeedbackRecord() {
				uni.navigateTo({
					url: "/page-mine-subcontract/feedbackRecord/feedbackRecord"
				})
			}
		}
	}
</script>

<style lang="less">
	.feedbackRecord {
		width: 50%;
		color: #D30202;
		font-weight: 600;
		height: 60rpx;
		display: flex;flex-direction: column;
    justify-content: space-between;
    align-items: center;
	
	}
	.Feedback {
		box-sizing: border-box;

		width: 100%;
		height: 100%;
		background: #FFFFFF;

		.Feedback-type {
			box-sizing: border-box;
			padding: 0 32rpx;
			width: 100%;
			height: 220rpx;
			border-bottom: 2rpx solid #F8F8F8;
			

			.Feedback-type-top {
				box-sizing: border-box;
				padding: 40rpx 0;
				font-size: 32rpx;
				font-weight: 400;
				color: #000000;
				
			}

			.type {
				display: flex;
				justify-content: space-between;

				view {
					width: 154rpx;
					height: 64rpx;
					// background: rgba(24, 181, 103, 0.18);
					// border: 2px solid #18B567;
					border-radius: 12rpx;
					background: #F4F4F4;
					line-height: 64rpx;
					text-align: center;
					font-size: 28rpx;
					font-weight: 400;
					color: #999999;
				}

				.highLight {
					background: rgba(24, 181, 103, 0.18);
					border: 2rpx solid #18B567;
					color: #18B567;
				}
			}
		}

		.Feedback-con {
			box-sizing: border-box;
			padding: 40rpx 32rpx 0 32rpx;
			background: #FFFFFF;
			border-bottom: 2rpx solid #F8F8F8;

			.Feedback-con-top {
				margin-bottom: 28rpx;
			}

			.con {
				textarea {
					width: 100%;
					font-size: 28rpx;
					font-weight: 400;
					color: #999999;
					height: 160rpx;
				}
			}

			.imgBox {
				width: 100%;
				display: flex;
				// justify-content: space-between;
				flex-wrap: wrap;
				gap: 15rpx;
				margin: 0;
				padding: 0;
				margin-bottom: 40rpx;

				.img {
					width: 212rpx;
					height: 212rpx;
					border: 2rpx dotted #979797;
					opacity: 1;
					border-radius: 16rpx;
					margin-top: 30rpx;
					position: relative;
					overflow: hidden;
					image {
						width: 100%;
						height: 100%;
					}
				}

			}

			.choice {
				display: flex;
				align-items: center;
				justify-content: center;
				width: 212rpx;
				height: 212rpx;
				border: 2rpx dotted #979797;
				opacity: 1;
				border-radius: 16rpx;
				margin-top: 30rpx;

				image {
					width: 56rpx;
					height: 56rpx;
				}
			}
		}

		.phone {
			box-sizing: border-box;
			padding: 40rpx 32rpx 0 32rpx;

			.phone-text {
				font-size: 32rpx;
				font-weight: 400;
				color: #000000;
			}

			textarea {
				font-size: 28rpx;
				font-weight: 400;
				color: #999999;
				margin-top: 28rpx;
				height: 100rpx;
				width: 100%;
			}
		}

		.submit {
			width: 686rpx;
			height: 96rpx;
			background: #0DA17D ;
			border-radius: 50rpx;
			font-weight: 400;
			color: #FFFFFF;
			line-height: 96rpx;
			text-align: center;
			margin: 0 auto;
			margin-top: 200rpx;
		}

		
	}
	.del {
		position: absolute;
		left: 0;
		right: 0;
		bottom: 0;
		text-align: center;
		color: #fff;
		line-height: 56rpx;
		font-size: 24rpx;
		background: rgba(0, 0, 0, .45)
	}
</style>
