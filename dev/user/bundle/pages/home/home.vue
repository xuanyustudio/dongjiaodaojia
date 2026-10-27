<template>
	<view class="home">
		<u-navbar title="" bgColor="rgba(0, 0, 0, 0.24)" leftIconColor="white" :autoBack="true"></u-navbar>
		<swiper class="swiper" :autoplay="true">
			<swiper-item v-for="(item,index) in technician.album" style="position:relative">
				<view class="swiper-item" @click="showImage(index)">
					<image :src="item" mode="widthFix"></image>
				</view>
			</swiper-item>
		</swiper>


		<view class="header">
			<view style="display: flex; position: relative;">

				<image v-if="technician.hframe==1" style="width: 146rpx;height: 146rpx;position: absolute;z-index: 10;"
					src='@/static/images/header.gif' />
				<!-- v-if="technician.hhot==1" v-if="technician.hrocket==1" -->
				<image style="width: 20px;height: 32px;position: absolute;bottom: 0px;z-index: 10;left:114rpx"
					src='@/static/images/hot.gif' />
				<image style="width: 25px;height: 45px;position: absolute;top: 0px;z-index: 10;left:114rpx"
					src='@/static/images/huojian.gif' />

				<image class="logo" :src="technician.avatar" mode=""></image>
				<view class="right">
					<view class="right-1">
						<text class="name">{{ technician.realname }}</text>
						<image
							src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844638614830234.png"
							mode=""></image>
							<!-- background: linear-gradient(143deg,#35dba0,#18b567); -->
						<u-tag text="更多照片"type="success" size="mini" @click="showImage(0)" bgColor="#35dba0" borderColor="#35dba0" style="margin-left: 20rpx;" shape="circle"></u-tag>
						<text class="num">{{ technician.collect }}人关注</text>
					</view>
					<view class="right-2">
						<!-- ../../../static/images/11@2x.png -->
						<image class="sex"
							:src="technician.sex == 1 ? require('../../../static/images/12.png') : technician.sex == 2 ? require('../../../static/images/11@2x.png') : require('../../../static/images/13.png')"
							mode=""></image>
						<text class="age">{{ technician.birthday }}岁</text>
						<text class="age">{{ technician.province.slice(0,2) }}</text>
						<text class="age">{{ technician.height }}cm</text>
						<view class="btn" @click="changeCollect">{{ collect != 0 ? '取消关注' : '关注' }}</view>
					</view>
				</view>
			</view>
		</view>
		<view class="cont">
			<view>从业{{ technician.practice }}年</view>
			<view>{{ technician.profile }}</view>
		</view>

		<view class="content">
			<view class="content-h1">
				<view v-for="(item,index) in 4" :key="index">
					<image src="../../../static/images/5@2x.png" mode=""></image>
					<text>{{ !index ? '实名认证' : index === 1 ? '头像认证' : index === 2 ? '健康证' : '按摩师证' }}</text>
				</view>
				<image class="r-icon" src="../../../static/images/9@2x.png" mode=""></image>
			</view>
			<view class="content-h2">
				<view class="view">
					<view>{{ technician.credit }}<text>分</text> </view>
					<view>信用分</view>
				</view>
				<view class="view">
					<view>{{ technician.praise }} <text>分</text> </view>
					<view>好评度</view>
				</view>
				<view class="view">
					<view>{{ technician.sales }} <text>次</text> </view>
					<view>服务次数</view>
				</view>
			</view>

			<!-- <view class="content-h3">
				<view v-for="(item,index) in comment" :key="index">
					{{ item.name }}
				</view>
			</view> -->
		</view>



		<view class="content">
			<view class="flex-col section_8">
				<view class="flex-row">
					<text class="text_19" :class="{'active':isTabActive==1}" @click="onClickTab(1)">TA的项目
						<image v-show="isTabActive==1" src="../../../static/images/isTabActive.png" class="image_11" />
					</text>
					<text class="text_19" :class="{'active':isTabActive==2}"
						@click="onClickTab(2)">TA的评论({{comment_count}})
						<image v-show="isTabActive==2" src="../../../static/images/isTabActive.png" class="image_11" />
					</text>
				</view>

				<view v-if="isTabActive==1">
					<!-- background: #eee;padding-top:1 0rpx; -->
					<view class="service" style="">
						<!-- <view class="h1">
							<text>TA的服务</text>
						</view> -->
						<view class="product-wrapper content-p">
							<navigator class="product-item flex-row" :key="i" v-for="(item, i) in service"
								:url="`/pages/details/details?id=${item.id}&tid=${id}`">

								<view class="left-section flex-col">
									<view class="text-wrapper flex-col">
										<u--image :showLoading="true" :src="item.cover" width=" 216rpx" height="182rpx"
											radius="4" :fade="true" duration="450"></u--image>
										<text class="text_6">{{ item.label }}</text>
									</view>
								</view>
								<view class="right-group flex-col">
									<view class="flex-row justify-bw">
										<text class="text_8">{{ item.name }}</text>
										<text class="text_10">销量{{ item.sales }}</text>
									</view>
									<view class="group_7 flex-row">
										<view class="left-text-wrapper flex-col items-center" v-if="item.coupon">
											<text>领券立减</text>
										</view>
										<view class="right-text-wrapper flex-col items-center" v-if="item.discount">
											<text>限时折扣</text>
										</view>
									</view>
									<view class="group_8 flex-row">
										<image
											src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844603777503649.png"
											class="image_5" />
										<text class="text_14">时长{{ item.duration }}分钟</text>
									</view>
									<view class="group_9 flex-row">
										<view class="left-group">
											<text class="text_16">￥</text>
											<!-- <text class="text_18 price-font">{{ (Number(item.price * item.discount)/10).toFixed(2) }}</text> -->
											<text class="text_18 price-font">{{ item.price }}</text>
											</text>
										</view>
										<view class="center-group flex-col">
											<view class="group_10">
												<text class="text_20">￥</text>
												<text class="text_22 price-font1">{{item.original_price}}</text>
											</view>
											<view class="divider"> </view>
										</view>
										<view class="right-text-wrapper_1 flex-col items-center">
											<text>立即下单</text>
										</view>
									</view>
								</view>
							</navigator>
						</view>
					</view>

				</view>

				<view v-else>
					<!-- <view class="flex-col section_11">
						<view class="group_9" :class="total === 0 ? 'group_no':''">
							<view @click="changeComment(b)" :class="comment_index === b ? 'select':'no-select'"
								class="flex-col items-center" v-for="(a,b) in comment" :key="b">
								<text>{{ a.name }}</text>
							</view>
						</view>
					</view> -->
					<view class="content-h3">
						<view @click="changeComment(b)" :class="comment_index === b ? 'select':'no-select'"
							v-for="(a,b) in comment" :key="b">
							{{ a.name }}
						</view>
					</view>
					<view class="flex-col section_12" v-for="(item,index) in list" :key="index">
						<view class="flex-row group_11">
							<image :src="item.avatar" class="image_14" />
							<view class="flex-col group_12">
								<text>{{ item.nickname }}</text>
								<view class="star">
									<u-icon name="star-fill" v-for="(a,b) in item.service_score"
										color="rgba(243, 151, 53, 1)">
									</u-icon>
								</view>
							</view>
						</view>
						<text class="text_33">{{ item.content }}</text>
						<view class="flex-col group_13">
							<view class="flex-row">
								<image v-for="(a,b) in item.images" :src="a" class="equal-division-item"
									mode="aspectFill" />
							</view>
							<text class="text_34">{{ item.create_time }}</text>
						</view>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				isTabActive: 1,
				id: '',
				technician: {
					"nickname": "",
					"realname": "",
					"avatar": "",
					"album": [],
					"praise": "",
					"sales": 0,
					"credit": -1,
					"service": "",
					"height": 0,
					"birthday": 0,
					"practice": 0,
					"profile": "0",
					"collect": 0,
					"province": ""
				},
				service: [],
				collect: "",
				comment: [],
				list: [],
				comment_count: 0,
				total: null,
				page: 1,
				comment_index: 0,
			}
		},
		onLoad(options) {
			this.id = options.id;
			this.init();

		},
		methods: {
			// 接收参数，不同页面的渲染
			async init() {

				let data = await this.$http.post('api/technician/details', {
					id: this.id
				});
				console.log(data.service, "打印的值123")
				for (let i in data) {
					this[i] = data[i];
				}
				this.getList();
				console.log('xxx', this.comment);
			},
			changeComment(index) {
				this.comment_index = index;
			},
			changeCollect() {
				uni.showModal({
					//  ~~~修改的地方
					// content: this.collect != 1 ? '是否确认取消关注？':'是否确认关注？',
					content: this.collect != 1 ? '是否确认关注？' : '是否确认取消关注？',
					success: (res) => {
						let {
							confirm
						} = res;
						if (confirm) {
							this.$http.post('api/collect/collect', {
								technician_id: this.id
							}).then(res => {
								uni.showToast({
									icon: 'success',
									// title: this.collect != 1 ? '取消成功' : '关注成功'
									title: this.collect != 1 ? '关注成功' : '取消成功'
								})
								this.collect == 1 ? this.collect = 0 : this.collect = 1;
							})
						}
					}
				})
			},
			onClickTab(e) {
				if (this.isTabActive !== e) {
					this.isTabActive = e
				}
			},
			showImage(index) {
				console.log('查看详情', index)
				
				var list = this.technician.album;
				list.push(this.technician.avatar);
				
				uni.previewImage({
					urls: list,
					current: index
				});
			},
			// 获取评论列表
			getList() {
				console.log('加载评论列表', this.comment)
				this.$http.post('api/index/comment', {
					id: 0,
					technician_id: this.id,
					changeOrigin: true,
					type: this.comment[this.comment_index].id,
					page: this.page,
				}).then(res => {
					console.log("评价")
					console.log(res)
					let {
						list,
						total
					} = res;
					this.list.push(...list);
					this.total = Number(total);
				});
			}
			// btn(item){
			// let order_info = uni.getStorageSync('order_info')||{};
			// order_info.technician_id = item.id;
			// console.log(order_info.technician_id,"252569451",22222222222222222222222,order_info)
			// uni.setStorageSync('order_info',order_info);
			// uni.setStorageSync('technician',{...this.technician,id:this.id});
			// uni.setStorageSync('order_info1',item);
			// uni.navigateTo({
			// 	url:`/pages/confirm_order/confirm_order?id=${item.id}`
			// })
			// }
		}
	}
</script>

<style lang="scss" scoped>
	.home {
		position: relative;
		
		.select {
			padding: 8rpx 20rpx;
			background-color: rgba(24, 181, 103, 0.12);
			border-radius: 16rpx;
			height: 56rpx;
			margin-right: 20rpx;
		}
		
		.no-select {
			padding: 8rpx 20rpx;
			background-color: rgb(248, 248, 248);
			border-radius: 16rpx;
			height: 56rpx;
			margin-right: 20rpx;
			color: rgb(153, 153, 153);
		}
		
		.section_12 {
			padding: 38rpx 32rpx 24rpx;
			background-color: rgb(255, 255, 255);
		
			.group_11 {
				color: rgb(0, 0, 0);
				font-size: 28rpx;
				font-weight: 500;
				line-height: 40rpx;
				white-space: nowrap;
		
				.image_14 {
					border-radius: 50%;
					width: 72rpx;
					height: 72rpx;
				}
		
				.group_12 {
					margin-left: 16rpx;
		
					.image_15 {
						margin-top: 8rpx;
						width: 136rpx;
						height: 24rpx;
					}
		
					.star {
						display: flex;
					}
				}
			}
		
			.text_33 {
				margin-right: 14rpx;
				margin-top: 24rpx;
				color: rgb(102, 102, 102);
				font-size: 28rpx;
				line-height: 44rpx;
				text-align: left;
			}
		
			.group_13 {
				margin-top: 23rpx;
				color: rgb(153, 153, 153);
				font-size: 24rpx;
				line-height: 34rpx;
				white-space: nowrap;
		
				.text_34 {
					margin-top: 15rpx;
				}
		
				.equal-division-item {
					flex: 1 1 212rpx;
					border-radius: 12rpx;
					width: 212rpx;
					height: 214rpx;
				}
		
				.image_17 {
					margin-left: 26rpx;
				}
		
				.image_18 {
					margin-left: 24rpx;
				}
			}
		}

		.section_8 {
			margin-top: 24rpx;
			padding: 24rpx 0rpx 28rpx;
			// background-color: rgb(255, 255, 255);

			.image_11 {
				margin-left: 44rpx;
				margin-bottom: 10rpx;
				width: 42rpx;
				height: 20rpx;
			}

			.text_20 {
				margin-top: 24rpx;
				color: rgb(0, 0, 0);
				font-size: 28rpx;
				font-weight: 500;
				line-height: 40rpx;
				white-space: nowrap;
			}

			.text_21 {
				margin-right: 14rpx;
				margin-top: 24rpx;
				color: rgb(102, 102, 102);
				font-size: 28rpx;
				line-height: 44rpx;
				text-align: left;
			}

			.text_19 {
				margin-left: 40rpx;
				margin-top: 4rpx;
				color: rgb(102, 102, 102);
				font-size: 28rpx;
				line-height: 40rpx;
				white-space: nowrap;

				&.active {
					color: rgb(0, 0, 0);
					font-size: 32rpx;
					font-weight: bold;
					line-height: 44rpx;
					white-space: nowrap;
				}
			}
		}

		.swiper {
			height: 340rpx;

			.swiper-item {
				image {
					width: 100%;
					height: 800rpx;
				}
			}
		}

		.header {
			height: 144rpx;
			// background: linear-gradient(to bottom, rgba(255, 255, 255, 0) 0, rgba(0, 0, 0, 0) 72rpx, rgba(255, 255, 255, 1));
			position: relative;
			z-index: 2;
			margin-top: -72rpx;
			padding: 0 32rpx;
			display: flex;

			&:before {
				content: '';
				position: absolute;
				left: 0;
				right: 0;
				bottom: 0;
				top: 72rpx;
				background: #fff;
			}

			.logo {
				width: 144rpx;
				height: 144rpx;
				flex-shrink: 0;
				border: 8rpx solid white;
				border-radius: 144rpx;
				overflow: hidden;
				box-sizing: border-box;
			}

			.right {
				margin-left: 16rpx;
				flex: 1;
				display: flex;
				flex-direction: column;

				&-1 {
					display: flex;
					align-items: center;
					flex: 1;
					margin-bottom: 24rpx;
					transform: translateY(24rpx);

					.name {
						color: white;
						font-size: 32rpx;
					}

					image {
						width: 90rpx;
						height: 36rpx;
						margin-left: 16rpx;
					}

					.num {
						flex: 1;
						text-align: right;
						color: rgba(255, 255, 255, 1);
						font-size: 24rpx;
					}
				}

				&-2 {
					align-items: center;
					display: flex;
					color: rgba(80, 80, 80, 1);
					font-size: 28rpx;
					padding-top: 24rpx;

					.sex {

						width: 40rpx;
						height: 40rpx;
						margin-right: 16rpx;
					}

					.age {
						position: relative;
					}

					.age:nth-of-type(2) {
						margin: 0 48rpx;
					}

					.age:nth-of-type(2)::after {
						content: '';
						width: 2rpx;
						height: 16rpx;
						background-color: rgba(228, 228, 228, 1);
						position: absolute;
						top: 50%;
						transform: translateY(-50%);
						left: -24rpx;
					}

					.age:nth-of-type(2)::before {
						content: '';
						width: 2rpx;
						height: 16rpx;
						background-color: rgba(228, 228, 228, 1);
						position: absolute;
						top: 50%;
						transform: translateY(-50%);
						right: -24rpx;
					}

					.btn {
						width: 128rpx;
						height: 48rpx;
						font-size: 25rpx;
						background: linear-gradient(143deg, #35DBA0 0%, #18B567 100%);
						border-radius: 48rpx;
						margin-left: 42rpx;
						display: flex;
						align-items: center;
						justify-content: center;
						color: white;
					}
				}
			}
		}

		.cont {
			display: flex;

			padding: 32rpx 32rpx 20rpx 32rpx;
			background-color: white;

			view:first-of-type {
				width: 20%;
				height: 44rpx;
				font-size: 20rpx;
				display: flex;
				text-align: center;
				justify-content: center;
				align-items: center;
				padding: 0 10rpx;
				border: 2rpx solid rgba(24, 181, 103, 1);
				color: rgba(24, 181, 103, 1);
				font-size: 24rpx;
				box-sizing: border-box;
				border-radius: 8rpx;
				margin-right: 12rpx;
			}

			view:last-of-type {
				width: 80%;
				padding-left: 10rpx;
				color: rgba(102, 102, 102, 1);
				font-size: 28rpx;
			}
		}

		.content {
			background-color: white;
			margin-top: 24rpx;

			&-h1 {
				padding: 0 32rpx;
				height: 88rpx;
				display: flex;
				position: relative;

				.r-icon {
					position: absolute;
					width: 32rpx;
					height: 32rpx;
					top: 50%;
					transform: translateY(-50%);
					right: 32rpx;
				}

				view {
					display: flex;
					align-items: center;
					margin-right: 22rpx;

					image {
						width: 32rpx;
						height: 32rpx;
					}

					text {
						color: rgba(231, 190, 123, 1);
						font-size: 28rpx;
						margin-left: 4rpx;
					}
				}
			}

			&-h2 {
				display: flex;
				padding: 22rpx 32rpx 24rpx 32rpx;

				.view {
					flex: 1;
					height: 172rpx;
					background-color: rgba(24, 181, 103, 0.08);
					border-radius: 24rpx;
					display: flex;
					flex-direction: column;
					align-items: center;
					justify-content: center;

					view:first-of-type {
						color: rgba(24, 181, 103, 1);
						font-size: 56rpx;
						font-weight: bold;

						text {
							font-size: 28rpx;
						}
					}

					view:last-of-type {
						font-size: 28rpx;
						color: rgba(80, 80, 80, 1);
					}
				}

				.view:nth-of-type(2) {
					margin: 0 24rpx;
				}
			}

			&-h3 {
				display: flex;
				flex-wrap: wrap;
				padding: 24rpx 32rpx;

				view {
					height: 56rpx;
					padding: 0 24rpx;
					background-color: rgba(24, 181, 103, .12);
					border-radius: 16rpx;
					margin-right: 24rpx;
					color: rgba(24, 181, 103, 1);
					font-size: 28rpx;
					display: flex;
					align-items: center;
					justify-content: center;
					margin-bottom: 24rpx;
				}

				.gayer {
					background-color: rgba(248, 248, 248, 1);
					color: rgba(153, 153, 153, 1);
				}
			}
		}

		.service {
			margin: 24rpx 0 32rpx;

			.h1 {
				height: 96rpx;
				display: flex;
				align-items: center;
				padding-left: 56rpx;
				background-color: white;

				text {
					color: rgba(0, 0, 0, 1);
					font-size: 32rpx;
					position: relative;
				}

				text::before {
					content: '';
					width: 8rpx;
					height: 24rpx;
					background-color: rgba(24, 181, 103, 1);
					border-radius: 8rpx;
					position: absolute;
					top: 50%;
					transform: translateY(-50%);
					left: -16rpx;
				}
			}

			.product-wrapper {
				margin: 30rpx 0;

				.product-item {
					// padding: 16rpx 22rpx 24rpx 24rpx;
					padding: 16rpx 0rpx 24rpx 0rpx;
					background-color: rgb(255, 255, 255);
					border-radius: 28rpx;
					margin: 20rpx 0;

					.left-section {
						//padding-top: 140rpx;
						color: rgb(255, 255, 255);
						font-size: 24rpx;
						line-height: 34rpx;
						white-space: nowrap;
						border-radius: 12rpx;
						//background-image: url('https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844604317986283.png');
						background-size: cover;
						background-repeat: no-repeat;
						width: 216rpx;
						height: 182rpx;

						.text-wrapper {
							padding: 13rpx 0 0 0;
							position: relative;

							.text_6 {
								text-align: center;
								width: 216rpx;
								position: absolute;
								bottom: 0;
								background-color: rgba(0, 0, 0, 0.36);
								border-radius: 0px 0px 12rpx 12rpx;
								//margin: 0 6rpx;
							}
						}
					}

					.right-group {
						margin-left: 24rpx;
						flex: 1 1 auto;

						.group_7 {
							margin-top: 8rpx;

							.left-text-wrapper {
								padding: 4rpx 0;
								color: rgb(255, 255, 255);
								font-size: 20rpx;
								line-height: 28rpx;
								white-space: nowrap;
								background-color: rgb(243, 151, 53);
								border-radius: 8rpx;
								width: 104rpx;
								height: 36rpx;
								text-align: center;
							}

							.right-text-wrapper {
								margin-left: 6rpx;
								padding: 2rpx 0;
								color: rgb(243, 151, 53);
								font-size: 20rpx;
								line-height: 28rpx;
								white-space: nowrap;
								border-radius: 8rpx;
								width: 104rpx;
								height: 36rpx;
								border: solid 2rpx rgb(243, 151, 53);
								text-align: center;
							}
						}

						.group_8 {
							margin-top: 8rpx;
							color: rgb(102, 102, 102);
							font-size: 24rpx;
							line-height: 34rpx;
							white-space: nowrap;

							.image_5 {
								width: 32rpx;
								height: 32rpx;
							}

							.text_14 {
								margin-left: 4rpx;
							}
						}

						.group_9 {
							margin-top: 16rpx;

							.left-group {
								margin-top: 14rpx;
								white-space: nowrap;
								height: 44rpx;

								.text_16 {
									color: rgb(255, 74, 74);
									font-size: 24rpx;
									line-height: 28rpx;
								}

								.text_18 {
									font-family: Price-Bold;
									color: rgb(255, 74, 74);
									font-size: 36rpx;
									line-height: 44rpx;
								}
							}

							.center-group {
								margin-left: 4rpx;
								margin-top: 20rpx;
								position: relative;

								.group_10 {
									line-height: 28rpx;
									white-space: nowrap;

									.text_20 {
										color: rgb(153, 153, 153);
										font-size: 24rpx;
										line-height: 28rpx;
									}

									.text_22 {

										color: rgb(153, 153, 153);
										font-size: 24rpx;
										line-height: 28rpx;
									}
								}

								.divider {
									background-color: rgb(151, 151, 151);
									width: 88rpx;
									height: 2rpx;
									position: absolute;
									left: 2rpx;
									right: 0;
									top: 50%;
									transform: translateY(-50%);
								}
							}

							.right-text-wrapper_1 {
								display: flex;
								align-self: flex-end;
								margin-left: 100rpx;
								margin-top: 6rpx;
								padding: 4rpx 0 2rpx;
								color: rgb(255, 255, 255);
								font-size: 24rpx;
								line-height: 34rpx;
								white-space: nowrap;
								background-image: linear-gradient(112.4deg,
										rgb(53, 219, 160) 0%,
										rgb(53, 219, 160) -13.77%,
										rgb(24, 181, 103) 111.69%,
										rgb(24, 181, 103) 100%);
								border-radius: 20rpx;
								width: 128rpx;
								height: 40rpx;
								text-align: center;
							}
						}

						.text_8 {
							color: rgb(0, 0, 0);
							font-size: 32rpx;
							font-weight: 500;
							line-height: 44rpx;
							white-space: nowrap;
						}

						.text_10 {
							margin-bottom: 10rpx;
							color: rgb(153, 153, 153);
							font-size: 24rpx;
							line-height: 34rpx;
							white-space: nowrap;
							padding-right: 30rpx;
						}
					}
				}
			}
		}
	}
</style>
