<template>
	<!-- 提现账户 -->
	<view class="withdrawalAccount" >
		<view class="Accountwrap">
			<view class="Account-top">
				<view class="Account-top-img">
					<image src="/static/images/zfbicon.png" mode=""></image>
				</view>
				<view class="">
					提现到支付宝账户
				</view>
			</view>
			<view class="Account-name" >
				<u-form labelPosition="left" :model="info" :rules="rules" ref="form">
					<u-line></u-line>
					<u-form-item class="formItem" labelWidth="200rpx" label="账户姓名" prop="userInfo.name" ref="item1">
						<u--input placeholder="输入提现人姓名" v-model="info.realname" border="none"></u--input>
					</u-form-item>
					<u-line></u-line>
					<u-form-item borderBottom="false" labelWidth="200rpx" label="提现账号" prop="userInfo.number"
						ref="item1">
						<u--input placeholder="输入提现账号" v-model="info.mobile" border="none"></u--input>
					</u-form-item>

				</u-form>

			</view>
			<!-- <view class="Account-name f-s14"  style="font-size: 46rpx; color: #262626;padding:0 30rpx 30rpx;" v-show = "ali==1">
				<view class="">已绑定</view>
				<view class="p-t15">账号：{{info.realname}}</view>
			</view> -->
		</view>

		<!-- 保存设置 -->
		<view class="keppAccount" @click="keppsetup" >
			保存设置
		</view>
	</view>
</template>

<script>
	// import {aliBind,withdraw} from "./api.js"
	export default {
		
		data() {
			return {
				imgUrl: this.$store.state.imgUrl,
				loading:true,
				howSex: false,
				ali:0,  //判断是否已绑定  0 未绑定 1 已绑定
				info:{
					ali:'',
					realname:'',
					balance:'',
					mobile:''
				},
				model1: {
					userInfo: {
						name: '',
						number: ""
					},
				},
				rules: {
					'realname': {
						type: 'string',
						required: true,
						message: '请输入提现人姓名',
						trigger: ['blur', 'change']
					},
					'mobile': {
						type: 'string',
						required: true,
						message: '请输入提现账号',
						trigger: ['blur', 'change']
						},
			},

			}
		},
		async onLoad(){
			uni.showLoading({title:'加载中'})
			this.init()
			
		},
		methods: {
			init(){
				this.$http.post('api/withdraw/withdraw', {
				}).then(res => {
					
						console.log("====")
						console.log(res)
						uni.hideLoading()
						this.loading = false
						this.info = res
						this.ali = res.ali
						console.log(this.info)
					
				})
			},
			async keppsetup() {
				
				let obj = {}
				obj.realname  =this.info.realname
				obj.mobile  =this.info.mobile
				
				console.log(obj)
				this.$http.post('api/withdraw/aliBind',obj).then(res => {
					this.ali = 1
					uni.showToast({
						title:"绑定成功"
					})
					
				})
				
				
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
				padding: 0 20rpx;
				height: 222rpx;

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
			background: linear-gradient(90deg, #46D17E 0%, #3EC735 100%);
			border-radius: 16rpx;
			text-align: center;
			line-height: 88rpx;
			font-size: 32rpx;
			font-weight: 500;
			color: #FFFFFF;
			margin-top: 64rpx;
		}

	}
</style>
