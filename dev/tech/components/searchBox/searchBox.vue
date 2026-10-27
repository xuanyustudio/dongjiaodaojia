<template>
	<view class="searchBox">
		<uni-search-bar radius="16px" placeholder="搜索订单号" :radius="100" @confirm="search" @cancel="del" @clear="del"></uni-search-bar>
	</view>
</template>

<script>
	export default{
		props:{
			order_sn:{
				type:String
			}
		},
		data(){
			return{
				
			}
		},
		methods:{
			search(e){
				this.$emit("emitSN",e.value)
			},
			del(){
				this.$emit("emitSN",'')
			}
		}
	}
</script>

<style lang="less">
	.searchBox{
		width: 688rpx;
		border-radius: 32rpx;
		margin: 0 auto;
	}
</style>
