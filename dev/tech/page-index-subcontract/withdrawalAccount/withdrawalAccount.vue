<template>
	<!-- 提现账户 -->
	<view class="withdrawalAccount" v-if="!loading">
		<view class="Accountwrap">
			<view class="Account-top">
				<view class="Account-top-img">
					<image :src="imgUrl+'zfbicon.png'" mode=""></image>
				</view>
				<view class="">
					提现到支付宝账户
				</view>
			</view>
			<view class="Account-name" v-show = "ali==0">
				<u-form labelPosition="left" :model="model1" :rules="rules" ref="form">
					<u-line></u-line>
					<u-form-item class="formItem" labelWidth="200rpx" label="账户姓名" prop="userInfo.name" ref="item1">
						<u--input placeholder="输入提现人姓名" v-model="model1.userInfo.name" border="none"></u--input>
					</u-form-item>
					<u-line></u-line>
					<u-form-item borderBottom="false" labelWidth="200rpx" label="提现账号" prop="userInfo.number"
						ref="item1">
						<u--input placeholder="输入提现账号" v-model="model1.userInfo.number" border="none"></u--input>
					</u-form-item>

				</u-form>



			</view>
			<view class="Account-name f-s14"  style="font-size: 46rpx; color: #262626;padding:0 30rpx 30rpx;" v-show = "ali==1">
				<view class="">已绑定</view>
				<view class="p-t15">账号：{{info.realname}}</view>
			</view>
		</view>

		<!-- 保存设置 -->
		<view class="keppAccount" @click="keppsetup"  v-show = "ali==0">
			保存设置
		</view>
	</view>
</template>

<script>
	import {aliBind,withdraw} from "./api.js"
	export default {
		async onLoad(){
			uni.showLoading({title:'加载中'})
			let res =await withdraw()
			if(res.code==1){
				uni.hideLoading()
				this.loading = false
				this.info = res.data
				this.ali = res.data.ali
			}
		},
		data() {
			return {
				imgUrl: this.$store.state.imgUrl,
				loading:true,
				howSex: false,
				ali:0,  //判断是否已绑定  0 未绑定 1 已绑定
				info:{},
				model1: {
					userInfo: {
						name: '',
						number: ""
					},
				},
				rules: {
					'userInfo.name': {
						type: 'string',
						required: true,
						message: '请输入提现人姓名',
						trigger: ['blur', 'change']
					},
					'userInfo.number': {
						type: 'string',
						required: true,
						message: '请输入提现账号',
						trigger: ['blur', 'change']
						},
			},

			}
		},
		methods: {
			async keppsetup() {
				
				let obj = {}
				obj.realname  =this.model1.userInfo.name
				obj.mobile  =this.model1.userInfo.number
				let res = await aliBind(obj)
				if(res.code==1){
					this.ali = 1
				}
				uni.showToast({
					title:'保存成功',
					icon:'success'
				})
				setTimeout( function(){
						uni.switchTab({
							url:'/pages/index/index'
						})
				} ,1000)
			}

		},
	}
</script>

<style lang="less">
	.withdrawalAccount {
		width: 686rpx;
		margin: 0 auto;

		.Accountwrap {
			margin-top: 24rpx;
			border-radius: 16rpx;
			background: #FFFFFF;

			.Account-top {
				height: 128rpx;
				width: 100%;
				font-size: 34rpx;
				font-weight: bold;
				color: #000000;
				display: flex;
				align-items: center;
				padding-left: 24rpx;

				.Account-top-img {
					width: 64rpx;
					height: 64rpx;
					margin-right: 24rpx;

					image {
						width: 100%;
						height: 100%;
					}
				}
			}

			.Account-name,
			.Account-number {
				width: 100%;
				font-size: 32rpx;
				// height: 192rpx;

				.u-form-item .data-v-067e4733 {
					height: 76rpx;
					padding-left: 12rpx;
					font-size: 32rpx;
					font-weight: 500;
					// color: #000000;
				}

				.u-form-item__body__right__message .data-v-067e4733 {
					color: #f00;
				}

				.u-form-item__body__right .data-v-067e4733 {
					padding: 0;
				}
			}
		}

		.keppAccount {
			width: 686rpx;
			height: 88rpx;
			margin: 0 auto;
			background: #0ea17d;
			border-radius: 50rpx;
			text-align: center;
			line-height: 88rpx;
			font-size: 32rpx;
			font-weight: 500;
			color: #FFFFFF;
			margin-top: 64rpx;
		}

	}
</style>
