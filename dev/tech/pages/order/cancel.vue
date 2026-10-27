<template>
	<view class="dd-flex flex-direction-column" style="min-height:calc(100vh - 44px);">
		<view class="p-t50 p-r50 p-l50 m-l10 m-r10">
			<view class="">
				<image :src="imgUrl+'images/orderImg/img01.png'" mode="widthFix" class="img" style="width:100%;"></image>
			</view>
			
			<template v-if="show">			
				<view class="f-s20 line-h150 p-t20 f-w700">取消订单将会降低您的信用分建议尽量不要取消</view>
				<view class="d-flex p-t10">
					<button type="default" class="p-0 f-s14 co_666" style="background:none;">取消规则 >></button>
				</view>
				<view class="p-t40 d-flex">
					<button type="default" class="flex1 regiter-bottom gray border-radius-8" @tap="show=false">确认取消</button>
					<button type="default" class="flex1 regiter-bottom border-radius-8 m-l15" @tap="back">暂不取消</button>
				</view>
			</template>
			
			<template v-else>
				<view class="f-s20 line-h150 p-t20 f-w700">订单已取消</view>
				<view class="co_666 f-s14 p-t5">如果目前不能继续接单，请先暂停在线状态</view>
				<view class="f-s16 f-w700 p-t30">请告知取消原因</view>
				<view :class="['bg_f8f8f8 f-s14 p-10 border-radius-6 m-t10 line-h170',{on:yyIndex==i}]" v-for="(item,i) in yy" :key="i" @tap="yyIndex=i">{{item.name}}</view>
				<view class="d-flex m-t10">
					<textarea v-model="text" class="flex1 bg_f8f8f8 p-10 border-radius-6 f-s14" maxlength="50" placeholder="请输入其他原因，最多50字" placeholder-class="co_ccc" style="height:132rpx" />
				</view>
				<view class="m-t10">
					<button type="default" class="regiter-bottom border-radius-6" @tap="submit">提交</button>
				</view>
			</template>
		</view>
		<view class="p-b50 p-r50 p-l50 m-l10 m-r10"></view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				imgUrl: this.$store.state.imgUrl,
				
				order_sn:'',
				show: true,
				
				yy:[],
				yyIndex:null,
				text:''
			}
		},
		onLoad(e) {
			if(e.order_sn) this.order_sn = e.order_sn;
			this.init()
		},
		methods: {
			init(){
				this.$db.http('order/cancelReason').then(res => {
					if(res.code==1){
						this.yy = res.data
					}
				})
			},
			
			// 返回上一级
			back() {
				uni.navigateBack({
					delta: 1,
				});
			},
			
			submit(){
				if(this.yyIndex == null && !this.text ){ uni.showToast({title: '请选择取消的原因',icon:'none'}); return;}
				
				let data = {
					order_sn: this.order_sn
				}
				if(this.yyIndex == null) data['cancel_reason'] = this.text
				else data['cancel_reason'] = this.yy[this.yyIndex].name+'，'+this.text
				// console.log(data)
				uni.showLoading({title:'提交中'})
				this.$db.http('order/cancelOrder',data).then(res => {
					if(res.code==1){
						uni.showToast({title:'已取消成功'})
						setTimeout(()=>{
							uni.navigateBack()
						},1000)
						// this.yy = res.data
					}
				})
			}
		}
	}
</script>

<style lang="less">
	page{background:#fff;}
	.on{
		position: relative; color:#46D17E;
		&:after{content:''; position: absolute; top:0;left:0;bottom:0;right:0;border:1px solid #46D17E; border-radius:12rpx;}
	}
</style>
