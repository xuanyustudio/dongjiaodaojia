<template>
	<view class="p-15 p-b0">
		<view class="f-s16 f-w700">投诉内容</view>
		<view class="d-flex p-t15 p-b15">
			<textarea v-model="text" class="f-s14" placeholder="请输入15字以上的描述，我们才能更好给您提供帮助" placeholder-class="co_ccc" style="height:160rpx;" />
		</view>
		<view class="imgBox">
			<view class="img2" v-for="(item,index) in imgArr" :key="item">
				<image :src="item" mode="" class="img"></image>
				<view class="del" @tap="del(index)">删除</view>
			</view>
			<view class="dd-flex add" @tap="choiceImg"><image :src="imgUrl+'images/mineImg/img02.png'" mode="" class="img" style="width:56rpx;height:56rpx;"></image></view>
		</view>
		
		<view class="tool-fixed p-15">
			<button type="default" class="regiter-bottom border-radius-100" @tap="submit">提交反馈</button>
			<view class="b-liuhai"></view>
		</view>
		<view class="b-liuhai" style="height:148rpx;"></view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				imgUrl: this.$store.state.imgUrl,
				
				order_sn:'',
				imgArr:[],
				text:''
			}
		},
		onLoad(e) {
			if(e.order_sn) this.order_sn = e.order_sn
		},
		methods: {
			//选择图片
			choiceImg() {
				let that = this
				uni.chooseImage({
					success: function(res) {
						that.imgArr = that.imgArr.concat(res.tempFilePaths)
						that.uploadImg()
					}
				})
			},
			//上传图片
			uploadImg() {
				let data = this.imgArr
				let fileList = []
				for (let i = 0; i < data.length; i++) {
					fileList.push({
						name: "file",
						url: data[i]
					})
				}
			},
			del(e){
				this.imgArr.splice(e, 1);
			},
			submit(){
				if(!this.text){uni.showToast({title:'请填写投诉内容',icon:'none'});return}
				if(this.text.length<15){uni.showToast({title:'投诉内容至少15字',icon:'none'});return}
				if(!this.imgArr.length){uni.showToast({title:'建议至少上传一张图片',icon:'none'});return}
				
				let data = {
					order_id: this.order_id,
					content: this.text,
					images: this.imgArr
				}
				uni.showLoading({title:'提交中'})
				this.$db.http('order/orderDetails',{order_sn:this.order_sn}).then(res => {
					if(res.code==1){
						uni.showToast({title:'提交成功'})
						setTimeout(()=>{
							uni.navigateBack()
						},1000)
					}
				})
			}
		}
	}
</script>

<style lang="less">
	page{ background:#fff;}
	.add{ width:208rpx; height:208rpx; border: 1px dashed #979797; border-radius:16rpx;}
	
	.imgBox {
		display: flex;
		// justify-content: space-between;
		flex-wrap: wrap;
		gap: 15rpx;
		margin: 0;
		padding: 0;
		margin-bottom: 40rpx;
	
		.img2 {
			width: 212rpx;
			height: 212rpx;
			opacity: 1;
			border-radius: 16rpx;
			margin-right:24rpx;
			margin-bottom: 24rpx;
			position: relative;
			overflow: hidden;
			&:nth-child(3n){ margin-right:0;}
			image {width: 100%;height: 100%;border-radius: 16rpx;}
			.del{ position: absolute; left:0;right:0; bottom:0;background:rgba(0,0,0,.6);color:#fff;text-align: center; font-size:24rpx; padding:8rpx 0;}
		}
	}
</style>
