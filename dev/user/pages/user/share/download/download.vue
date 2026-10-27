<template>
	<view>
		<u-swiper :list="list1" height="718"></u-swiper>
		<div class="btn-img" @click="down">
			<img src="https://yiyanyuese.oss-cn-beijing.aliyuncs.com/images/H5/btn.png" width="100%" height="auto">

		</div>
		<u-overlay :show="weixin">
			<view class="warp">
				<view class="rect">
					<img src="https://yiyanyuese.oss-cn-beijing.aliyuncs.com/images/H5/WechatIMG987.png" width="70%"
						height="auto">
				</view>
			</view>
		</u-overlay>
	</view>

</template>

<script>
	export default {
		data() {
			return {
				list1: [
					'https://app.sclhyg.com/public/images/app-1.jpg',
					'https://app.sclhyg.com/public/images/app-2.jpg',
					'https://app.sclhyg.com/public/images/app-3.jpg',
				],
				weixin: false
			}
		},
		methods: {
			//判断是否微信登陆
			isWeiXinLogin() {
				var ua = window.navigator.userAgent.toLowerCase();
				if (ua.match(/MicroMessenger/i) == 'micromessenger') {
					return true; // 微信中打开
				} else {
					return false; // 普通浏览器中打开
				}
			},
			//下载
			down() {
				// 判断手机平台是 Android 还是 IOS
				var downloadUrl = 'https://www.pgyer.com/ZdLH'; // 下载地址
				
				if (uni.getSystemInfoSync().platform === 'android') {
					// alert('运行Android上')
				} else {
					var downloadUrl = 'https://www.pgyer.com/ZdLH'; // 下载地址
					
				}
				var urlStr = encodeURI(downloadUrl)
				// #ifdef APP-PLUS
				plus.runtime.openURL(urlStr);
				return;
				// #endif
				// #ifdef H5 
				window.open(urlStr);
				// #endif
			}
		},
		onShow() {
			this.weixin = this.isWeiXinLogin()

		}
	}
</script>

<style>
	.btn-img {
		position: fixed;
		bottom: 0;
		left: 0;
		z-index: 1;
		width: 100%;
	}

	.warp {
		display: flex;

		justify-content: center;
		height: 100%;
	}

	.rect {
		margin-top: 50rpx;
		height: 200rpx;
		display: flex;
		justify-content: center;
	}

	.rect img {}
</style>
