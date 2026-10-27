<template>
	<view class="u-content">
		<u-parse :content="nodes"></u-parse>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				nodes:"",
				field: '',
				title: ''
			}
		},
		onLoad(options){
			let field = options.title;
			this.field = field;
			let title = '服务协议';
			title = field === 'service' ? '服务协议' : field === 'privacy' ? '隐私政策' : '关于我们';
			this.title = title;
			this.init();
		},
		onReady() {
			uni.setNavigationBarTitle({
				title: this.title
			});
		},
		methods: {
			init(){
				this.$http.post('api/user/config',{
					field: this.field
				}).then(res =>{
					this.nodes = res;
				})
			}
		}
	}
</script>

<style>
.u-content {
        padding: 24rpx;
    }
</style>
