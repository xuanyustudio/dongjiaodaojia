<template>
	<view class="flex-col page">
		<view class="no" v-if="total === 0">
			<zNo toast="暂无收藏记录~"></zNo>
		</view>
		<view class="flex-col group">
			<view class="section_1"> </view>
			<view class="flex-col list">
				<view class="list-item " :key="i" v-for="(item, i) in dataList" @click="toPath(item)">
					<view class="left-section flex-col items-center" :style="{'backgroundImage': `url(${item.avatar})`}">
						<image
							v-if="item.new"
							src="../../../static/my/tnew.png"
							class="image_1" />
						<view class="flex-col items-center text-wrapper">
							<text>核酸正常</text>
						</view>
					</view>
					<view class="center-group flex-col">
						<view class="group_1 flex-row">
							<text class="text_2 text_3">{{ item.realname }}</text>
							<image
								src="../../../static/my/auth.png"
								class="image_3" />
						</view>
						<view class="group_2 flex-row">
							<u-icon v-for="(sitem,sindex) in parseInt(item.praise)" :key="sindex" name="star-fill" size="12" color="rgba(243, 151, 53, 1)"></u-icon>
							<text class="text_4">好评{{ item.praise }}分</text>
						</view>
						<view class="group_3 flex-row">
							<view class="left-text-wrapper flex-col" v-if="item.label">
								<text class="text_6">{{ item.label }}</text>
							</view>
							<view class="right-text-wrapper flex-col items-center">
								<text>{{ item.practice }}年经验</text>
							</view>
						</view>
						<view class="group_4 flex-row">
							<image
								v-if="false"
								src=""
								class="image_9" />
							<text v-if="false" class="text_9 text_10">浅墨养生</text>
							<image
								src="../../../static/my/pl.png"
								class="image_11" />
							<text class="text_11 text_12">{{ item.comment }}评论</text>
							<image
								src="../../../static/my/dang.png"
								class="image_13" />
							<text class="text_13 text_14">{{ item.sales }}单</text>
						</view>
					</view>
					<view class="right-group flex-col">
						<view class="flex-col">
							<view class="top-text-wrapper flex-col items-center">
								<text>可约{{ item.appointment_time }}</text>
							</view>
							<text class="text_16">距离{{ item.distance }}km</text>
						</view> 
						<view class="bottom-text-wrapper flex-col items-center">
							<text>立即预约</text>
						</view>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
// 后台有数据就引入xer.vue  ，后台米有数据就引入index.vue
	// import zNo from '@/components/z-no/index.vue'
	import zNo from '@/components/z-no/xer.vue'
	export default {
		data() {
			return {
				dataList: [],
				latitude: '',
				longitude: '',
				page: 1,
				total: null,
			};
		},
		components:{
			zNo
		},
		onLoad(){
			// uni.showLoading({title: '加载中',mask:true});
			uni.getLocation({
				altitude: false,
				geocode: true,
				// type: 'gcj02',
				success: (res) => {
					let { latitude, longitude } = res;
					this.latitude = latitude;
					this.longitude = longitude;
					this.init();
				}
			})
		},
		onReachBottom(){
			if( this.dataList.length === this.total ){
				return;
			}
			this.page++;
			this.init();
		},
		methods:{
			init(){
				// 我的收藏列表
				this.$http.post('api/collect/index',{
					lat: this.latitude,
					lng: this.longitude,
					page: this.page
				}).then(res =>{
					uni.hideLoading()
					let { list ,total } = res;
					this.total = total;
					this.dataList.push(...list);
					
				})
			},
			toPath(item){
				console.log(item)
				let url = `/bundle/pages/home/home?id=${item.technician_id}`;
				uni.navigateTo({
					url,
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
		
		.no{
			
			width: 100%;
			height: 100%;
			top: 0;
			left: 0;
			background-color: white;
			display: flex;
			align-items: center;
			justify-content: space-around;
		}
		.group {
			

			.section_1 {
				background-image: linear-gradient(180deg,
						rgb(255, 255, 255) 0%,
						rgb(255, 255, 255) 0%,
						rgb(248, 248, 248) 100%,
						rgb(248, 248, 248) 100%);
				height: 48rpx;
			}

			.list {
				
				
				.list-item {
					padding: 34rpx  30rpx;
					background-color: rgb(255, 255, 255);
					border-radius: 24rpx;
					display: flex;
					//justify-content: space-around;
					

					.left-section {
						padding-top: 140rpx;
						color: rgb(255, 255, 255);
						font-size: 24rpx;
						line-height: 34rpx;
						white-space: nowrap;
						border-radius: 50%;
						// background-image: url('https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844638060014119.png');
						background-position: 0px 0px;
						background-size: 100% 100%;
						background-repeat: no-repeat;
						width: 176rpx;
						height: 176rpx;
						position: relative;

						.image_1 {
							width: 80rpx;
							height: 32rpx;
							position: absolute;
							right: 0;
							top: 0;
						}

						.text-wrapper {
							padding-bottom: 2rpx;
							background-color: rgb(243, 151, 53);
							border-radius: 18rpx;
							width: 128rpx;
							position: relative;
						}
					}

					.center-group {
						margin-left: 16rpx;
						flex: 30%; 
						.group_1 {
							color: rgb(0, 0, 0);
							font-size: 32rpx;
							font-weight: 500;
							line-height: 44rpx;
							white-space: nowrap;

							.text_2 {
								color: rgb(0, 0, 0);
								font-size: 32rpx;
								font-weight: 500;
								line-height: 44rpx;
								white-space: nowrap;
							}

							.text_3 {
								color: initial;
								font-size: initial;
								font-weight: initial;
								line-height: initial;
								white-space: initial;
							}

							.image_3 {
								margin: 4rpx 0 4rpx 8rpx;
								width: 90rpx;
								height: 36rpx;
							}
						}

						.group_2 {
							margin-top: 6rpx;
							color: rgb(153, 153, 153);
							font-size: 20rpx;
							line-height: 28rpx;
							white-space: nowrap;

							.image_5 {
								width: 80rpx;
								height: 24rpx;
							}

							.image_7 {
								margin-left: 4rpx;
								width: 52rpx;
								height: 24rpx;
							}

							.text_4 {
								margin-left: 8rpx;
							}
						}

						.group_3 {
							margin-top: 14rpx;
							color: rgb(243, 151, 53);
							font-size: 24rpx;
							line-height: 34rpx;
							white-space: nowrap;

							.left-text-wrapper {
								padding: 2rpx 0 4rpx;
								background-color: rgba(243, 151, 53, 0.16);
								border-radius: 8rpx;
								height: 40rpx;

								.text_6 {
									margin-left: 12rpx;
									margin-right: 10rpx;
								}
							}

							.right-text-wrapper {
								margin-left: 8rpx;
								padding: 2rpx 0 4rpx;
								background-color: rgba(243, 151, 53, 0.16);
								border-radius: 8rpx;
								width: 118rpx;
								height: 40rpx;
							}
						}

						.group_4 {
							margin-top: 16rpx;
							color: rgb(153, 153, 153);
							font-size: 20rpx;
							line-height: 28rpx;
							white-space: nowrap;

							.image_9 {
								width: 24rpx;
								height: 24rpx;
							}

							.text_9 {
								margin-left: 4rpx;
							}

							.text_10 {
								margin-left: initial;
							}

							.image_11 {
								margin-left: 12rpx;
								width: 24rpx;
								height: 24rpx;
							}

							.text_11 {
								margin-left: 4rpx;
							}

							.text_12 {
								margin-left: initial;
							}

							.image_13 {
								margin-left: 12rpx;
								width: 24rpx;
								height: 24rpx;
							}

							.text_13 {
								margin-left: 6rpx;
							}

							.text_14 {
								margin-left: initial;
							}
						}
					}

					.right-group {
						margin-left: 10rpx;

						.bottom-text-wrapper {
							margin-left: 8rpx;
							margin-top: 48rpx;
							padding: 8rpx 0 6rpx;
							color: rgb(255, 255, 255);
							font-size: 24rpx;
							line-height: 34rpx;
							white-space: nowrap;
							background-image: linear-gradient(116.3deg,
									rgb(53, 219, 160) 0%,
									rgb(53, 219, 160) -13.77%,
									rgb(24, 181, 103) 111.69%,
									rgb(24, 181, 103) 100%);
							border-radius: 24rpx;
							width: 128rpx;
						}

						.top-text-wrapper {
							padding: 4rpx  5rpx;
							color: rgb(24, 181, 103);
							font-size: 24rpx;
							line-height: 34rpx;
							white-space: nowrap;
							background-image: linear-gradient(111.2deg,
									rgba(53, 219, 160, 0.12) 0%,
									rgba(53, 219, 160, 0.12) -13.77%,
									rgba(24, 181, 103, 0.12) 111.69%,
									rgba(24, 181, 103, 0.12) 100%);
							border-radius: 6rpx;
							
						}

						.text_16 {
							margin-top: 10rpx;
							align-self: flex-end;
							color: rgb(153, 153, 153);
							font-size: 20rpx;
							line-height: 28rpx;
							white-space: nowrap;
						}
					}
				}
			}
		}
	}
</style>
