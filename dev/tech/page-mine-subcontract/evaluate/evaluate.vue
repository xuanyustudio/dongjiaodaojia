<template>
	<!-- 服务评价 -->
	<view class="evaluate">
		<view class="header">
			<!-- <view class="active">服务及时</view>
			<view class="active">服务及时</view>
			<view>服务及时</view>
			<view>服务及时</view>
			<view>服务项目周到</view>
			<view>体验很好</view>
			<view>体验一般</view> -->
			<view :class="index<2 ?'active':''" v-for="(item,index) in commentTypeData" :key="item.id">
				{{item.name}}
			</view>
		</view>
		<view class="types">
			<view :class="currentid =='全部'?'active types-item':'types-item'" @click="changeTypeID('全部')">
				全部
			</view>
			<view :class="item.id ==currentid?'active types-item':'types-item'" @click="changeTypeID(item.id)"
				v-for="(item,index) in commentTypeData" :key="item.id">
				{{item.name}}({{item.count?item.count :0}})
			</view>
		</view>
		<!-- 评价列表 -->
		<view class="evaluateList">

			<view class="item" v-for="(item,index) in commentListData" :key="index">
				<!-- 用户 -->
				<view class="name">
					156****6654
				</view>
				<!-- 评价内容 -->
				<view class="content">
					{{item.content?item.content:'无评价'}}
				</view>
				<!-- 评价图片 -->
				<view class="imgbox">
					<view class="img" v-for="(items,index) in item.images" :key="index">
						<image :src="items" mode=""></image>

					</view>
					<!-- 		<view class="img">
						<image :src="imgUrl+'comprehensiveScoreImg/full.png'" mode=""></image>
					</view>
					<view class="img">
						<image :src="imgUrl+'comprehensiveScoreImg/full.png'" mode=""></image>

					</view> -->
				</view>
				<!-- 满意图标 -->
				<view class="icon">
					<image :src="imgUrl+'pjicon.png'" mode=""></image>
					<text>很满意</text>
				</view>
				<!-- 日期 -->
				<view class="time">
					{{item.create_time}}
				</view>
			</view>
		</view>

	</view>
</template>

<script>
	import {
		getType,
		getList
	} from "./api.js"
	export default {
		data() {
			return {
				imgUrl: this.$store.state.imgUrl,
				commentTypeData: [],
				commentListData: [],
				currentid: '全部',
				openid: uni.getStorageSync('token'),
			}
		},
		onLoad() {
			this.getCommentType()
			this.getCommentList("")
		},
		methods: {
			getCommentType() {
				getType().then(res => {
					console.log(res,'打印的res')
					this.commentTypeData = res.data
				})

			},
			getCommentList(types = '') {
				let openid = this.openid
				getList({
					openid,
					types

				}).then(res => {
					this.commentListData = res.data.list
					console.log(this.commentListData)
					if(types==''){
						this.commentTypeData.forEach(item => {
							item.count = 0
							for (let i = 0; i < this.commentListData.length; i++) {
								if (item.id == this.commentListData[i].types) {
									if (!item.count) {
										item.count = 1
									} else {
										item.count = item.count + 1
									}
						
								}
							}
						
						
						})
					}
					
				})
			},
			changeTypeID(id) {
				
				this.currentid = id
				if (id == "全部") {
					this.getCommentList('')
					return
				}
				this.getCommentList(this.currentid)

			},

		}
	}
</script>

<style lang="less">
	.evaluate {
		width: 100%;
		height: 100%;
		background: #fff;

		.header {
			// height: 200rpx;
			background: #FFFFFF;
			display: flex;
			flex-wrap: wrap;
			gap: 24rpx;
			box-sizing: border-box;
			padding: 24rpx 30rpx;

			view {
				height: 64rpx;
				line-height: 64rpx;
				font-size: 24rpx;
				font-weight: 400;
				color: #999999;
				padding: 0 30rpx 0 28rpx;
				background: #F8F8F8;
				margin: 0;
				border-radius: 8rpx;

			}

			.active {
				color: #46D17E;
				background: #E8FAEF;
			}
		}

		.types {
			width: 750rpx;
			height: 88rpx;
			background: #FFFFFF;
			box-sizing: border-box;
			padding: 0 32rpx;
			display: flex;
			column-gap: 50rpx;
			border-bottom: 2rpx #F8F8F8 solid;
			border-top: 2rpx #F8F8F8 solid;

			.types-item {
				font-size: 24rpx;
				font-weight: 400;
				color: #666666;
				line-height: 88rpx;
			}

			.active {
				color: #46D17E;
			}

		}

		.evaluateList {
			.item {
				width: 100%;
				box-sizing: border-box;
				padding: 32rpx;
				position: relative;
				border-bottom: 2rpx #F8F8F8 solid;

				.name {
					font-size: 28rpx;
					font-weight: 500;
					color: #000000;
				}

				.icon {
					width: 104rpx;
					height: 40rpx;
					position: absolute;
					top: 30rpx;
					left: 224rpx;


					image {
						position: absolute;
						width: 100%;
						height: 100%;
					}

					text {
						position: absolute;
						font-size: 24rpx;
						font-weight: 500;
						color: #FFFFFF;
						left: 13rpx;
						top: 4rpx;
						// left: 50%;
						// top: 50%;
						// transform: translate(-50%,-50%);
					}
				}

				.content {
					font-size: 28rpx;
					font-weight: 400;
					color: #666666;
					margin-top: 16rpx;
				}

				.time {
					position: absolute;
					top: 32rpx;
					right: 32rpx;
					font-size: 24rpx;
					font-weight: 400;
					color: #999999;

				}

				.imgbox {
					display: flex;
					column-gap: 24rpx;
					flex-wrap: wrap;

					.img {
						width: 213rpx;
						height: 213rpx;
						background: #D8D8D8;
						opacity: 1;
						margin-top: 12rpx;
						border-radius: 12rpx;

						image {
							width: 100%;
							height: 100%;
							border-radius: 12rpx;
						}
					}
				}
			}
		}
	}
</style>
