<template>
	<view class="content" >
	<view class="top"></view>
	<view class="banner">
		<dl>
			<dt><image src="@/static/logo/wx.png" mode=""></image></dt>
			<dd>享享到家</dd>
		</dl>
		<view class="img">
			<!-- <image src="../../../static/img/ewm.jpg" mode=""></image> -->
			 <uqrcode v-if="url"  :size="150" ref="uQRCode" :text="url" class="uqrcode" />
		</view>
		<!-- <view class="tgtit">推广链接：<text class="tugurl">{{url}}</text></view> -->
		<view class="sharbuttn">
			<view class="btn" @click="save">保存二维码</view>
			<view class="btn" @click="sharurl">复制推广链接</view>
		</view>
		
		<!-- <button class="btn" @click="share">分享</button> -->
		<!-- <view class="shartitle"><view>分享</view></view>
		<view class="sharapk" @click="share">
			<view><image src="../../../static/img/weact.png"></image></view>
			<view><image src="../../../static/img/shar.png"></image></view>
			<view><image src="../../../static/img/qq.png"></image></view>
		</view>
		
		
		<view class="bottom">
			<ul>
				<li>1、好友识别二维码通过手机号进行注册</li>
				<li>2、也可分享此页面到微信或邀请好友注册</li>
				<li>3、注册完成后您即可得到相应的奖励</li>
			</ul>
		</view> -->
	</view>
	
	</view>
</template>

<script>
	// 引入方法
	import appShare, { closeShare } from '@/uni_modules/zhouWei-APPshare/js_sdk/appShare';
	// import  {closeShare} from '';
	export default {
		data(){
			return {
				providerList:[],				
				sourceLink: '',		
				type:0,
				pid:'',
				url:''
			} 
		},
		onLoad() {
			this.$http.post('api/user/userInfo').then(res => {
				// console.log(res,"打印的地址信息")
				this.pid = res.id
				//http://localhost:8080/#/pages/index/yq
				const origin = typeof window !== 'undefined' ? window.location.origin + window.location.pathname : ''
				this.url = origin + "#/pages/index/yq?pid=" + this.pid
			})
			this.version = plus.runtime.version;
			uni.getProvider({
				service: 'share',
				success: (e) => {
					let data = [];
					for (let i = 0; i < e.provider.length; i++) {
						switch (e.provider[i]) {
							case 'weixin':
								data.push({
									name: '分享到微信好友',
									id: 'weixin'
								})
								data.push({
									name: '分享到微信朋友圈',
									id: 'weixin',
									type: 'WXSenceTimeline'
								})
								break;
							case 'qq':
								data.push({
									name: '分享到QQ',
									id: 'qq'
								})
								break;
							default:
								break;
						}
					}
					this.providerList = data;
				},
				fail: (e) => {
					console.log('获取登录通道失败'+ JSON.stringify(e));
				}
			});
		},
		methods:{
			
			//复制分享链接
			sharurl(){
				uni.setClipboardData({
					data: this.url,
					success: function () {
					
						uni.showModal({
							title: '复制成功',
							content: '内容已复制到粘贴板，可前往其他应用粘贴查看。', 
							showCancel:false,
							success: function(res) {
								if (res.confirm) {											 
									//console.log('用户点击确定');
								} else if (res.cancel) {
									//console.log('用户点击取消');
								}
							}
						});
					}
				});
			},
			//保存图片到相册
			save(){
				this.$refs.uQRCode.save({
				        success: res => {
				          uni.showToast({
				          	title:"保存成功"
				          })
				        }
				      })
				// uni.showActionSheet({
				// 	itemList:['保存图片到相册'],
				// 	success: () => {
				// 		plus.gallery.save('http://pds.jyt123.com/wxtest/logo.png', function() {
				// 			uni.showToast({
				// 				title:'保存成功',
				// 				icon:'none'
				// 			})
				// 		}, function() {
				// 			uni.showToast({
				// 				title:'保存失败，请重试！',
				// 				icon:'none'
				// 			})
				// 		});
				// 	}
				// })
			},
			share(e) {
				let shareData = {
					shareUrl: this.url,
					shareTitle: "享享到家",
					shareContent: "享享到家，是一家以采耳、推拿为主项，个人定制型的o2o平台，本平台提供专业、正规、安全、便捷的上门服务",
					shareImg: "https://yiyanyuese.oss-cn-beijing.aliyuncs.com/images/H5/WechatIMG595.png",
					appId: "wxd0e0881530ee4444", // 默认不传type的时候，必须传appId和appPath才会显示小程序图标
					appPath: "pages/home/home",
					appWebUrl: "https://kemean.com/",
				};
				// 调用
				let shareObj = appShare(shareData, res => {
					console.log("分享成功回调", res);
					// 分享成功后关闭弹窗
					// 第一种关闭弹窗的方式
					closeShare();
				});
			},
			openLink() {
				plus.runtime.openWeb(this.sourceLink)
			}
		}
	}
	
	
</script>

<style>
	.tugurl{
		color: #999;
	}
	.sharbuttn{
		display: flex;
		justify-content: center;
	}
	.shartitle{
		    width: 80%;
			text-align: center;
			margin-left: 10%;
			border-bottom: 1px solid #dddddd;
			position: relative;
			height: 60upx;
	}
	.tgtit{
		text-align: center;
	}
	.shartitle view{
		    height: 50upx;
			line-height: 50upx;
			font-size: 28upx;
			color: #999;
			width: 120upx;
			margin: 32upx auto;
			position: absolute;
			background: #fff;
			left: 50%;
			margin-left: -60upx;
	}
	.sharapk{
	 display: flex;
	 justify-content: center;
	 margin: 20upx auto
	}
	.sharapk view{
		margin: 40upx;
	}
	.sharapk view image{
		height: 80upx;
		width: 80upx;
	}
	.content{
		width: 100%;
		background-color: #ffffff;
	}
	.top{
		width: 100%;
		 height:200rpx;
		/*background: url(http://pds.jyt123.com/wxtest/banner.png) no-repeat ;
		background-size:100% ;
		background-position:center center; */
	}
	.banner{
		width: 100% ;
		background-color: #FFFFFF;
		border-radius: 60upx 60upx 0 0;
		margin-top: -60upx;
		padding-bottom: 100rpx;
	}
	.banner dl{
		margin-top: -80upx;
	}
	.banner dl dt{
		text-align: center;
	}
	.banner dl dt image{
		margin: 0 auto;
		width: 130rpx;
		height: 130rpx;
		border-radius:50% ;
	}
	.banner dl dd{
		text-align: center;
		padding-top: 20rpx;
		color: #797979;
	}
	.img{
		width: 300upx;
		height: 300upx;
		
		margin: 0 auto;
		margin-top: 60upx;
	}
	.uqrcode{
		width: 300rpx;
		height: 300rpx;
	}
	.btn{
		width: 260upx;
		height: 60upx;
		line-height: 60upx;
		margin: 0 auto;
		margin-top: 60upx;
		border-radius: 40upx;
		border: 0;
		font-size: 26upx;
		outline: 0;
		background: #33b17b;
		color: #FFFFFF;
		text-align: center;
	}
	.bottom{
		width: 100%;
		height: 400upx;
	/* 	background: url(../../static/img/beij.png) no-repeat; */
		background-position:left bottom;/* 设置背景图片位置 */
		background-size: 20%;
		color: #797979;
		font-size: 26rpx;
	}
	.bottom ul{
		padding-left: 40upx;
		box-sizing: border-box;
	}
	.bottom ul li{
		width: 100%;
		height: 60upx;
	}
</style>
