<template>
	<!-- 基本信息 -->
	<view class="essentialInformation">
		<view class="header">
			<view class="header-h2">
				设置您的基本信息
			</view>
			<view class="header-text">
				资料设置后无法修改
			</view>
		</view>
		<view class="info">
			<view class="height">
				<view class="det">
					本人身高
				</view>
				<picker :range="heightData" :value="height" @change="choiceHeight" v-show="s">
					<view class="choiceHeight">
						<view class="">
							{{height!==""?heightData[height]:'请选择本人身高'}}
						</view>
						
						<u-icon name="arrow-right" color="#666" size="20"></u-icon>
					</view>
				</picker>
				<view v-show="b"> 
					<view class="choiceHeight">
						<view class="">
							{{height!==""?height:'请选择本人身高'}}
						</view>
					</view>
				</view>
			</view>
			<view class="height">
				<view class="det">
					本人性别
				</view>
				<picker :range="sexData" :value="sex" @change="choiceSex" v-show="s">
					<view class="choiceHeight">
						<view class="">
							{{sex!==""?sexData[sex]:'请选择性别'}}
						</view>
						<u-icon name="arrow-right" color="#666" size="20"></u-icon>
					</view>
				</picker>
				<view v-show="b">
					<view class="choiceHeight">
						<view class="">
						{{sersex==1?'男':'女'}}
						</view>
					</view>
				</view>
			</view>
			<view class="height" style="margin-top: 40rpx;">
				<view class="det">
					出生年月
				</view>
				<picker mode="date" fields="day" @change="choicebirthday" v-show="s">
					<view class="choiceHeight">
						<view class="">
							{{birthday!==""?birthday:'请选择出生年月'}}
						</view>
						<u-icon name="arrow-right" color="#666" size="20"></u-icon>
					</view>
				</picker>
				<view v-show="b">
					<view class="choiceHeight">
						<view class="">
							{{birthday!==""?birthday:'请选择出生年月'}}
						</view>
					</view>
				</view>
			</view>
			<view class="height" style="margin-top: 40rpx;">
				<view class="det">
					从业时间
				</view>
				<picker mode="date" fields="day" @change="choiceworkTime" v-show="s">
					<view class="choiceHeight">
						<view class="">
							{{workingTime!==""?workingTime:'请选择从业时间'}}
						</view>
						<u-icon name="arrow-right" color="#666" size="20"></u-icon>
					</view>
				</picker>
				<view v-show="b">
					<view class="choiceHeight">
						<view class="">
							{{workingTime!==""?workingTime:'请选择从业时间'}}
						</view>
					</view>
				</view>
			</view>
			<view class="personalProfile">
				<view class="det">
					个人简介
				</view>
				<textarea class="textarea" v-model="personalProfile" placeholder="简单的介绍您的信息" style="height:224rpx;" />
			</view>

		</view>
		<!-- 提交 -->
		<view class="submit" @click="setInfo" v-if="zt==null || zt==2"><view class="">提交</view></view>
	</view>
</template>

<script>
	import {
		getUserInfo,setInformation
	} from "./api.js"
	export default {
		data() {
			
			return {
				zt: null, //状态
				workingTime: "", //从业时间
				height: "", //身高
				sex:'',
				personalProfile: "",
				birthday: "",
				b:false,
				s:true,
				sersex:''
			}
		},
		computed: {
			heightData() {
				let newHeightData = []
				for (let i = 0; i < 101; i++) {
					newHeightData.push(String(150 + i))
				}
				return newHeightData
			},
			sexData() {
				let newsexData = ['男','女']
				return newsexData
			}
		},
		onLoad() {
			this.initialize()
			
		},
		methods: {
			// 初始化页面信息
			initialize(){
				let obj = {
					openid: this.$db.get('token')
				}
				this.$db.http('my.Index',obj).then(res => {
					if(res.code==1){
						let data = res.data.technician_data
						if(data){
							this.b = true
							this.s = false
							this.height = data.height
							this.birthday = data.birthday
							this.workingTime = data.practice
							this.personalProfile = data.profile
							this.zt = data.status
							this.sersex = data.sex
						}
						
					}
				})
			},
			// 选择身高
			choiceHeight(e) {
				//通过e.detail.value获取值，获取的是自定义数据的下标
				this.height = e.detail.value
			},
			// 选择性别
			choiceSex(e) {
				//通过e.detail.value获取值，获取的是自定义数据的下标
				this.sex = e.detail.value
			},
			// 选择从业时间
			choiceworkTime(e) {
				//通过e.detail.value获取值，获取的是自定义数据的下标
				this.workingTime = e.detail.value
			},
			//出生年月
			choicebirthday(e) {
				this.birthday = e.detail.value
			},
			//设置基本信息
			setInfo() {
				if(this.height === ''){ uni.showToast({title:'请选择身高',icon:'none'}); return}
				if(this.sex === ''){ uni.showToast({title:'请选择性别',icon:'none'}); return}
				if(this.birthday === ''){ uni.showToast({title:'请选择出身时间',icon:'none'}); return}
				if(this.workingTime === ''){ uni.showToast({title:'请选择从业时间',icon:'none'}); return}
				if(this.personalProfile === ''){ uni.showToast({title:'请简单描述一下',icon:'none'}); return}
				
				let obj = {
					height: this.height+150,
					sex: this.sex,
					birthday: this.birthday,
					profile: this.personalProfile,
					practice: this.workingTime
				}
				setInformation(obj).then(res => {
					uni.showToast({title: res.msg,icon: "none"})
					setTimeout(() => {
						uni.navigateTo({
							url: "../personalInformation/personalInformation"
						})
					}, 700)
				})
			}
		}
	}
</script>

<style lang="less">
	page{background:#fff;}
	.essentialInformation {
		width: 100%;
		height: 200rpx;
		background: #0ea17d;

		.header {
			color: #FFFFFF;
			border: 1rpx solid #0ea17d;

			.header-h2 {
				margin-top: 48rpx;
				font-size: 40rpx;
				font-weight: bold;

				margin-left: 32rpx;
			}

			.header-text {
				margin-top: 8rpx;
				margin-left: 32rpx;
				font-size: 28rpx;
				font-weight: 400;
			}

		}

		.info {
			position: relative;
			box-sizing: border-box;
			padding: 0 32rpx;
			width: 750rpx;
			border-radius: 40rpx 40rpx 0px 0px;
			margin-top: 27rpx;
			background: #fff;
			border-top: 1rpx solid #fff;
			padding-bottom:60rpx;
			.height {
				.det {
					margin: 32rpx 0 16rpx 0;
					font-size: 32rpx;
					font-weight: bold;
				}

				.choiceHeight {
					box-sizing: border-box;
					display: flex;
					justify-content: space-between;
					align-items: center;
					height: 96rpx;
					line-height: 96rpx;
					font-size: 28rpx;
					font-weight: 400;
					color: #999999;
					background: #F7F8F9;
					border-radius: 16rpx;
					padding: 0 16rpx 0 28rpx;
				}
			}

			.personalProfile {
				width: 100%;

				.det {
					font-size: 32rpx;
					font-weight: bold;
					color: #000000;
					margin-top: 40rpx;
					margin-bottom: 16rpx;
				}

				.textarea {
					box-sizing: border-box;
					padding: 28rpx;
					width: 686rpx;
					height: 360rpx;
					background: #F7F8F9;
					border-radius: 16rpx;
				}
			}

		}

		.submit {
			width: 750rpx;
			height: 120rpx;
			background: #FFFFFF;

			view {
				width: 685rpx;
				height: 88rpx;
				background: #0ea17d;
				border-radius: 50rpx;
				margin: 0 auto;
				margin-bottom: 20rpx;
				text-align: center;
				line-height: 88rpx;
				font-size: 32rpx;
				font-weight: 400;
				color: #FFFFFF;
			}
		}
	}
</style>
