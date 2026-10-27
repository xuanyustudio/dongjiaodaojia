<template>
	<view class="flex-col page">
		<view class="flex-col group">
			<view class="flex-col section_1">
				<view class="serxs">
			<view class="text_2" style="    flex-direction: column;
    align-items: center;
    justify-content: space-around;display: flex;height: 70rpx;;font-weight: 600;color: #D30202;">意见反馈<view style="background-color: #D30202;width: 50rpx;
					height: 4rpx; "></view></view>
					<view class="text_2"  @click="toPath"  style="    flex-direction: column;
    align-items: center;
    justify-content: space-around;display: flex;height: 70rpx;;font-weight: 600;color: #D30202;">反馈记录<view style="background-color: #D30202;width: 50rpx;
					height: 4rpx; "></view></view>
				</view>
				<view class="flex-row group_1">
					<view class="flex-col items-center text-wrapper" v-for="(item,index) in typeList"
						:class="{'active':selectValue==index}" @click="choice(index)">
						<text>{{ item.name }}</text>
					</view>


				</view>
			</view>	
			<view class="flex-col section_2">
				<view class="flex-col">
					<text class="text_7" style="font-weight: 600;">问题说明</text>
					<text class="text_8">
						<u--textarea v-model="content" placeholder="请输入15字以上的描述，我们才能更好给您提供帮助"  count border="none"></u--textarea>
					</text>
				</view>
				<view class="group_3">
					
					<view class="image" v-for="(item,index) in images" :key="index">
						<image class="image_2" :src="item" mode=""></image>
						<view class="close" @click="del(index)">
							<u-icon name="close-circle-fill" color="red" size="20"></u-icon>
						</view>
					</view>
					<view class="image" @click="upload">
						<image
							src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844659951094858.png"
							class="image_1" />
						<image
							src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844659994357733.png"
							class="image_2" />
					</view>
				</view>
			</view>
			<view class="flex-col section_3">
				<view class="flex-col">
					<text class="text_9">联系方式</text>
					<text class="text_10">
					 <u--input
						type="number"
					    placeholder="留下您的联系方式方便我们联系您哦~"
					    v-model="mobile"
						border="none"
					  ></u--input></text>
				</view>
				<view class="flex-col items-center button" @click="submit">
					<text>提交反馈</text>
				</view>
				
				<!-- <view class="submit" @click="toPath">查看反馈记录</view> -->
			</view>
		</view>
	</view>
</template>

<script>
	import { uploadImage } from '@/config/utils'
	export default {
		data() {
			return {
				selectValue: 0,
				content:'',
				mobile:'',
				typeList:[],
				images: [],
				isSubmit:false
			};
		},
		onLoad(e){
			
			this.init();
		},
		methods: {
			upload(){
				uni.chooseImage({
					count:1,
					success: (res) => {
						uni.showLoading({title:'图片上传中'})
						let { tempFilePaths } = res;
						let file = tempFilePaths[0];
						uploadImage(file).then(res =>{
							uni.hideLoading()
							this.images.push(res);
						})
					}
				})
			},
			del(index){
				uni.showModal({
					content:'是否确认删除此图片',
					success: (res) => {
						console.log(res)
						let { confirm } = res;
						if( confirm ){
							this.images.splice(index,1)
						}
					}
				})
			},
			init(){
				// 获取意见反馈类型
				this.$http.post('api/opinion/index').then(res =>{
					console.log(res)
					this.typeList = res;
				})
			},
			choice(e) {
				this.selectValue = e
			},
			toPath(){
				uni.navigateTo({
					url:'/bundle/pages/feedback_record/feedback_record'
				})
			},
			submit(){
				if(this.isSubmit){
					uni.showToast({
						icon:'none',
						title:'请勿频繁提交'
					})
					return;
				}
				
				this.isSubmit = true;
				
				if( !this.content ){
					uni.showToast({
						icon:'none',
						title:'请输入反馈内容'
					})
					return;
				}
				
				if( !this.mobile ){
					uni.showToast({
						icon:'none',
						title:'请输入联系方式'
					})
					return;
				}
				if (!this.$base.phoneRegular.test(this.mobile)) {
					uni.showToast({
						title: '手机号格式不正确',
						icon: 'none'
					});
					return;
				}
				this.$http.post('api/opinion/createOpinion',{
					content: this.content,
					type: this.typeList[this.selectValue].id,
					images: this.images,
				}).then(res =>{
					this.isSubmit = false;
					uni.showToast({
						icon:'success',
						title:"提交成功"
					})
					
					setTimeout(function(){
						uni.navigateBack()
					},1000)
					
				})
			},
		}
	};
</script>

<style scoped lang="scss">
	.serxs{
		color: #D30202;
		font-size: 35rpx;
		font-weight: 600;
		display: flex;
		justify-content: space-around;
	}
	.page {
		background-color: #f8f8f8;
		width: 100%;
		height: 100%;
		overflow-y: auto;

		.group {
			flex: 1 1 auto;
			overflow-y: auto;

			.section_1 {
				padding: 42rpx 32rpx 40rpx;
				background-color: rgb(255, 255, 255);

				.text_2 {
					color: rgb(0, 0, 0);
					font-size: 32rpx;
					font-weight: 500;
					line-height: 44rpx;
					white-space: nowrap;
				}

				.group_1 {
					margin-top: 32rpx;

					.text-wrapper {
						padding: 10rpx;
						width: 200rpx;
						margin-right: 10rpx;

						font-size: 28rpx;
						line-height: 40rpx;
						white-space: nowrap;

						border-radius: 12rpx;
						height: 64rpx;

						color: rgb(153, 153, 153);
						background-color: rgb(244, 244, 244);

						border:none;
						 &.active {
							color: rgb(24, 181, 103);
							border: solid 2rpx rgb(24, 181, 103);
							background-color: rgba(24, 181, 103, 0.18);
						}
					}

					.text-wrapper_1 {
						margin-left: 25rpx;
						flex: 1 1 154rpx;
						color: rgb(153, 153, 153);
						font-size: 28rpx;
						line-height: 40rpx;
						white-space: nowrap;
						padding: 12rpx 0;
						background-color: rgb(244, 244, 244);
						border-radius: 12rpx;
						height: 64rpx;
					}

					.view_1 {
						margin-left: 23rpx;
					}
				}
			}

			.section_2 {
				padding: 40rpx 32rpx;
				background-color: rgb(255, 255, 255);
				
				.group_3 {
					padding: 78rpx 0;
					position: relative;
					width: 100%;
					display: flex;
					flex-wrap: wrap;
					.image{
						position: relative;
						width: 212rpx;
						height: 212rpx;
						flex-shrink: 0;
						display: flex;
						align-items: center;
						justify-content: center;
						margin: 0 30rpx 20rpx 0;
						.close{
							width: 40rpx;
							height: 40rpx;
							position: absolute;
							top: -20rpx;
							right: -20rpx;
						}
					}
					.image_1 {
						width: 56rpx;
						height: 56rpx;
					}

					.image_2 {
						border-radius: 24rpx;
						width: 212rpx;
						height: 212rpx;
						position: absolute;
						left: 0;
						bottom: 0;
					}
				}

				.text_7 {
					color: rgb(0, 0, 0);
					font-size: 32rpx;
					font-weight: 500;
					line-height: 44rpx;
					white-space: nowrap;
				}

				.text_8 {
					margin-top: 28rpx;
					color: rgb(153, 153, 153);
					font-size: 28rpx;
					line-height: 40rpx;
					white-space: nowrap;
				}
			}

			.section_3 {
				padding: 40rpx 32rpx 100rpx;
				background-color: rgb(255, 255, 255);

				.button {
					margin-top: 84rpx;
					padding: 26rpx 0;
					color: rgb(255, 255, 255);
					font-size: 32rpx;
					font-weight: 500;
					line-height: 44rpx;
					white-space: nowrap;
					background-color: rgb(24, 181, 103);
					border-radius: 48rpx;
				}

				.text_9 {
					color: rgb(0, 0, 0);
					font-size: 32rpx;
					font-weight: 500;
					line-height: 44rpx;
					white-space: nowrap;
				}

				.text_10 {
					margin-top: 28rpx;
					color: rgb(153, 153, 153);
					font-size: 28rpx;
					line-height: 40rpx;
					white-space: nowrap;
				}
			}
		}
		.submit{
			text-align: center;
			color: rgba(70, 209, 126, 1);
			font-size: 28rpx;
			padding: 40rpx 0;
		}
	}
</style>
