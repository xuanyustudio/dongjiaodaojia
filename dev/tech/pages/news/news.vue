<template>
	<view class="news">
		<!-- 头部导航 -->
		<uni-nav-bar statusBar :fixed="true" class="navBar" :height="'88rpx'" :backgroundColor="'#fff'"
			style="font-size: 36rpx;" :color="'#000'" :border="false" title="消息"></uni-nav-bar>
		<!-- 消息列表 -->
		<view class="text-center p-t50 p-b50" v-if="newsList.length<1">
			<image :src="imgUrl+'no_order.png'" mode="widthFix" class="img" style="width:232rpx;"></image>
			<view class="f-s14 co_666 p-t10">暂时没有消息哦~</view>
		</view>
		<view class="char-list" v-if="newsList.length">
			<uni-list>
				<uni-list-item class="listItem" v-for="(item,index) in newsList" :key="item.id">
					<!-- 自定义 header -->
					<view slot="header" class="slot-box">
						<image class="slot-image" :src="imgUrl+item.avatar" mode="widthFix"></image>
					</view>
					
					<!-- 自定义 body -->
					<view slot="body" class="content">
						<view class="title">{{item.title}}</view>
						<view class="note">{{item.content}}</view>
					</view>
					
					<!-- 自定义 footer-->
					<template slot="footer">
						<view class="chat-custom-right">
							<!-- <view v-if="item.badgetext!=='0'" class="chat-custom-right-top">
								<text>{{item.badgetext}}</text>
							</view> -->
							<view class="p-t5"></view>
							<view class="chat-custom-right-bottom">
								<text>{{item.create_time.split(" ")[0].substr(5)}}</text>
							</view>
						</view>
					</template>
				</uni-list-item>
			</uni-list>
		</view>
	</view>
</template>

<script>
	import {getNewList } from "./api.js"
	export default {
		onReady(){
			this.getNewListData()
		},
		components: {},
		onReachBottom (){
			console.log("触底")
		},

		data() {
			return {
				imgUrl: this.$store.state.imgUrl,
				newsList:[]
			}
		},
		methods: {
			//获取消息列表
			getNewListData(){
				uni.showLoading({title:'加载中'})
				getNewList().then(res=>{
					if(res.code==1){
						uni.hideLoading()
						res.data.list.forEach(item=>{
							if(item.status =="0"){
								item.badgetext ="1"
							}
							if(item.status =="1"){
								item.badgetext ="0"
								let i = "防守打法"
							}
							if(item.type =="1"){
								item.avatar = "images/newsImg/2.png"
								item.title = "账户通知"
								
							}
							if(item.type =="2"){
								item.avatar = "images/newsImg/1.png"
								item.title = "新订单"
							}
						})
						this.newsList =res.data.list
					}
				})
			},
			navigateTo(e){
				uni.navigateTo({
					url:e
				})
			}
		}
	}
</script>

<style lang="less">
	.news{
		width: 100%;
		height: 100%;
	}
	.listItem {
		width: 750rpx;
		height: 144rpx;
		background: #FFFFFF;
		// 头像
		.slot-box {
			width: 96rpx;
			height: 96rpx;

			image {
				width: 100%;
				height: 100%;
			}
		}

		.content {
			width: 404rpx;
			height: 100%;
			margin-left: 16rpx;

			.title {
				font-size: 32rpx;
				font-family: PingFang SC;
				font-weight: bold;
				color: #262626;
			}

			.note {
				font-size: 28rpx;
				font-family: PingFang SC;
				font-weight: 400;
				color: #666666;
				margin-top: 20rpx;
				text-overflow: ellipsis;
				overflow: hidden;
				white-space: nowrap;

			}
		}
	}

	.chat-custom-right {
		width: 60rpx;
		height: 100%;
		display: flex;
		flex-direction: column;
		justify-content: space-between;
		align-items: center;
		margin-left: 100rpx;


		.chat-custom-right-top {
			width: 32rpx;
			height: 28rpx;
			background: #FF5A5A;
			border-radius: 16rpx;
			text-align: center;
			display: flex;
			justify-content: center;
			align-items: center;
			margin-top: 15rpx;

			text {
				color: #fff;
				font-size: 20rpx;
			}

		}

		.chat-custom-right-bottom {
			width: 300rpx;
			text-align: center;
			font-size: 24rpx;
			color: #999999;
		}
	}
</style>
