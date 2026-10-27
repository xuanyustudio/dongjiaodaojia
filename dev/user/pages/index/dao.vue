<template>
	<view>
		<view v-if="trues" >
			<zNo></zNo>
		</view>
		<view class="ser"  v-for="(item,index) in 3" :key="index" @click="segr()">
			<view class="sertt">
				<view class="left">
					<view class="boxer">
						<image style="width: 100%;height: 180rpx;" src="../../static/demo/2.jpg"></image>
						<view style="font-size: 25rpx;font-weight: 600;">享享到家美容院</view>
						<view style="font-size: 15rpx; color: #999999 ;">青岛市南区山东路6号</view>
					</view>
				</view>
				<view class="left">
					<view class="boxer">
						<image style="width: 100%;height: 180rpx;" src="../../static/demo/2.jpg"></image>
						<view style="font-size: 25rpx;font-weight: 600;">享享到家美容院</view>
						<view style="font-size: 15rpx; color: #999999 ;">青岛市南区山东路6号</view>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	// 后台没有数据就引入xer.vue  ，后台接口给出数据就引入index.vue
	// import zNo from '@/components/z-no/index.vue'
	import zNo from '@/components/z-no/xer.vue'
	export default {
		data(){
			return{
				trues:true,
				dataList:[
					{name:"231"}
				],
				sert:[{name:"231"}]
			}
		},
		onLoad() {
			if( this.sert.length == this.dataList.length ){
				this.trues = false
			}
		},
		methods:{
			segr(){
				uni.navigateTo({
					url:'ji'
				})
			}
		},
		components:{
			zNo
		}
	}
</script>

<style scoped lang="scss">
	page{
		background-color: #f5f5f5;
	}
	.ser{
		width: 85%;
		margin: 40rpx auto;
	}
	.sertt{
		width: 100%;
		height: 300rpx;
		display: flex;
		justify-content: space-between;
	}
	.left{
		width: 47%;
		background-color: white;
		height: 100%;
		// height: 1;
	}
	.boxer{
		width: 90%;
		height: 90%;
		margin: 20rpx auto;
		display: flex;
		    flex-direction: column;
		    justify-content: space-between;
		    align-items: center;
	}
</style>