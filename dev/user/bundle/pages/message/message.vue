<template>
	<view class="flex-col page">
		<view class="flex-col group">
			<view class="justify-between" v-for="(item,index) in dataList" :key="index">
				<view class="flex-row view">
					<image
						:src="item.type == 1 ? 'https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844657083165810.png':'https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844658690718219.png'"
						class="image_1" />
					<view class="flex-col group_3">
						<text class="text_1">{{ item.type == 1 ? '系统消息' : '订单消息' }}</text>
						<text class="text_2">{{ item.content }}</text>
					</view>
				</view>
				<view class="flex-col items-center text-wrapper" v-if="false">
					<text>1</text>
				</view>
			</view>
		</view>
		
		<view class="no" v-if="total === 0">
			<zNo></zNo>
		</view>
	</view>
</template>

<script>
	import zNo from '@/components/z-no/index.vue'
	export default {
		data() {
			return {
				page: 1,
				total: null,
				dataList: []
			};
		},
		onLoad(){
			this.init();
		},
		onReachBottom(){
			if( this.total === this.dataList.length ){
				return;
			}
			this.page++;
			this.init()
		},
		components:{
			zNo
		},
		methods:{
			init(){
				this.$http.post('api/news/index',{
					page: this.page
				}).then(res =>{
					let { list, total } = res;
					this.dataList.push(...list);
					this.total = total;
				})
			}
		}
	};
</script>

<style scoped lang="scss">
	.page {
		background-color: #ffffff;
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
			padding: 38rpx 32rpx 1146rpx;
			flex: 1 1 auto;
			overflow-y: auto;
			.view{
				padding: 20rpx 0;
			}
			.group_4 {
				margin-top: 72rpx;

				.group_5 {
					margin: 6rpx 0 4rpx 24rpx;

					.text_4 {
						color: rgb(0, 0, 0);
						font-size: 32rpx;
						font-weight: 500;
						line-height: 44rpx;
						white-space: nowrap;
					}

					.text_5 {
						margin-top: 8rpx;
						color: rgb(102, 102, 102);
						font-size: 24rpx;
						line-height: 34rpx;
						white-space: nowrap;
					}
				}
			}

			.text-wrapper {
				margin-top: 12rpx;
				padding: 2rpx 0;
				color: rgb(255, 255, 255);
				font-size: 20rpx;
				line-height: 28rpx;
				white-space: nowrap;
				background-color: rgb(255, 82, 82);
				border-radius: 16rpx;
				width: 36rpx;
				height: 32rpx;
			}

			.image_1 {
				width: 96rpx;
				height: 96rpx;
			}

			.group_3 {
				margin: 6rpx 0 4rpx 24rpx;

				.text_1 {
					color: rgb(0, 0, 0);
					font-size: 32rpx;
					font-weight: 500;
					line-height: 44rpx;
					white-space: nowrap;
				}

				.text_2 {
					margin-top: 8rpx;
					color: rgb(102, 102, 102);
					font-size: 24rpx;
					line-height: 34rpx;
					white-space: nowrap;
				}
			}
		}
	}
</style>
