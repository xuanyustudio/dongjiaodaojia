<template>
	<view>
		<view v-if="trues" >
			<zNo></zNo>
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
				sert:[] // 里面如果有数据 就隐藏组件内容，如果没有信息就显示组件内容
			}
		},
		onLoad() {
			if( this.sert.length == this.dataList.length ){
				this.trues = false
			}
		},
		methods:{
			
		},
		components:{
			zNo
		}
	}
</script>

<style>
</style>