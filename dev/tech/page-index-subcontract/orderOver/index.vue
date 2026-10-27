<template>
	<view class="">
		<!-- 头部绿色背景 -->
		<view class="header-bg"></view>

		<uni-nav-bar statusBar class="navBar" left-icon="left" :height="'88rpx'" :backgroundColor="'none'"
			:fontSize="'36rpx'" :color="'#F4F4F4'" :border="false" title="服务结束" @clickLeft="back"></uni-nav-bar>

		<view class="p-15 p-b40 position-relative">
			<view class="f-s20 f-w700 co_fff d-flex align-items-center"><image :src="imgUrl+'images/orderImg/img05.png'" mode="" class="img m-r5" style="width:40rpx;height:40rpx;"></image> 本次服务已结束</view>
			<view class="d-flex align-items-center p-t10 co_fff">
				<view class="dd-flex f-w700 f-s20">
					<view class="f-s12 f-w400 p-t3">预估收入</view>
					<view class="f-s14 p-t3">￥</view>
					<view>{{data.technician_money}}</view>
				</view>
				<view class="f-s12 p-t3">（包含出行费{{data.fare}}元）</view>
			</view>
		</view>
		
		<view class="flex1 bg_fff position-relative d-flex">
			<!-- 综合评分 -->
			<view class="flex1 bg_fff" style="border-radius:40rpx;margin-top:-32rpx;">
				<view class="p-15 dd-flex">
					<view class="flex1 f-w700">对本次客户的评价</view>
					<view class="dd-flex" @tap="navigateTo('/page-index-subcontract/complaint/index?order_sn='+order_sn)"><view class="f-s14 co_666 p-r10">去投诉</view> <image :src="imgUrl+'images/indexImg/slices_two/jiantou.png'" mode="widthFix" class="img" style="width:14rpx;"></image></view>
				</view>
				<view class="p-15 p-t30">
					<view class="dd-flex">
						<u-rate :count="count" v-model="value" activeColor="#FFB83C" size="38" allowHalf :disabled="cDis" disabledColor="#FFB83C"></u-rate>
					</view>
					<view class="f-s14 co_999 text-center p-t10">您的宝贵评价，对我们非常重要</view>
				</view>
			</view>
		</view>
		
		<view class="tool-fixed bg_fff p-15" style="box-shadow:0 -8rpx 16px rgba(0, 0, 0, .06);">
			<button type="default" class="regiter-bottom gray border-radius-6" v-if="cDis">已评价</button>
			<button type="default" class="regiter-bottom border-radius-6" @tap="submit" v-else>提交</button>
			<view class="b-liuhai"></view>
		</view>
		<view class="b-liuhai" style="height:148rpx;"></view>
	</view>
</template>

<script>
	// import {headHeight,getOpenid} from "./api.js"
	export default {
		components: {},
		data() {
			return {
				imgUrl: this.$store.state.imgUrl,
				
				order_sn:'O2022030349995051',
				data:'',
				
				count: 5,
				value: 4,
				cDis: false
			}
		},
		onLoad(e) {
			if(e.order_sn) this.order_sn = e.order_sn
		},
		onShow() {
			this.init()
		},
		methods: {
			init(){
				uni.showLoading({title:'加载中'})
				this.$db.http('order/orderDetails',{order_sn: this.order_sn}).then(res => {
					if(res.code==1){
						uni.hideLoading()
						this.data = res.data
						console.log('查看评价',this.data)
						
						if(res.data.praise != '0.0'){
							this.value = res.data.praise;
							this.cDis = true
							
							console.log('星星数',this.value)
						}
					}
				})
			},
			submit(){
				uni.showLoading({title:'正在提交'})
				let data = {
					order_id: this.order_sn,
					score:this.value
				}
				console.log('客户评论提交',data)
				// console.log('token值',store.state.token) 
				this.$db.http('order/comment',data).then(res => {
					if(res.code==1){
						uni.showToast({title:'评价成功'})
						setTimeout(()=>{
							this.cDis = true
							uni.navigateBack({
							   delta: 5//向上多少写几
							});
						},2000)
					}
				})
			},
			navigateTo(e){
				uni.navigateTo({
					url:e
				})
			},
			back() {
				uni.navigateBack()
			}
		}
	}
</script>

<style lang="less">
	page {
		background: #fff;
	}

	// 头部绿色背景
	.header-bg {position: absolute; left: 0;top: 0;right: 0;height: 600rpx;background: #46D17E;}

	.item {
		position: relative;width: 31%;margin-right: 3%;margin-bottom: 3%;border-radius: 16rpx;overflow: hidden;
		&:nth-child(3n) {margin-right: 0;}
		image {width: 100%;height: 256rpx;}
		.del {position: absolute;left: 0;right: 0;bottom: 0;text-align: center;color: #fff;line-height: 56rpx;font-size: 24rpx;background: rgba(0, 0, 0, .45)}
	}

	.item2 {border: 1px dashed #979797;box-sizing: border-box;height: 256rpx;}
</style>
