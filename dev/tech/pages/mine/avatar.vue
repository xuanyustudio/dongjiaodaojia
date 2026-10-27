<template>
	<view class="">
		<!-- 头部绿色背景 -->
	<!-- 	<view class="header-bg">
			<image :src="imgUrl+'images/indexImg/slices_one/headerbg.png'" mode="widthFix"></image>
		</view> -->
	<view class="header-bg">
			<image mode=""></image>
		</view>
		<uni-nav-bar statusBar class="navBar" left-icon="left" :height="'88rpx'" :backgroundColor="'none'"
			:fontSize="'36rpx'" :color="'#F4F4F4'" :border="false" title="设置头像" @clickLeft="back"></uni-nav-bar>
		<view class="dd-flex p-t20 p-b50">
			<view class="position-relative">
				<view class="border-radius-100" style="border:8rpx solid #fff;overflow: hidden;">
					<!-- 修改的地方 -->
					<image :src="img" mode="aspectFill" class="img" style="width:200rpx;height:200rpx;"></image>
					<view class="position-absolute" style="bottom:0;right:0;" @tap="choiceImg(0)"><image :src="imgUrl+'images/mineImg/img01.png'" mode="" class="img" style="width:74rpx;height:74rpx;"></image></view>
				</view>
			</view>
		</view>
		<view class="flex1 bg_fff position-relative d-flex flex-direction-column">
			<!-- 综合评分 -->
			<view class="flex1 bg_fff d-flex flex-direction-column" style="border-radius:40rpx;margin-top:-32rpx;">
				<view class="p-15">设置本人相册</view>
				<view class="flex1 p-l15 p-r15 p-b15">
					<view class="d-flex flex-wrap">
						<view class="item" v-for="(item,i) in list" :key="i">
							<image :src="item" mode="aspectFill" class="img"></image>
							<view class="del" @tap="del(i)" v-if="zt!=0">删除</view>
						</view>
						<view class="item item2 dd-flex" @tap="choiceImg(1)">
							<image :src="imgUrl+'images/mineImg/img02.png'" mode="" class="img" style="width:56rpx;height:56rpx;"></image>
						</view>
					</view>
					<!-- <view class="d-flex p-t5"><button type="default" class="p-0 f-s14 co_666" style="background:none;">查看上传要求 >></button></view> -->
				</view>
			</view>
		</view>
		<view class="tool-fixed bg_fff p-15" style="box-shadow:0 -8rpx 16px rgba(0, 0, 0, .06);background-color: #0ea17d;">
			<!-- <view  class="regiter-bottom border-radius-6" v-if="zt==2">审核通过</view> -->
			<!-- <view  class="regiter-bottom border-radius-6" v-if="zt == 0">认证审核中</view> -->
			<!-- <view  style="background-color: #0ea17d;border-radius: 50rpx;"  class="ser" v-else @tap="submit">提交</view> -->
			
			<view class="ser" style="background-color: #0ea17d;border-radius: 50rpx;" @click="submit" v-if="zt==null || zt==2">提交</view>
			
			
		</view>
		<view class="b-liuhai" style="height:148rpx;"></view>
	</view>
</template>

<script>
	import {setAlbum} from "./api.js"
	export default {
		components: {},
		data() {
			return {
				imgUrl: this.$store.state.imgUrl,
				zt: null, //状态
				img:"",
				index:null,
				list: [],
				list2: [] // 单次上传数量存放
			}
		},
		onLoad() {
			this.init()
			
		},
		onShow() {
			//获取技师 轮播图 和通知
			let token = uni.getStorageSync('token')
			if(this.zt !== 0){
				this.zt = 2
			}
			console.log(this.zt,"zt")
			console.log(this.list,"list")
			console.log(this.list2,"list2")
			// if( token ) this.getIndexData()
		},
		methods: {
			albumImages(raw) {
				if (Array.isArray(raw)) return raw.filter(Boolean)
				const text = raw == null ? '' : String(raw).trim()
				if (!text) return []
				if (text.startsWith('[')) {
					try {
						const parsed = JSON.parse(text)
						if (Array.isArray(parsed)) return parsed.filter(Boolean)
					} catch (e) {}
				}
				return [text]
			},
			init(){
				uni.showLoading({title:'提交中'})
				this.$db.http('my.Index').then(res => {
					console.log(res,"打印成功回调")
					if(res.code==1){	
						uni.hideLoading()
						let id = res.data.technician_album	
						if(id){
							this.img = id.avatar
							console.log(id.album,"打印的id.album")
							this.list = this.albumImages(id.album)
							this.zt = id.status
						}else{
							this.img = this.imgUrl+'images/indexImg/slices_two/touxiang.png'
						}
					}
				})
			},
			//选择图片
			choiceImg(is) {
				if(this.zt==0){ return}
				let _this = this
				uni.chooseImage({
					success: function(res) {
						console.log(JSON.stringify(res.tempFilePaths),"头像打印");
						uni.showLoading({title:'图片上传中'})
						let a = res.tempFilePaths //选择图片路径
						for(let i in a){
							_this.uploadImg(a[i],a.length,is)
							// _this.uploadImg(a[i],is)
						}
					}
				})
			},
			//上传图片
			uploadImg(e,num,is) {
				let _this = this
				let token = uni.getStorageSync('token')
				uni.uploadFile({
					url: _this.$cfg['httpUrl']+'index/uploadImage',
					filePath: e,
					name: 'file',
					header:{
						token:_this.$db.get('token')
					},
					success: (res) => {
						// console.log(res,"请求接口打的印")
						let _data = JSON.parse(res.data);
						// console.log(_data,"打印的接口数据")
						if(_data.code==1){
							console.log(1,is,num)
							if(is){
								
								// 相册
								_this.list = _this.list.concat(_data.data)
								console.log(_this.list,"相册的值")
								_this.list2.push(_data.data)
								
								if(_this.list2.length == num){
									_this.list2 = []
									uni.hideLoading()
								}
							}else{
								// 头像
								_this.img = _data.data
								console.log(_this.img,"打印的this.img")
								uni.hideLoading()
							}
						}
					}
				});
			},
			del(e){
				this.list.splice(e, 1);
			},
			submit(){
				if(!this.list.length){uni.showToast({title:'相册中还没有头像',icon:'none'});return}
				let datas = {
					avatar: this.img, // 头像
					album: this.list  // 相册
				}
				console.log('提交的json',datas)
				uni.showLoading({title:'提交中'})
				
				this.$db.http('my.Index/setAlbum',datas).then(res => {
					console.log('提交结果',res)
					if(res.code==1){
						this.zt = 2			
						
						setTimeout(()=>{
							uni.showToast({title:'设置成功'})
						uni.navigateBack()
						},3000)
					}
				})
			},
			back() {
				// uni.navigateBack()
			}
		}
	}
</script>

<style lang="less">
	page {
		background: #fff;
	}

	// 头部绿色背景
	.header-bg {
		position: absolute;
		left: 0;
		top: 0;
		right: 0;
		height: 680rpx;
		background:#0ea17d ;

		image {
			width: 100%;
		}
	}

	.item {
		position: relative;
		width: 31%;
		margin-right: 3%;
		margin-bottom: 3%;
		border-radius: 16rpx;
		overflow: hidden;

		&:nth-child(3n) {
			margin-right: 0;
		}

		image {
			width: 100%;
			height: 256rpx;
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
			background: rgba(0, 0, 0, 0.45)
		}
	}

	.item2 {
		border: 1px dashed #979797;
		box-sizing: border-box;
		height: 256rpx;
	}
	.ser{
		height: 80rpx;
		color: white;
		line-height: 80rpx;
		display: flex;
		align-items: center;
		justify-content: space-evenly;
	}
</style>
