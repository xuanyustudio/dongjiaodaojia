<template>
	<!-- 首页头部扫码等 -->
	<view class="nav">
		<view class="nav-item" v-for="(item,index) in navData" :key="item.text" @click="skip(item.path)">
			<image :src="imgUrl+item.img" mode=""></image>
			<text>{{item.text}}</text>
		</view>


	</view>
</template>

<script>
	export default {
		data() {
			return {
				imgUrl: this.$store.state.imgUrl,
				navData: [
					{
						img: "images/indexImg/slices_one/mangshi.png",
						text: "忙时设置",
						path: "/page-index-subcontract/busyHourSetting/busyHourSetting"
					},
					{
						img: "images/indexImg/slices_one/qiandao.png",
						text: "签到",
						path: "/page-index-subcontract/signIn/signIn"
					},
					
					{
						img: "images/indexImg/slices_one/baojing.png",
						text: "紧急报警",
						path: "/page-index-subcontract/emergencyHelp/emergencyHelp"
					},
				],
			}
		},
		methods: {
			skip(path) {
				let token = this.$db.get('token')
				if(token){
					uni.navigateTo({
						url: path
					})
				}else{
					uni.navigateTo({
						url:"/pages/login/login"
					})
				}
			}
		}
	}
</script>

<style lang="less" scoped>
	// 首页操作
	.nav {
		width: 100%;
		// height: 130rpx;
		display: flex;
		justify-content: space-between;
		margin-top: 52rpx;

		.nav-item {
			// flex: 1;
			display: flex;
			flex-direction: column;
			justify-content: space-around;
			text-align: center;

			image {
				width: 120rpx;
				height: 120rpx;
				margin: 0 auto;
			}

			text {

				font-size: 24rpx;
				font-family: PingFang SC;
				font-weight: 400;
				line-height: 31rpx;
				color: #FFFFFF;
			}
		}
	}
</style>
