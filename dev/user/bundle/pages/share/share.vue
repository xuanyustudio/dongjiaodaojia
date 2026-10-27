<template>
	<view class="share">
		
		<u-navbar
			title=""
			:autoBack="true"
			bgColor="transparent"
			leftIconColor="white"
		>
		</u-navbar>
		
		<image class="img" src="../../../static/images/share.png" mode="widthFix"></image>
		<view class="cont">
			<view class="cont-1">
				<view class="cont-2">
					<view class="title">每邀请一个新用户，最高可赚</view>
					<view class="price color">
						20 <text class="color">元</text>
					</view>
					<view class="p">
						新用户注册可领取30元优惠券
					</view>
				</view>
			</view>
		</view>
		<view class="flex content">
			<image @click="to('/pages/user/zhiwen-share/zhiwen-share')" src="../../../static/images/share-btn.png" mode="widthFix"></image>
		</view>
		<view class="box">
			<view class="flex h1">如何赚钱</view>
			<view class="box-1 flex">
				<view class="views flex align-items-start" v-for="(item,index) in 3" :key="index">
					<view class="flex flex-direction-column">
						<image :src="require('../../../static/images/' + (46 + index) + '@2x.png')" mode=""></image>
						<text>{{ index === 0 ? '分享' : index === 1 ? '新人下单' : '第二单' }}</text>
						<text>{{ index === 0 ? '邀请好友' : index === 1 ? '最高赚20元' : '最高赚10元' }}</text>
					</view>
					<view class="i flex" v-if="index !== 2">
						<u-icon name="play-right-fill" color="rgba(255, 227, 131, 1)"></u-icon>
					</view>
				</view>
			</view>
		</view>

		<view class="box">
			<view class="flex h1">我的收益</view>
			<view class="box-2 flex">
				<view class="views flex flex-direction-column margin_24">
					<view class="price color">
						{{ money }}<text class="color">元</text>
					</view>
					<view class="btn flex" @tap="to('/bundle/pages/commission/commission')">
						累积收入 <u-icon name="play-right-fill" size="14" color="rgba(2, 87, 45, 1)"></u-icon>
					</view>
				</view>
				<view class="views flex flex-direction-column">
					<view class="price color">
						{{ user }} <text class="color">人</text>
					</view>
					<view class="btn flex">
						我的好友 <u-icon class="icon" name="arrow-down-fill" size="14" color="rgba(2, 87, 45, 1)" style="margin-top: 2rpx;margin-left: 10rpx;"></u-icon>
					</view>
				</view>
			</view>
			<view class="box-2">
				<view class="list">
					<view class="h2">邀请记录</view>
					<view class="view flex" v-for="(item,index) in dataList" :key="index">
						<image :src="item.avatar" mode=""></image>
						<view class="right">
							<view>用户：{{ item.nickname }}</view>
							<view>TEL：{{ item.mobile ? item.mobile.slice(0,3) + '****' + item.mobile.slice(7) : '****'}}
							</view>
						</view>
					</view>

					<view class="no flex" v-if="!dataList.length">
						暂无数据
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import appShare, {
		closeShare
	} from '@/uni_modules/zhouWei-APPshare/js_sdk/appShare';
	export default {
		data() {
			return {
				money: 0,
				user: 0,
				page: 1,
				total: null,
				dataList: [],
				pid:'',
				is:false,
			}
		},
		onLoad() {
			let userinfo = uni.getStorageSync("userInfo");
			this.init();
			this.getList();
		},
		onReachBottom() {
			if ( this.is) {return}
			this.page++;
			this.getList();
		},
		methods: {
			init() {
				// 收益
				this.$http.post('api/share/index').then(res => {
					for (let i in res) {
						this[i] = res[i];
					}
				});
				// 获取当前用户的id
				this.$http.post('api/user/userInfo').then(res => {
					this.pid = res.id
				})
			},
			// 邀请记录
			getList() {
				this.$http.post('api/share/team', {
					page: this.page
				}).then(res => {
					let {
						list,
						total
					} = res;
					this.total = total;
					if(this.page<2){
						this.dataList = list
					}else{
						this.dataList.push(...list);
					}
					if(!list.length){
						this.is = true
					}
				})
			},
			
			to(e){
				uni.navigateTo({
					url:e
				})
			}
		}
	}
</script>

<style lang="scss" scoped>
	.share {
		min-height: 100vh;
		background-color: rgba(24, 181, 103, 1);
		display: flex;
		flex-direction: column;

		.flex {
			display: flex;
			align-items: center;
			justify-content: center;
		}

		.align-items-start {
			align-items: flex-start;
		}

		.justify-content-start {
			justify-content: flex-start;
		}

		.flex-direction-column {
			flex-direction: column;
		}

		.color {
			// background: linear-gradient(-31deg, #FFDD68 0%, #FFFFFF 100%);
			// -webkit-background-clip: text;
			// color: transparent;
			color: #fff;
		}

		.img {
			width: 100%;
		}

		.cont {
			margin: -40rpx 40rpx 0;
			height: 395rpx;
			padding-bottom: 112rpx;
			background-color: rgba(0, 0, 0, .3);
			border-radius: 20rpx 20rpx 0 0;
			position: relative;
			z-index: 2;

			&-1 {
				height: 435rpx;
				margin: -40rpx 50rpx 0;
				background-color: red;
				background-color: rgba(0, 0, 0, .5);
				border-radius: 20rpx 20rpx 0 0;
			}

			&-2 {
				height: 395rpx;
				background-color: rgba(0, 0, 0, .3);
				margin: 0 35rpx;
				transform: translateY(40rpx);
				border-radius: 20rpx 20rpx 0 0;
				position: relative;
				z-index: 3;
				display: flex;
				flex-direction: column;
				align-items: center;

				.title {
					color: rgba(255, 221, 123, 1);
					font-size: 28rpx;
					display: flex;
					align-items: center;
					justify-content: center;
					padding: 31rpx 0 34rpx 0;
				}

				.price {
					font-size: 150rpx;
					display: flex;
					justify-content: center;
					align-items: flex-end;
					font-weight: bold;

					text {
						font-size: 50rpx;
						transform: translateY(-36rpx);
					}
				}

				.p {
					width: 378rpx;
					height: 51rpx;
					border: 1rpx solid rgba(255, 180, 79, 1);
					border-radius: 51rpx;
					display: flex;
					align-items: center;
					justify-content: center;
					color: rgba(255, 227, 131, 1);
					font-size: 24rpx;
				}
			}
		}

		.content {
			height: 202rpx;
			background-color: rgba(0, 0, 0, .2);
			margin: 0 40rpx 20rpx;
			border-radius: 0 0 20rpx 20rpx;

			image {
				width: 459rpx;
			}
		}

		.box {
			margin: 20rpx 40rpx;
			background-color: rgba(0, 0, 0, .2);
			padding: 47rpx 0 36rpx 0;
			border-radius: 20rpx;

			.h1 {
				color: rgba(255, 227, 131, 1);
				font-size: 36rpx;
				position: relative;
				margin-bottom: 70rpx;
			}

			.h1::after {
				content: '';
				width: 50rpx;
				height: 2rpx;
				background-color: rgba(255, 227, 131, 1);
				transform: translateX(20rpx);
			}

			.h1::before {
				content: '';
				width: 50rpx;
				height: 2rpx;
				background-color: rgba(255, 227, 131, 1);
				transform: translateX(-20rpx);
			}

			&-1 {
				.views {
					color: rgba(255, 187, 87, 1);
					font-size: 26rpx;

					image {
						width: 115rpx;
						height: 115rpx;
						margin-bottom: 20rpx;
					}
				}

				.i {
					margin: 0 40rpx;
					height: 115rpx;
				}
			}

			&-2 {
				margin: 0 34rpx;

				.views {
					flex: 1;
					height: 180rpx;
					background-color: rgba(0, 78, 25, 0.9);
					border-radius: 24rpx;

					.price {
						font-size: 60rpx;
						font-weight: bold;

						text {
							font-size: 26rpx;
						}
					}

					.btn {
						width: 192rpx;
						height: 56rpx;
						background: linear-gradient(-55deg, #FFBD59 0%, #FFDF7E 100%);
						border-radius: 56rpx;
						color: rgba(0, 78, 25, 1);
						font-size: 31rpx;

						.icon {
							margin-left: 18rpx;
						}
					}
				}

				.list {
					margin-top: 24rpx;
					background-color: rgba(0, 78, 25, 0.9);
					border-radius: 24rpx;
					padding: 40rpx;

					.h2 {
						color: rgba(255, 227, 131, 1);
						font-size: 32rpx;
					}

					.no {
						height: 80rpx;
						color: #C0C0C0;
						font-size: 28rpx;
					}

					.view {
						margin-top: 44rpx;

						image {
							width: 96rpx;
							height: 96rpx;
							border-radius: 96rpx;
							margin-right: 26rpx;
						}

						.right {
							flex: 1;
							color: rgba(255, 227, 131, 1);
							font-size: 28rpx;

							text:last-of-type {
								font-size: 24rpx;
							}
						}
					}
				}

				.margin_24 {
					margin-right: 24rpx;
				}
			}
		}
	}
</style>
