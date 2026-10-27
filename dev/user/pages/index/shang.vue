<template>
	<view>
		<!-- 可以赋值订单信息的数据   以及接口   -->
		<u-sticky bgColor="#fff">
		<u-tabs :current="tabsIndex" @click="click" style="background-color: #FFFFFF;" :list="list4" lineWidth="30" lineColor="#D30202" :activeStyle="{
								color: '#D30202',
								fontWeight: 'bold',
								transform: 'scale(1.05)'
							}" :inactiveStyle="{
								color: '#606266',
								transform: 'scale(1)'
							}" itemStyle="padding-left: 15px; padding-right: 15px; height: 34px;padding-bottom: 5px">
		</u-tabs>
		</u-sticky>
			<zNo></zNo>
	</view>
</template>

<script>
	// 后台没有数据就引入xer.vue  ，后台接口给出数据就引入index.vue
	// import zNo from '@/components/z-no/index.vue'
	import zNo from '@/components/z-no/xer.vue'
	export default {
		data(){
			return{
				total: null,
				tabsIndex: 0,
				list4: [{
					name: '皮肤管理',
					status: ''
				}, {
					name: '身体管理',
					status: 0,
				}, {
					name: '健康管理',
					status: 1,
				}],
			}
		},
		components:{
			zNo
		}
	}
</script>

<style scoped lang="scss">
	
</style>