<template>
	<view class="page">
		<!-- <z-nav-bar title="个人信息" bgColor="#F5f5f5"></z-nav-bar> -->
		<view class="cell_list" @click="onUnloadImg">
			<view class="cell_left txt">头像</view>
			<view class="cell_right">
				<image :src="avatar" mode="aspectFill"></image>
			</view>
		</view>
		<z-prompt :value="nickname" text="请输入昵称" @confirm="onNameChange" :options="popupOptions">
			<view class="cell_list">
				<view class="cell_left txt">昵称</view>
				<view class="cell_right arrow">{{ nickname }}</view>
			</view>
		</z-prompt>
		
		<!-- <view class="cell_list" @click="onUnloadImg">
			<view class="cell_left txt">性别</view>
			<view class="cell_right">
				<u-icon name="arrow-right" size="14"></u-icon>
			</view>
		</view> -->
		<!-- <z-prompt :value="phone" text="请输入手机号" @confirm="onPhoneChange" :options="popupOptions">
			<view class="cell_list">
				<view class="cell_left txt">手机号</view>
				<view class="cell_right arrow">{{ phone }}</view>
			</view>
		</z-prompt> -->
		<!-- 按钮 -->
		<view class="form_but"><button class="active" @click="logout">退出登录</button></view>
	</view>
</template>

<script>
	import { uploadImage } from '@/config/utils'
	import {
		mapState,
		mapMutations
	} from 'vuex';
	export default {
		data() {
			return {
				popupOptions: {
					placeholder: ''
				},
				avatar: '',
				nickname: '',
				phone: ""
			};
		},
		computed: {
			...mapState(['userInfo'])
		},
		//第一次加载
		onLoad(e) {
			// 获取用户信息
			this.$http.post('api/user/userInfo').then(res =>{
				console.log(res)
				for( let i in res ){
					this[i] = res[i];
					
				}
			})
			// this.avatar = this.userInfo.avatar || "";
			// this.nickname = this.userInfo.nickname || "";
			// this.phone = this.userInfo.phone || "";
		},
		//页面显示
		onShow() {},
		//方法
		methods: {
			...mapMutations(['setUserInfo']),
			//修改昵称
			onNameChange(e) {
				this.nickname = e.value;
				if( this.nickname ){
					this.$http.post('api/user/editUser',{
						field: 'nickname',
						value: this.nickname
					}).then(res =>{
						uni.showToast({
							icon:'success',
							title:'修改成功'
						})
					})
					e.close();
					return
				}
				uni.showToast({
					title:'请输入昵称',
					icon:'none'
				})
				
				
			},
			//修改手机号
			onPhoneChange(e) {
				if (!this.$base.phoneRegular.test(e.value)) {
					uni.showToast({
						title: '请输入正确的手机号',
						icon: 'none'
					});
					return;
				}
				this.phone = e.value;
				e.close();
			},
			upload(){
				
			},
			//修改头像
			onUnloadImg() {
				uni.chooseImage({
					count:1,
					success: (res) => {
						let { tempFilePaths } = res;
						let file = tempFilePaths[0];
						uploadImage(file).then(res =>{
							// this.images.push(res);
							this.avatar = res;
							this.$http.post('api/user/editUser',{
								field: 'avatar',
								value: this.avatar
							}).then(res =>{
								uni.showToast({
									icon:'success',
									title:'修改成功'
								})
							})
							
						})
					}
				})
			},
			//提交
			logout(){},
		},
		//页面隐藏
		onHide() {},
		//页面卸载
		onUnload() {},
		//页面下来刷新
		onPullDownRefresh() {},
		//页面上拉触底
		onReachBottom() {},
		//用户点击分享
		onShareAppMessage(e) {
			return this.wxShare();
		}
	};
</script>
<style lang="scss" scoped>
	@import '@/style/mixin.scss';
	.cell_list {
		padding: 30rpx 30rpx;
		margin: 20rpx 30rpx 0 30rpx;
		border-radius: 8rpx;
	}

	.cell_right image {
		width: 140rpx;
		height: 140rpx;
		border-radius: 50%;
	}
</style>
