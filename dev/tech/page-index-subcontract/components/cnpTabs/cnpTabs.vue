<template>
	<view class="tabs">
		<u-tabs @click="changeIndex" :list="list" :duration="100" lineWidth="30" lineColor="#46D17E" :activeStyle="{
		   						color: '#000',
		   						fontWeight: '500',
		   						transform: 'scale(1.02)'
		   					}" :inactiveStyle="{
		   						color: '#666',
		   						transform: 'scale(1)'
		   					}" :itemStyle="{'height':'88rpx','font-size':'28rpx','width':itemWidth}">
		</u-tabs>
	</view>
</template>

<script>
	export default{
		props:{
			list:{
				type:Array
			},
			itemWidth:{
				type:String
			}
		},
		data(){
			return{
			
			}
		},
		methods:{
			changeIndex(item) {
				this.$emit("changeCurrent",item)
			
			}
		}
	}
</script>

<style lang="less">
	.tabs {
		width: 750rpx;
		height: 88rpx;
		background: #fff;
		display: flex;
		align-items: center;
	}
</style>
