<template>
	<view class="flex-col page">
		<u-swiper :list="list3" indicator indicatorMode="line" circular height='300'></u-swiper>
		<view class="section_3">
			<u-skeleton rows="3" :loading="loading" :title="false" titleWidth='100%' rowsHeight='26'></u-skeleton>
			<view class="flex-row section_4" v-show="tipsValue">
				<image src="../../static/images/detail-tips.png" class="image_4" />
				<view class="text_1">{{tipsValue}}</view>


			</view>
			<u-scroll-list class="justify-center horiz-list" :indicator="false">
				<view class="flex-col horiz-list-item" :key="i" v-for="(item, i) in productlList"
					@click="onClickItem(i)" :class="{'active':isActive==i}">
					<text class="text_2">{{ item.name }}</text>
					<view class="flex-row group">
						<text class="text_3 prcie">{{ item.price }}</text>
						<text class="text_4">元/{{item.duration}}分钟</text>
					</view>
				</view> 

			</u-scroll-list>
			<view class="justify-between section_5" v-if="!is_vip && configIni && configIni.showVip">
				<view class="flex-row group_2">
					<image
						src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844679077316805.png"
						class="image" />
					<text class="text_10">黑卡下单再享受{{ vip_discount }}折</text>
				</view>
				<view class="flex-col items-center text-wrapper" @click="toPath('/bundle/pages/user_vip/user_vip')">
					<text>去开卡</text>
				</view>
			</view> 
		</view>

		<view class="justify-between section_6" style="flex-shrink:0;">
			<u-skeleton rows="1" :loading="loading" :title="false" titleWidth='100%' v-show="loading"></u-skeleton>
			<text class="text_12" v-show="!loading">保障</text>
			<view class="flex-row group_3" v-show="!loading">
				<image
					src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844680572912402.png"
					class="image_6" />
				<text class="text_13">不满意重服务</text>
				<image
					src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844679591498645.png"
					class="image_8" />
				<text class="text_14">无额外收费</text>
			</view>
			<image
				src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844680694652017.png"
				class="image" />
		</view>
		<view class="justify-between section_7" style="flex-shrink:0;" v-if="coupon.length && coupon.length" @click="showCoupon">
			<u-skeleton rows="1" :loading="loading" :title="false" titleWidth='100%' v-show="loading"></u-skeleton>

			<view class="flex-row" v-show="!loading" v-if="coupon.length">

				<text class="text_15">领券</text>

				<view class="flex-row group_5">
					<view class="left-text-wrapper flex-col items-center" v-for="(item,index) in coupon" :key="index">
						<text>{{ item.name }}</text>
					</view>
				</view>
			</view>
			<image @click="showCoupon" v-if="coupon.length"
				src="https://project-user-resource-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844823571794239.png"
				class="image_10" />
		</view>
		<u-skeleton class="flex-col section_8" rows="3" :loading="loading" :title="false" titleWidth='100%'
			v-show="loading" rowsHeight='26'></u-skeleton>

		<view class="content" v-show="!loading">
			<view class="flex-col section_8">
				<view class="flex-row">
					<text class="text_19" :class="{'active':isTabActive==1}" @click="onClickTab(1)">项目介绍
						<image v-show="isTabActive==1" src="../../static/images/isTabActive.png" class="image_11" />
					</text>
					<text class="text_19" :class="{'active':isTabActive==2}"
						@click="onClickTab(2)">用户评论({{comment_count}})
						<image v-show="isTabActive==2" src="../../static/images/isTabActive.png" class="image_11" />
					</text>
				</view>

				<view v-if="isTabActive==1">
					<!-- <u-read-more ref="uReadMore" :showHeight="showHeight" toggle @open="open" @close="close"
						:shadowStyle="shadowStyle" closeText="展开" color="#999999" textIndent="0">
						<u-parse :content="content" @load="load" :tag-style="tagStyle"></u-parse>
					</u-read-more> -->
					<u-parse :content="content" @load="load" :tag-style="tagStyle"></u-parse>
				</view>

				<view v-else>
					<view class="flex-col section_11">
						<view class="group_9" :class="total === 0 ? 'group_no':''">
							<view @click="changeComment(b)" :class="comment_index === b ? 'select':'no-select'"
								class="flex-col items-center" v-for="(a,b) in comment" :key="b">
								<text>{{ a.name }}</text>
							</view>
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

			<!-- <view class="justify-between section_10">
				<view class="flex-row">
					<text class="text_23">用户评论</text>
					<text class="text_24">（{{ comment_count }}）</text>
				</view>
			</view>
			<view class="flex-col section_11">
				<view class="group_9" :class="total === 0 ? 'group_no':''">
					<view @click="changeComment(b)" :class="comment_index === b ? 'select':'no-select'"
						class="flex-col items-center" v-for="(a,b) in comment" :key="b">
						<text>{{ a.name }}</text>
					</view>
				</view>
			</view>
			<view class="flex-col section_12" v-for="(item,index) in list" :key="index">
				<view class="flex-row group_11">
					<image :src="item.avatar" class="image_14" />
					<view class="flex-col items-center group_12">
						<text>{{ item.nickname }}</text>
						<view class="star">
							<u-icon name="star-fill" v-for="(a,b) in item.service_score" color="rgba(243, 151, 53, 1)">
							</u-icon>
						</view>
					</view>
				</view>
				<text class="text_33">{{ item.content }}</text>
				<view class="flex-col group_13">
					<view class="flex-row">
						<image v-for="(a,b) in item.images" :src="a" class="equal-division-item" />
					</view>
					<text class="text_34">{{ item.create_time }}</text>
				</view>
			</view> -->
		</view>
		<view style="width:100%;height: 200rpx;"></view>
		<view class="flex-col group_14">
			<view class="section_13"> </view>
			<view class="flex-row section_14">
				<view class="flex-col items-center group_15" @click="onCollection()" v-if="false">
					<!-- <image
						src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844681855870306.png"
						class="image_19" /> -->
					<u-icon size="28" :name="name" :color="name === 'heart-fill' ? 'rgb(243, 151, 53)':''"></u-icon>
					<text>收藏</text>
				</view>
				<view class="flex-col group_16" @click="onConfirm">
					<view class="flex-col items-center button">
						<text>立即下单</text>
					</view>
				</view>
			</view>
		</view>

		<coupon ref="coupon" :couponList="coupon"></coupon>

		<!-- <u-popup :show="showPopup" :round="10" mode="bottom" @close="closePopup" @open="openPopup">
			<view style="padding-top: 30rpx;">
				<view>
					<u-collapse :border="false" :value="['name1']" @change="xdChange" @close="xdClose" @open="xdOpen">
						<u-collapse-item :title="item.name" name="name1" v-for="item in config">
							<text class="u-collapse-content">{{item.content}}</text>
						</u-collapse-item>
					</u-collapse>
				</view>

				<u-button v-if="waitTimer<=0" @click="onConfirm" :text="waitTimer>0?waitTimer+'秒后可下单,请认真阅读事项':tid?'立即下单': '选择技师'"
					:customStyle="{'width':'80%','height':'90rpx','background':'rgb(24, 181, 103)','margin':'0 auto','color':'white','border-radius':'50rpx','font-size':'48rpx'}">
				</u-button>
				<u-button v-else :text="waitTimer>0?waitTimer+'秒后可下单,请认真阅读事项':tid?'立即下单': '选择技师'"
					:customStyle="{'width':'80%','height':'90rpx','background':'#999','margin':'0 auto','color':'white','border-radius':'50rpx','font-size':'48rpx'}">
				</u-button>
			</view>
		</u-popup> -->

	</view>
</template>

<script>
	import tmBottomnavigation from '@/tm-vuetify/components/tm-bottomnavigation/tm-bottomnavigation.vue'
	import tmSkeleton from '@/tm-vuetify/components/tm-skeleton/tm-skeleton.vue'
	import coupon from "./components/coupon.vue"
	export default {
		components: {
			tmBottomnavigation,
			tmSkeleton,
			coupon
		},
		data() {
			return {
				waitTimer: 10,
				showPopup: false,
				loading: true,
				productlList: [],
				isActive: 0,
				isTabActive: 1,
				list3: [],
				content: '',
				showHeight: 200,
				tagStyle: {
					p: 'color: #606266; line-height: 24px;'
				},
				shadowStyle: {
					position: 'relative',
					backgroundImage: "linear-gradient(-180deg, rgba(255, 255, 255, 0) 0%, #fff 80%)",
					paddingTop: "100px",
					marginTop: "-100px",
				},
				tipsValue: '',
				id: '',
				tid: '',
				praise: 0,
				is_vip: true,
				vip_discount: 0,
				comment_count: 0,
				comment: [],
				comment_index: 0,
				list: [],
				total: null,
				page: 1,
				name: "heart",
				coupon: [],
				config: [],
				inter: null,
				configIni:[]
			};
		},
		onLoad(options) {
			this.id = options.id;
			this.tid = !options.tid ? '' : options.tid;
			console.log("打印的值", options)
		},
		onShow() {
			this.getConfig();
			this.init();
		},
		onReachBottom() {
			if (this.list.length === this.total) {
				return;
			}
			this.page++;
			this.getList();
		},
		onNavigationBarButtonTap(e) {
			let {
				index
			} = e;
			if (index === 0) {
				uni.navigateTo({
					url: '/bundle/pages/share/share'
				})
			}
		},
		methods: {
			getConfig(){
				this.configIni = {
					showYaoQing: true,
					showVip: true,
					showJsInfo: true,
					showBj: true
				}
			},
			xiadan() {
				var that = this;
				that.showPopup = true;
				that.inter = setInterval(function() {
					that.waitTimer--;
					if (that.waitTimer <= 0) {
						clearInterval(that.inter);
					}
				}, 1000)

			},
			closePopup() {
				this.showPopup = false;
				clearInterval(this.inter);
				this.waitTimer = 15;
			},
			openPopup() {

			},
			init() {
				var that = this;
				// 获取详情
				this.$http.post('api/index/serviceDetails', {
					id: this.id
				}).then(res => {
					this.loading = false;
					this.config = res.config
					this.tipsValue = '随叫随到 最快30分钟上门';
					let {
						service,
						vip,
						spec,
						comment
					} = res;
					this.list3 = service.banner;
					this.content = service.introduce;
					this.praise = service.praise;
					this.comment_count = service.comment_count;
					for (let i in vip) {
						this[i] = vip[i];
					}
					this.productlList = spec;
					this.comment = comment;
					this.getList();
					this.getCoupon();
					// setTimeout(() => {
					// 	that.$nextTick(() => {
					// 		that.$refs.uReadMore.init();
					// 	})
					// }, 500)
				});
			},
			// 获取评论列表
			getList() {
				this.$http.post('api/index/comment', {
					id: this.id,
					technician_id: 0,
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
			},
			// 获取优惠券
			getCoupon() {
				this.$http.post('api/index/coupon', {
					id: this.id
				}).then(res => {
					this.coupon = res;
				})
			},
			changeComment(index) {
				this.comment_index = index;
				this.page = 1;
				this.total = null;
				this.list = [];
				this.getList();
			},
			load() {
				// this.$refs.uReadMore.init()
			},
			open(name) {
				console.log('open', name);
			},
			close(name) {
				console.log('close', name);
			},
			onPullDownRefresh() {
				let _self = this
				console.log('refresh');
				setTimeout(function() {
					uni.stopPullDownRefresh(); //停止当前页面下拉刷新
					_self.page = 1;
					_self.total = null;
					_self.list = [];
					_self.init();
				}, 1000);
			},
			onClickItem(e) {
				if (this.isActive !== e) {
					this.isActive = e
				}
			},
			onClickTab(e) {
				if (this.isTabActive !== e) {
					this.isTabActive = e
				}
			},
			onCollection() {
				this.$http.post('api/collect/collect', {
					technician_id: this.id
				}).then(res => {
					this.name === 'heart-fill' ? this.name = 'heart' : this.name = 'heart-fill';
					if (this.name === 'heart-fill') {
						uni.showToast({
							icon: 'none',
							title: '收藏成功'
						})
					} else {
						uni.showToast({
							icon: 'none',
							title: '取消成功'
						})
					}
				})
			},
			onConfirm() {

				// if (this.waitTimer > 0) {
				// 	uni.showToast({
				// 		icon: 'none',
				// 		title: '请耐心看完再下单'
				// 	})
				// 	return false;
				// }

				let isActive = this.productlList[this.isActive];
				uni.setStorageSync('order_info', {
					service_id: this.id,
					spec_id: isActive.id,
					coupon_id: 0,
					technician_id: this.tid
				})
				if (!this.tid) {
					uni.navigateTo({
						url: `/pages/selectTechnician/selectTechnician?name=${this.productlList[this.isActive].name}`
					});
				} else {
					uni.navigateTo({
						url: `/pages/confirm_order/confirm_order?id=${this.tid}`
					})
				}
			},
			showCoupon() {
				this.$refs.coupon.open();
			},
			toPath(url) {
				uni.navigateTo({
					url,
				})
			},

			xdOpen(e) {
				// console.log('open', e)
			},
			xdClose(e) {
				// console.log('close', e)
			},
			xdChange(e) {
				// console.log('change', e)
			}
		}
	};
</script>

<style scoped lang="scss">
	.page {
		padding-bottom: 66rpx;
		background-color: #f8f8f8;
		width: 100%;
		height: 100%;

		.section_1 {
			padding: 88rpx 0 24rpx;
			color: rgb(255, 255, 255);
			font-size: 36rpx;
			font-weight: 500;
			line-height: 50rpx;
			white-space: nowrap;
			background-image: url('https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844674346731028.png');
			background-position: 0px 0px;
			background-size: 100% 100%;
			background-repeat: no-repeat;

			.section_2 {
				padding: 20rpx 32rpx 18rpx;
				background-color: rgba(0, 0, 0, 0.24);
			}

			.image_3 {
				margin-top: 348rpx;
				align-self: center;
				width: 104rpx;
				height: 12rpx;
			}
		}

		.section_3 {
			padding: 24rpx 0 24rpx 24rpx;
			background-color: rgb(255, 255, 255);

			.section_4 {
				margin-right: 32rpx;
				padding: 10rpx 26rpx 14rpx;
				color: rgb(102, 102, 102);
				font-size: 28rpx;
				line-height: 40rpx;
				white-space: nowrap;
				background-color: rgb(248, 248, 248);
				border-radius: 24rpx;

				.image_4 {
					margin-top: 4rpx;
					width: 112rpx;
					height: 36rpx;
				}

				.text_1 {
					margin-left: 26rpx;
				}
			}

			.horiz-list {
				padding: 24rpx 0;

				.horiz-list-item {
					padding: 22rpx 18rpx 26rpx 22rpx;
					background-color: #F8F8F8;
					border-radius: 24rpx;
					height: 152rpx;
					margin: 0 8rpx;

					&.active {
						background-color: rgba(24, 181, 103, 0.12);
						border: solid 2rpx rgb(24, 181, 103);
					}

					.text_2 {
						color: rgb(0, 0, 0);
						font-size: 32rpx;
						font-weight: 500;
						line-height: 44rpx;
						white-space: nowrap;
					}

					.group {
						margin-top: 4rpx;

						.text_3 {
							color: rgb(243, 151, 53);
							font-size: 44rpx;
							line-height: 52rpx;
							white-space: nowrap;
							font-family: Price-Bold;
						}

						.text_4 {
							margin: 16rpx 4rpx 0;
							color: rgb(153, 153, 153);
							font-size: 24rpx;
							font-weight: 500;
							line-height: 34rpx;
							white-space: nowrap;
						}
					}
				}

				.horiz-list-item_1 {
					margin-left: 24rpx;
					padding: 24rpx 24rpx 28rpx;
					background-color: rgb(248, 248, 248);
					border-radius: 24rpx;
					height: 152rpx;

					.text_5 {
						color: rgb(0, 0, 0);
						font-size: 32rpx;
						font-weight: 500;
						line-height: 44rpx;
						white-space: nowrap;
					}

					.group_1 {
						margin-top: 4rpx;

						.text_6 {
							color: rgb(243, 151, 53);
							font-size: 44rpx;
							line-height: 52rpx;
							white-space: nowrap;
						}

						.text_7 {
							margin-left: 4rpx;
							margin-top: 16rpx;
							color: rgb(153, 153, 153);
							font-size: 24rpx;
							font-weight: 500;
							line-height: 34rpx;
							white-space: nowrap;
						}
					}
				}

				.horiz-list-item_2 {
					margin-left: 24rpx;
					padding: 24rpx 0 28rpx;
					background-color: rgb(248, 248, 248);
					border-radius: 24rpx 0 0 24rpx;
					width: 160rpx;
					height: 152rpx;

					.text_8 {
						color: rgb(0, 0, 0);
						font-size: 32rpx;
						font-weight: 500;
						line-height: 44rpx;
						white-space: nowrap;
					}

					.text_9 {
						margin-top: 4rpx;
						color: rgb(243, 151, 53);
						font-size: 44rpx;
						line-height: 52rpx;
						white-space: nowrap;
					}
				}
			}

			.section_5 {
				margin-right: 32rpx;
				padding: 26rpx 24rpx;
				background-image: linear-gradient(97.2deg,
						rgb(81, 81, 81) 0%,
						rgb(81, 81, 81) -15.28%,
						rgb(36, 36, 36) 114.1%,
						rgb(36, 36, 36) 100%);
				border-radius: 24rpx;

				.group_2 {
					color: rgb(226, 189, 128);
					font-size: 28rpx;
					font-weight: 500;
					line-height: 40rpx;
					white-space: nowrap;

					.text_10 {
						margin: 4rpx 0 4rpx 16rpx;
					}
				}

				.text-wrapper {
					padding: 6rpx 0 2rpx;
					color: rgb(38, 38, 38);
					font-size: 28rpx;
					line-height: 40rpx;
					white-space: nowrap;
					background-image: linear-gradient(125.9deg,
							rgb(255, 218, 157) 0%,
							rgb(255, 218, 157) -7.15%,
							rgb(231, 191, 124) 104.11%,
							rgb(231, 191, 124) 100%);
					border-radius: 24rpx;
					width: 136rpx;
					height: 48rpx;
				}
			}
		}

		.section_6 {
			margin-top: 26rpx;
			padding: 20rpx 32rpx;
			background-color: rgb(255, 255, 255);

			.text_12 {
				margin: 4rpx 0;
				color: rgb(0, 0, 0);
				font-size: 28rpx;
				font-weight: 500;
				line-height: 40rpx;
				white-space: nowrap;
			}

			.group_3 {
				margin: 4rpx 0;
				color: rgb(80, 80, 80);
				font-size: 28rpx;
				line-height: 40rpx;
				white-space: nowrap;

				.text_13 {
					margin-left: 8rpx;
				}

				.image_8 {
					margin: 6rpx 0 6rpx 30rpx;
					width: 24rpx;
					height: 28rpx;
				}

				.text_14 {
					margin-left: 12rpx;
				}
			}
		}

		.section_7 {
			padding: 22rpx 32rpx;
			background-color: rgb(255, 255, 255);

			.image_10 {
				margin-right: 8rpx;
				align-self: center;
				width: 32rpx;
				height: 8rpx;
			}

			.text_15 {
				color: rgb(0, 0, 0);
				font-size: 28rpx;
				font-weight: 500;
				line-height: 40rpx;
				white-space: nowrap;
			}

			.group_5 {
				margin-left: 80rpx;
				color: rgb(255, 255, 255);
				font-size: 24rpx;
				line-height: 34rpx;
				white-space: nowrap;

				.left-text-wrapper {
					padding: 6rpx 0 4rpx;
					background-color: rgb(243, 151, 53);
					border-radius: 8rpx;
					width: 144rpx;
					height: 44rpx;
				}

				.view_1 {
					margin-left: 16rpx;
				}
			}
		}

		.section_8 {
			margin-top: 24rpx;
			padding: 24rpx 32rpx 28rpx;
			background-color: rgb(255, 255, 255);

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

		.section_9 {
			padding: 12rpx 0;
			color: rgb(153, 153, 153);
			font-size: 28rpx;
			line-height: 40rpx;
			white-space: nowrap;
			background-color: rgb(255, 255, 255);

			.image_12 {
				margin-left: 14rpx;
				align-self: center;
				width: 20rpx;
				height: 12rpx;
			}
		}

		.section_10 {
			margin-top: 24rpx;
			padding: 28rpx 32rpx 24rpx;
			background-color: rgb(255, 255, 255);

			.group_8 {
				margin-bottom: 4rpx;
				color: rgb(153, 153, 153);
				font-size: 28rpx;
				line-height: 40rpx;
				white-space: nowrap;
			}

			.text_23 {
				color: rgb(0, 0, 0);
				font-size: 32rpx;
				font-weight: 500;
				line-height: 44rpx;
				white-space: nowrap;
			}

			.text_24 {
				color: rgb(102, 102, 102);
				font-size: 32rpx;
				line-height: 44rpx;
				white-space: nowrap;
			}
		}

		.section_11 {
			padding: 24rpx 32rpx;
			background-color: rgb(255, 255, 255);

			.group_9 {
				color: rgb(24, 181, 103);
				font-size: 28rpx;
				line-height: 40rpx;
				white-space: nowrap;
				display: flex;

				.text-wrapper_1 {
					padding: 8rpx 0;
					background-color: rgba(24, 181, 103, 0.12);
					border-radius: 16rpx;
					width: 160rpx;
					height: 56rpx;
					margin-right: 20rpx;
				}

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

				.view_3 {
					margin-left: 24rpx;
				}

				.text-wrapper_2 {
					margin-left: 24rpx;
					padding: 8rpx 0;
					background-color: rgba(24, 181, 103, 0.12);
					border-radius: 16rpx;
					width: 176rpx;
					height: 56rpx;
				}

				.text-wrapper_3 {
					margin-left: 24rpx;
					padding: 8rpx 0;
					background-color: rgba(24, 181, 103, 0.12);
					border-radius: 16rpx;
					width: 118rpx;
					height: 56rpx;
				}
			}

			.group_10 {
				margin-top: 24rpx;
				color: rgb(153, 153, 153);
				font-size: 28rpx;
				line-height: 40rpx;
				white-space: nowrap;

				.text-wrapper_4 {
					padding: 8rpx 0;
					background-color: rgb(248, 248, 248);
					border-radius: 16rpx;
					width: 216rpx;
					height: 56rpx;
				}

				.text-wrapper_5 {
					margin-left: 24rpx;
					padding: 8rpx 0;
					background-color: rgb(248, 248, 248);
					border-radius: 16rpx;
					width: 176rpx;
					height: 56rpx;
				}
			}

			.group_no {
				padding-bottom: 144rpx;
			}
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

		.group_14 {
			padding: 2rpx 0 68rpx;
			position: fixed;
			bottom: 0;
			width: 100vw;

			.section_13 {
				background-color: rgb(255, 255, 255);
				height: 84rpx;
			}

			.section_14 {
				padding: 16rpx 32rpx;
				background-color: rgb(255, 255, 255);
				box-shadow: 0px -8rpx 16rpx 0px rgba(0, 0, 0, 0.06);
				position: absolute;
				left: 0;
				right: 0;
				bottom: 2rpx;

				.group_15 {
					margin-bottom: 4rpx;
					color: rgb(80, 80, 80);
					font-size: 20rpx;
					line-height: 28rpx;
					white-space: nowrap;

					.image_19 {
						width: 56rpx;
						height: 56rpx;
					}
				}

				.group_16 {
					margin-left: 40rpx;
					flex: 1 1 auto;
					color: rgb(255, 255, 255);
					font-size: 32rpx;
					line-height: 44rpx;
					white-space: nowrap;

					.button {
						padding: 22rpx 0;
						background-color: rgb(24, 181, 103);
						border-radius: 42rpx;
					}
				}
			}
		}

		.image_6 {
			margin: 4rpx 0;
			width: 32rpx;
			height: 32rpx;
		}

		.image {
			width: 48rpx;
			height: 48rpx;
		}
	}
</style>
