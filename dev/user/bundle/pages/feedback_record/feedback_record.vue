<template>
	<view class="flex-col page">
		<view class="flex-col group">
			
			<view class="flex-col section_3" v-for="(item,index) in dataList" :key="index">
				<view class="flex-col section_4">
					<view class="top-group justify-between">
						<text class="text_1">时间：{{ item.create_time }}</text>
						<text :class=" item.reply ? 'text_6' : 'text_3' ">{{ item.reply ? '已回复' : '未回复' }}</text>
					</view>
					<view class="divider"> </view>
					<text class="text_7">问题：{{ item.content }}</text>
					<view class="flex-row equal-division_1">
						<image
						v-for="(a,b) in item.images"
							:src="a"
							class="equal-division-item" />
					</view>
				</view>
				<text class="text_8" v-if="item.reply">{{ item.reply }}</text>
			</view>
			
			<view class="no" v-if="total === 0">
				<zNo></zNo>
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
				dataList: [],
				page: 1,
				total: null,
			};
		},
		onLoad(){
			this.init();
		},
		onReachBottom() {
			if( this.total === this.dataList.length ){
				return;
			}
			this.page++;
			this.init();
		},
		components:{
			zNo
		},
		methods:{
			init(){
				this.$http.post('api/opinion/opinionList',{
					page: this.page
				}).then(res =>{
					console.log(res)
					let { list, total } = res;
					this.dataList.push(...list);
					this.total = total;
				})
			},
		}
	};
</script>

<style scoped lang="scss">
	.page {
		background-color: #f8f8f8;
		width: 100%;
		height: 100%;
		overflow-y: auto;
		.no{
			position: fixed;
			width: 100%;
			height: 100%;
			top: 0;
			left: 0;
			display: flex;
			align-items: center;
			justify-content: center;
		}
		.group {
			padding-bottom: 544rpx;
			flex: 1 1 auto;
			overflow-y: auto;

			.group_1 {
				padding-bottom: 396rpx;
				position: relative;

				.section_1 {
					background-image: linear-gradient(180deg,
							rgb(255, 255, 255) 0%,
							rgb(255, 255, 255) 0%,
							rgb(248, 248, 248) 100%,
							rgb(248, 248, 248) 100%);
					height: 48rpx;
				}

				.section_2 {
					padding: 0 24rpx 24rpx;
					background-color: rgb(255, 255, 255);
					border-radius: 24rpx;
					position: absolute;
					left: 32rpx;
					right: 32rpx;
					top: 50%;
					transform: translateY(-50%);

					.text_4 {
						margin-top: 24rpx;
						color: rgb(0, 0, 0);
						font-size: 28rpx;
						line-height: 40rpx;
						white-space: nowrap;
					}

					.equal-division {
						margin-top: 24rpx;

						.image_2 {
							margin-left: 26rpx;
						}

						.image_3 {
							margin-left: 24rpx;
						}
					}
				}
			}

			.section_3 {
				margin: 0 32rpx;
				padding-bottom: 16rpx;
				background-color: rgba(24, 181, 103, 0.12);
				border-radius: 24rpx;

				.section_4 {
					padding: 0 24rpx 24rpx;
					background-color: rgb(255, 255, 255);
					border-radius: 24rpx;

					.text_7 {
						margin-top: 24rpx;
						color: rgb(0, 0, 0);
						font-size: 28rpx;
						line-height: 40rpx;
						white-space: nowrap;
					}

					.equal-division_1 {
						margin-top: 24rpx;
						
						.image_5 {
							margin-left: 26rpx;
						}

						.image_6 {
							margin-left: 24rpx;
						}
					}
				}

				.text_8 {
					margin-left: 24rpx;
					margin-top: 14rpx;
					color: rgb(24, 181, 103);
					font-size: 24rpx;
					line-height: 34rpx;
					white-space: nowrap;
				}
			}

			.top-group {
				padding: 24rpx 0 22rpx;

				.text_1 {
					color: rgb(102, 102, 102);
					font-size: 28rpx;
					line-height: 40rpx;
					white-space: nowrap;
				}

				.text_3 {
					color: rgb(243, 151, 53);
					font-size: 28rpx;
					line-height: 40rpx;
					white-space: nowrap;
				}

				.text_6 {
					color: rgb(24, 181, 103);
					font-size: 28rpx;
					line-height: 40rpx;
					white-space: nowrap;
				}
			}

			.divider {
				margin-left: 4rpx;
				background-image: repeating-linear-gradient(90deg,
						rgb(228, 228, 228),
						rgb(228, 228, 228) 1.2618296529968454%,
						transparent 1.2618296529968454%,
						transparent 2.5236593059936907%);
				height: 2rpx;
			}

			.equal-division-item {
				flex-shrink: 0;
				border-radius: 24rpx;
				width: 196rpx;
				height: 196rpx;
				margin-right: 24rpx;
			}
		}
	}
</style>
