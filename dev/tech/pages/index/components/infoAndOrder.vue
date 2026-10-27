<template>
	<view>
	<view class="index-infoAndOrder">
		<!-- 个人信息 -->
		<view class="info flex1">
			<view class="info-header" @click="to(config.showJsInfo?'/page-mine-subcontract/personalInformation/personalInformation':'')">
				<view class="">
					<image :src="imgUrl+'images/indexImg/slices_two/shenfen.png'" mode="" class="img"></image>
					<text>个人信息</text>
				</view>
				<image class="jiantou" :src="imgUrl+'images/indexImg/slices_two/jiantou.png'" mode=""></image>
			</view>
			<view class="serssssss" v-if="technician && technician.nickname" @click="to(config.showJsInfo?'/page-mine-subcontract/personalInformation/personalInformation':'')">
				<view v-if="technician.status==0" class="notText">
					未完成信息认证
				</view>
				<view v-if="technician.status==1" class="notText">
					{{technician.nickname}}
				</view>
				<!-- 未认证 -->
				<view class="notInfo">
					<text v-if="technician.status==0">未认证</text>
					<text v-if="technician.status==1">已认证</text>
					<!-- <text>未资格认证</text> -->
				</view>
				<!-- 头像 -->
				<view class="headPture">
					<!-- <image :src="technician.avatar" mode=""></image> -->
					<image :src="info" mode=""></image>
				</view>
			</view>
			<template v-else>
				<view class="p-30 p-t50 p-b0" >
					<button type="default" style="background-color: #0ca27b; color: white;height: 60rpx;line-height: 60rpx;border-radius: 30rpx;" class="" @tap="to">请登录</button>
				</view>
			</template>
		</view>
		<!-- 三个选项卡 -->
		<view class="nav">
			<view class="nav-item" v-for="(item,index) in navData" :key="item.text" @click="skip(item.path)" v-if="config.showBj?true:index<2">
				<image :src="item.img" mode=""></image>
				<text>{{item.text}}</text>
				<view style="height: 20rpx;"></view>
			</view>
		</view>
	</view>
	<view class="indexinfoAndOrderser">
		<!-- 订单 -->
				<view class="order flex1 "id="sers" >
					
					<view class="order-header"  @click="goOreder">
						<view class="header-left">
							<text>我的订单</text>
							<image class="jiantou" :src="imgUrl+'images/indexImg/slices_two/jiantou.png'" mode=""></image>
						</view>
					</view>
					<view class="sers">
					<view class="order-item" id="ss"  :style="{'background':item.background}" v-for="(item,index) in orderData" :key="item.type" @click="stop(item.url)">
						<view class="imageO">
							<!-- <image class="imageO" :src="imgUrl+item.img" mode=""> -->
							</image>
						</view>
		
						<view :class="['flex1',item.index==0?'order-item-num first':'order-item-num']" id="ber">
							<view class="num">{{item.num}}</view>
							<view class="text">{{item.type}}</view>
							
						</view>
					</view>
		
				</view>
				</view>

	</view>
	</view>
</template>


<script>
	
	export default {
		props:{
			technician:{
				type:Object,
			
			},
			info:{
				type:String,
			},
			config:{
				type:Array,
				default:[]
			}
			
		},
	
		data() {
			return {
				imgUrl: this.$store.state.imgUrl,
				orderData: [{
						img: "images/indexImg/slices_two/xindingdan2.png",
						type: "新订单",
						num: "0",
						background:"#E8FAEF",
						url:"/pages/order/order?title=0"
					},
					{
						img: "images/indexImg/slices_two/jinxingzhong.png",
						type: "进行中",
						num: "0",
						background:"#EFF2FF",
						url:"/pages/order/order?title=2"
					},
					{
						img: "images/indexImg/slices_two/yiwancheng2.png",
						type: "已完成",
						num: "0",
						background:"#FFF7E7",
						url:"/pages/order/order?title=4"
					}
				],
				navData: [
					{
						img: "../../static/index/b.png",
						text: "忙时设置",
						path: "/page-index-subcontract/busyHourSetting/busyHourSetting"
					},
					{
						img: "../../static/index/f.png",
						text: "签到",
						path: "/page-index-subcontract/signIn/signIn"
					},
					
					{
						img: "../../static/index/c.png",
						text: "紧急报警",
						path: "/page-index-subcontract/emergencyHelp/emergencyHelp"
					},
				],
			}
		},
		watch:{
			technician: {
				immediate: true,
				handler(newValue) {
					this.applyOrderCount(newValue)
				}
			}
		},
		methods:{
			applyOrderCount(newValue) {
				if (!newValue) return
				this.orderData.forEach((item) => {
					if (item.type == '新订单') {
						item.num = newValue.new_order == null ? 0 : newValue.new_order
					} else if (item.type == '进行中') {
						item.num = newValue.ongoing_order == null ? 0 : newValue.ongoing_order
					} else if (item.type == '已完成') {
						item.num = newValue.complete_order == null ? 0 : newValue.complete_order
					}
				})
			},
			// 跳转个人信息
			goInfo(){
				uni.navigateTo({
					url:"/page-mine-subcontract/personalInformation/personalInformation"
				})
			},
			//跳转订单
			goOreder(){
				uni.switchTab({
					url:"/pages/order/order"
				})
			},
			to(e){
				let token = this.$db.get('token')
				if(token){
					uni.navigateTo({url:e})
				}else{
					uni.navigateTo({
						url:"/pages/login/login"
					})
				}
			},
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
			},
			stop(url) {
				console.log(url)
				let token = this.$db.get('token')
				if(token){
					uni.reLaunch({
						
						
							url: url
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
	.serssssss{
	
	}
	#ber{
		width: 100%;
		height: 100%;
		display: flex;
		    align-items: center;
		    flex-direction: column;
		    justify-content: center;
			font-size: 40rpx;
	}
	#sers{
		width: 95%;
		margin: 10rpx auto;
		
	}
	#ss{
		width: 200rpx;
		border-radius: 20rpx;
		display: flex;
	}
	.sers{
		width: 100%;
		margin: 20rpx auto;
		height: 200rpx;
		display: flex;
		justify-content: space-between;
	}
	.ser{
		background-color: #666666;
		width: 95%;
		height: 50%;
		margin: 0 auto;
	}
	.indexinfoAndOrderser{
		width: 92%;
				height: 300rpx;
				display: flex;
				justify-content: space-between;
				margin: 28rpx auto ;
				flex-direction: column;
				
				background-color: white;
				border-radius: 20rpx;
	}
	.index-infoAndOrder {
		position: relative;
		width: 96%;
		height:460rpx;
		display: flex;
		justify-content: space-between;
		margin: -80rpx 0 0 0 ;
flex-direction: column;
	
		// 个人信息
		.info {
			// width: 332rpx;
			height: 524rpx;
			background: #FFFFFF;
			border-radius: 10rpx;
			margin-left: 30rpx;
			// margin-right: 26rpx;
			height: 50%;
			.info-header {
				font-size: 28rpx;
				font-family: PingFang SC;
				font-weight: 400;
				line-height: 1;
				color: #262626;
				opacity: 1;
				// width: 282rpx;
				height: 84rpx;
				display: flex;
				justify-content: space-between;
				align-items: center;
				color: #333333;
				margin: 0 26rpx 0 24rpx;
				border-bottom: 2rpx rgba(112,112,112,.08) solid;

				view {
					display: flex;
					// width: 150rpx;
					// height: 30rpx;
					// margin-left: 20rpx;
			

					image {
						width: 32rpx;
						height: 32rpx;
						margin-right: 8rpx;
						
					}
				}

				.jiantou {
					width: 16rpx;
					height: 48rpx;
					// margin-right: 20rpx;

				}
			}

			.notText {
				// width: 224rpx;
				height: 44rpx;
				font-size: 32rpx;
				font-family: PingFang SC;
				font-weight: 500;
				color: #262626;
				opacity: 1;
				margin: 24rpx 26rpx 14rpx 24rpx;
			}

			// 未认证
			.notInfo {
				// width: 100%;
				// height: 34rpx;
				border-radius: 4rpx;
				display: flex;
				align-items: center;
				margin: 50rpx 26rpx 0 12rpx;
				text {
					font-size: 20rpx;
					font-family: PingFang SC;
					font-weight: 300;
					color: #666666;
					background: #FFB83C;
					
					margin-left: 12rpx;
					padding: 4rpx 12rpx;
					border-radius: 8rpx;
				}
			}

			// 头像
			.headPture {
				width: 150rpx;
				height: 150rpx;
				margin-top: -130rpx;
				margin-left:500rpx;
				image {
					width: 100%;
					height: 100%;
					border-radius:50%;
				}

			}
		}

		// 我的订单
		.order {
			width: 332rpx;
			margin-left:24rpx;
			height: 524rpx;
			background: #FFFFFF;
			border-radius: 10rpx;
			margin-right: 30rpx;
			border: 1px solid saddlebrown;

			.order-header {
			font-size: 28rpx;
			font-family: PingFang SC;
			font-weight: 400;
			line-height: 1;
			color: #262626;
			opacity: 1;
			// width: 282rpx;
			height: 84rpx;
			display: flex;
			justify-content: space-between;
			align-items: center;
			color: #333333;
			margin: 0 26rpx 0 24rpx;

				view {
					display: flex;
					align-items: center;
					width: 200rpx;
					height: 30rpx;
					// margin-left: 20rpx;

					
				}

				.jiantou {
					width: 16rpx;
					height: 48rpx;
					

				}
			}

			.order-item {
				// width: 282rpx;
				// height: 128rpx;
				// margin-left: 24rpx;
				// margin-right: 26rpx;
				// padding-left:24rpx;
				// display: flex;
				// border-radius: 6rpx;
				// background: #E3EFFF;
				// align-items: center;
				// margin-top: 12rpx;
				
				.imageO {
					width: 80rpx;
					height: 85rpx;
					// margin-left: 20rpx;
				}

				.order-item-num {
					// width: 90rpx;
					height: 108rpx;
					display: flex;
					flex-direction: column;
					margin-left: 32rpx;
					// text-align: center;
					font-size: 26rpx;

					.text {
						color: #262626;
						margin-top: 14rpx;
					}

					.num {
						color: #262626;
						font-size:32rpx;
						font-weight: bold;

					}
				}
			}
		}
	}
	// 首页操作
	.nav {
	    width: 96%;
	    display: flex;
	     justify-content: space-around;
	    margin: 0 0 0 30rpx;
		background-color: white;
		border-bottom-right-radius:20rpx ;
		border-bottom-left-radius:20rpx ;
		.nav-item {
			// flex: 1;
			display: flex;
			flex-direction: column;
			justify-content: space-around;
			text-align: center;
	
			image {
				width: 75rpx;
				height: 80rpx;	
				margin: 0 auto;
			}
	
			text {
	
				font-size: 24rpx;
				font-family: PingFang SC;
				font-weight: 400;
				line-height: 31rpx;
				color: black;
			}
		}
	}
	.header-left{
		width: 100%;
		height: 40rpx;
		display: flex;
		justify-content: space-between;
		    align-items: center;
			image{
				width: 8px;
				    height: 26px;
				
			}
	}
</style>
