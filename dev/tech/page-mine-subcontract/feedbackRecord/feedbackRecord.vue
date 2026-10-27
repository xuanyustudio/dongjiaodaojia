<template>
	<view class="feedbackRecord">
		<view class="item" v-for="(item,index) in feedbackListData" :key="item.id">
			<view class="item-top">
				<view class="time">
					时间：{{item.create_time}}
				</view>
				<view class="reply">
					{{item.reply==""?'未回复':item.reply}}
				</view>
			</view>
			<view class="item-content">
				<view class="type">
					问题:{{item.content}}
				</view>
				<view class="imgBox">
					<view class="img" v-for="(items,index) in item.images" :key="items">
						<image :src="items" mode=""></image>
					</view>
					<!-- 	<view class="img">
						
					</view>
					<view class="img">
						
					</view>
					<view class="img">
						
					</view> -->

				</view>
			</view>

		</view>

	</view>
</template>

<script>
	import {
		feedbackList
	} from "./api.js"
	export default {
		data() {
			return {
				feedbackListData: [],
				openid: uni.getStorageSync('token'),
				page: 1
			}
		},
		onLoad() {
			this.getFeedbackList()
		},
		onReachBottom() {
			this.page++
			this.getFeedbackList()

		},

		methods: {
			getFeedbackList() {
				let {
					page,
					openid
				} = this.$data
				feedbackList({
					page,
					openid
				}).then(res => {
					this.feedbackListData = [...this.feedbackListData,...res.data.list, ]
					console.log(this.feedbackListData)
				})

			},

		}
	}
</script>

<style lang="less">
	page{
		background: #F8F8F8;
	}
	.feedbackRecord {
		width: 686rpx;
		// height: 396rpx;
		background: #F8F8F8;
		opacity: 1;
		border-radius: 24rpx;
		margin: 0 auto;
		padding-bottom: 50rpx;

		.item {
			box-sizing: border-box;
			padding: 0 25rpx;
			margin-top: 24rpx;
			background: #fff;
			padding-bottom: 24rpx;

			.item-top {
				display: flex;
				justify-content: space-between;
				align-items: center;
				height: 88rpx;
				border-bottom: 2rpx #E4E4E4 dotted;

				.time {
					font-size: 28rpx;
					color: #666666;
				}

				.reply {
					font-size: 28rpx;
					color: #F39735;
				}

			}

			.item-content {
				.type {
					font-size: 28rpx;
					color: #000000;
					// height: 90rpx;
					line-height: 90rpx;
				}

				.imgBox {
					// height: 196rpx;
					display: flex;
					flex-wrap: wrap;
					gap: 20rpx;

					.img {
						width: 196rpx;
						height: 196rpx;
						background: #D8D8D8;
						opacity: 1;
						border-radius: 24rpx;
						image{
							width: 100%;
							height: 100%;
						}
					}

				}
			}
		}
	}
</style>
