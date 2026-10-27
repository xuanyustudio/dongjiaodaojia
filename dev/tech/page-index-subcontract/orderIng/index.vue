<template>
	<view class="">
		<!-- 头部绿色背景 -->
		<view class="header-bg"></view>

		<uni-nav-bar statusBar class="navBar" left-icon="left" :height="'88rpx'" :backgroundColor="'none'"
			:fontSize="'36rpx'" :color="'#F4F4F4'" :border="false" :title="data.order_status==3?'服务进行中':data.order_status==4?'服务结束':''" @clickLeft="back"></uni-nav-bar>

		<view class="p-15 p-b40 position-relative">
			<view class="f-s20 f-w700 co_fff">{{data.order_status==3?'服务已开始，请认真工作':data.order_status==4?'本次服务已结束':''}}</view>
			<view class="dd-flex p-t10">
				<text class="co_fff f-s14">服务{{data.order_status==3?'已':''}}用时</text>
				<view class="tips_time">
					<text style="margin-left: 16rpx" class="time">{{ hour }}</text>
					<text class="spot">:</text> <text class="time">{{ minutes }}</text>
					<text class="spot">:</text> <text class="time">{{ seconds }}</text>
				</view>
				<view class="flex1"></view>
			</view>
		</view>
		
		<view class="flex1 bg_fff position-relative d-flex">
			<!-- 综合评分 -->
			<view class="flex1 bg_fff d-flex flex-direction-column" style="border-radius:40rpx;margin-top:-32rpx;">
				<view class="p-15">
					<view class="dd-flex">
						<image :src="data.avatar" mode="" class="img border-radius-100" style="width:88rpx;height:88rpx;"></image>
						<view class="flex1 p-l10">
							<view class="f-s14 f-w700">尾号{{data&&data.username.substr(7)}}</view>
							<view class="f-s12 co_666 p-t3">信用分{{data.praise}} | 好评率{{data.praise}}%</view>
						</view>
						<image @tap="call(data.username)" :src="imgUrl+'images/orderImg/img03.png'" mode="" class="img" style="width:80rpx;height:80rpx;"></image>
					</view>
					<view class="d-flex align-items-center p-t20">
						<view class="f-s16 f-w700">服务时间{{data&&data.appointment_time.substr(10)}}</view>
						<view class="f-s12 bg_000 co_fff border-radius-3 line-h150 p-l5 p-r5 m-l15">时长</view>
						<view class="f-s14 co_666 p-l3">{{data.duration}}分钟</view>
					</view>
					<view class="d-flex justify-content-space-between f-s12 p-t5 p-b15">
						<view class="p-r20 co_666">服务地址：{{data.province}}{{data.city}}{{data.district}}{{data.address}}</view>
						<text class="f-s12 co_999">{{data.distance}}km</text>
					</view>
					<view class="dd-flex p-t20 p-b20" style="border-top:1px solid rgba(112,112,112,.12)">
						<view class="border-radius-100 co_fff f-s12 text-center" style="width:88rpx;line-height:40rpx;background:#7C94FB;border-bottom-right-radius:0!important;">SPA</view>
						<view class="f-s16 f-w700 p-l5 flex1 p-r20 webkit-line-clamp-1">{{data.service_name}}</view>
						<view class="dd-flex f-w700 co_000 f-s20">
							<view class="f-s12 co_666 f-w400 p-t5">预估收入</view>
							<view class="f-s14 p-t3">￥</view>
							<view>{{data.technician_money}}</view>
						</view>
					</view>
					<view class="dd-flex p-t20 p-b20" style="border-top:1px solid rgba(112,112,112,.12)">
						<view class="flex1">
							<view class="f-s16 f-w700">遇到危险，紧急求助</view>
							<view class="f-s12 co_999 p-t5">如果目前不能继续接单，请先暂停在线状态</view>
						</view>
						<view><button type="default" class="regiter-bottom orange border-radius-100 h28 f-s14 p-l25 p-r25" @tap="baojing">报警</button></view>
					</view>
				</view>
			</view>
		</view>
		
		<u-modal :show="show" :showCancelButton='true' :content='content' @cancel="show=false" @confirm="confirm"></u-modal>
		
		<view class="tool-fixed bg_fff p-15 d-flex" style="box-shadow:0 -8rpx 16px rgba(0, 0, 0, .06);">
			<view class="regiter-bottom border-radius-6 flex1" id="fuwu" @tap="cancel" v-if="data.order_status==3">服务结束</view>
			<template v-if="data.order_status==4">
				<view class="regiter-bottom gray border-radius-6 flex1">服务已结束</view>
				<view class="regiter-bottom mintr border-radius-6 p-l20 p-r20 m-l10" @click="cancel" id="quxiao">去投诉</view>
			</template>
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
				
				show:false,
				content:'',
				id:'',
				
				order_sn:'O2022030349995051',
				data:'',
				
				hour: "00",
				minutes: "00",
				seconds: "58",
				timer: null,
			}
		},
		onLoad(e) {
			clearTimeout(this.timer);
			this.timer = null;
			if(e.order_sn) this.order_sn = e.order_sn
		},
		onShow() {
			clearTimeout(this.timer);
			this.timer = null;
			
			this.init()
		},
		onHide() {
			clearTimeout(this.timer);
			this.timer = null;
		},
		onUnload(){
			clearTimeout(this.timer);
			this.timer = null;
		},
		methods: {
			getTime() {
				this.seconds = this.seconds++ > 8 ? this.seconds : "0" + this.seconds;
				if (this.seconds > 59) {
					this.seconds = "00";
					this.minutes = this.minutes++ > 9 ? this.minutes : "0" + this.minutes;
					if (this.minutes > 59) {
						this.hour = this.hour++ > 9 ? this.hour : "0" + this.hour;
					}
				}
			},
			
			sjc(e){
				let a = Math.abs(Math.round(new Date()) - e*1000),
					h = parseInt((a % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)),
					m = parseInt((a % (1000 * 60 * 60)) / (1000 * 60)),
					s = ((a % (1000 * 60)) / 1000).toFixed(0)
				this.hour = h<10 ? '0'+h : h
				this.minutes = m<10 ? '0'+m : m
				this.seconds = s<10 ? '0'+s : s
			},
			sjc2(e){
				let a = e,
					h = parseInt((a % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)),
					m = parseInt((a % (1000 * 60 * 60)) / (1000 * 60)),
					s = ((a % (1000 * 60)) / 1000).toFixed(0)
				this.hour = h<10 ? '0'+h : h
				this.minutes = m<10 ? '0'+m : m
				this.seconds = s<10 ? '0'+s : s
			},
			
			init(){
				uni.showLoading({title:'加载中'})
				this.$db.http('order/orderDetails',{order_sn:this.order_sn}).then(res => {
					if(res.code==1){
						uni.hideLoading()
						this.data = res.data
						
						if(res.data.order_status<4){
							this.sjc(res.data.service_time)
							this.timer = setInterval(() => {
								this.getTime()
							}, 1000)
						}else{
							let a = res[1].data.data.complete_time
							let service_time = res[1].data.data.service_time
							console.log(Date.parse(a),'aa')
							console.log(service_time,'service_time')
							let htmls = Date.parse(a) - (service_time*1000)
							console.log(htmls,"htmls")
							this.sjc2(htmls)
							clearTimeout(this.timer);
							this.timer = null;
						}
					}
				})
			},
			
			confirm(){
				this.show = false
				if(this.id=='fuwu'){
					uni.showLoading({
						title:'加载中'
					})
					this.$db.http('order/completeOrder',{order_sn:this.order_sn}).then(res => {
						if(res.code==1){
							uni.showToast({title:'服务已结束'})
							clearTimeout(this.timer);
							this.timer = null;
							
							setTimeout(()=>{
								this.url('/page-index-subcontract/orderOver/index?order_sn='+this.order_sn)
							},1000)
							// this.yy = res.data
						}
					})
					// this.url('')
				}
			},
			
			cancel(e){
				let {
					id
				} = e.target;
				this.id = id;
				if(id=='fuwu'){
					this.content = '确认服务已经结束？'
				}
				this.show = true
			},
			
			baojing(){
				uni.navigateTo({
					url:'/page-index-subcontract/emergencyHelp/emergencyHelp'
				})
			},
			
			back() {
				uni.navigateBack()
			},
			
			url(e){
				uni.navigateTo({
					url: e
				})
			},
			
			call(e){
				uni.makePhoneCall({
					phoneNumber: e
				});
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
	
	.tips_time {
		font-size: 28rpx;
		font-weight: 400;
		color: #ffffff;
	
		.time {
			font-size: 28rpx;
			padding: 6rpx 10rpx;
			font-weight: bold;
			line-height: 0px;
			color: #46d17e;
			opacity: 1;
			background: #ffffff;
			opacity: 1;
			border-radius: 8rpx;
		}
	
		.spot {
			font-size: 28rpx;
			color: #9fe7bc;
			margin: 0 8rpx;
		}
	}
</style>
