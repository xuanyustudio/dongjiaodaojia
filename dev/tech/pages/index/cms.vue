<template>
	<view class="p-15">
		<u-skeleton rows="3" title :loading='loading'>
			<u-parse :content="data"></u-parse>
		</u-skeleton>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				loading: true,
				data:'',
				id:''
			}
		},
		onLoad(e) {
			if(e.id) this.id = e.id
			this.setNavTitle(this.id=='tech_privacy'?'隐私协议':'用户协议')
			this.init()
		},
		methods: {
			init(){
				this.$db.http('index/tcp',{field:this.id}).then(res => {
					if(res.code==1){
						this.data = res.data
						this.loading = false
					}
				})
			},
			//页面标题同步
			setNavTitle(e){
				uni.setNavigationBarTitle({
				　　title: e
				})
			},
		},

	}
</script>

<style lang="less">
	
</style>
