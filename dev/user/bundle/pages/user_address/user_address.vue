<template>
	<view class="flex-col page">
		<view class="flex-col group">
			<view class="flex-col">
				<view class="list-item flex-row" :key="i" v-for="(item, i) in list" @click="toPath(item)">
					<image
						src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844650858427321.png"
						class="image_1" />
					<view class="right-group flex-col">
						<view class="top-group justify-between">
							<view class="flex-row">
								<text class="text_1">{{ item.realname }}</text>
								<text class="text_3">{{ item.mobile }}</text>
								<view class="flex-col items-center text-wrapper" v-if="item.is_default">
									<text>默认</text>
								</view>
							</view>
							<image v-if="!type"
								src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844651421378871.png"
								class="image_3" />
						</view>
						<text
							class="text_6">{{ item.province }}{{ item.city }}{{ item.district }}{{ item.address }}</text>
					</view>
				</view>
			</view>

			<view class="no" v-if="!list.length">
				<zNo></zNo>
			</view>

			<view class="justify-center button" @click="add">
				<u-icon name="plus" color="#FFFFFF"></u-icon>
				<text class="text_14">添加服务地址</text>
			</view>
		</view>
	</view>
</template>

<script>
	// 后台没有数据就引入xer.vue  ，后台接口给出数据就引入index.vue
	// import zNo from '@/components/z-no/index.vue'
	import zNo from '@/components/z-no/xer.vue'
	export default {
		data() {
			return {
				type: '',
				list: []
			};
		},
		onLoad(options) {
			this.type = options.type ? options.type : '';

		},
		onShow() {
			this.init() ;
			uni.$on('change', () => {
				this.init();
			})
		},
		components: {
			zNo
		},
		onUnload() {
			uni.$off('change')
		},
		methods: {
			init() {
				this.$http.get('api/address/index').then(res => {
					this.list = res;
				})
			},
			add() {
				uni.navigateTo({
					url: "/bundle/pages/address_edit/address_edit"
				})
			},
			toPath(item) {
				// :url="'/bundle/pages/address_edit/address_edit?id='+ item.id"
				if (this.type) {
					uni.setStorageSync('address_info', item);
					uni.navigateBack({
						delta: 1
					})
					return;
				}
				uni.navigateTo({
					url: '/bundle/pages/address_edit/address_edit?id=' + item.id
				})
			}
		}
	};
</script>

<style scoped lang="scss">
	page {
		background-color: #ffffff;
		width: 100%;
		height: 100%;

		.no {
			position: fixed;
			width: 100%;
			height: 100%;
			top: 0;
			left: 0;
			z-index: 8;
			background-color: white;
			display: flex;
			align-items: center;
			justify-content: center;
		}

		.group {
			padding: 40rpx 32rpx 100rpx;
			flex: 1 1 auto;


			.button {
				position: fixed;
				bottom: 80rpx;
				left: 32rpx;
				width: calc(100% - 64rpx);
				padding: 26rpx 0;
				color: rgb(255, 255, 255);
				font-size: 32rpx;
				font-weight: 500;
				line-height: 44rpx;
				white-space: nowrap;
				background-color: rgb(24, 181, 103);
				border-radius: 48rpx;
				z-index: 99;

				.image_9 {
					width: 40rpx;
					height: 40rpx;
				}

				.text_14 {
					margin-left: 8rpx;
				}
			}

			.list-item {
				&:not(:first-of-type) {
					margin-top: 82rpx;
				}

				.image_1 {
					width: 92rpx;
					height: 92rpx;
				}

				.right-group {
					margin-left: 20rpx;
					flex: 1 1 auto;

					.top-group {
						padding-left: 4rpx;

						.image_3 {
							width: 44rpx;
							height: 44rpx;
						}

						.text_1 {
							color: rgb(0, 0, 0);
							font-size: 32rpx;
							font-weight: 500;
							line-height: 44rpx;
							white-space: nowrap;
						}

						.text_3 {
							margin-left: 16rpx;
							margin-top: 4rpx;
							color: rgb(153, 153, 153);
							font-size: 28rpx;
							font-weight: 500;
							line-height: 40rpx;
							white-space: nowrap;
						}

						.text-wrapper {
							margin: 8rpx 0 4rpx 26rpx;
							padding: 2rpx 0;
							color: rgb(24, 181, 103);
							font-size: 20rpx;
							line-height: 28rpx;
							white-space: nowrap;
							background-color: rgba(24, 181, 103, 0.24);
							border-radius: 8rpx;
							width: 56rpx;
							height: 32rpx;
						}
					}

					.text_6 {
						width: 80%;

						margin-top: 8rpx;
						color: rgb(102, 102, 102);
						font-size: 24rpx;
						line-height: 40rpx;

					}
				}
			}
		}
	}
</style>
