<script>
	import {
		index
	} from "pages/index/api.js"

	export default {
		onLaunch: function() {
			let token = uni.getStorageSync("token")
			this.$store.commit("changeToken", token)

		},

		onShow: function() {},
		onHide: function() {}

	}
</script>
<style lang="scss">
	@import "@/uni_modules/uview-ui/index.scss"; 
@import "./tm-vuetify/mian.min.css";
	/*每个页面公共css */
	// @import "./tm-vuetify/mian.min.css";
	// @import "./tm-vuetify/scss/theme.css";

	* {
		margin: 0;
		padding: 0;
	}
	
	.uni-tabbar{box-shadow: 0px -4px 10px rgba(0, 0, 0, 0.06);}
	
	.flex1{ flex:1}
	.img{ vertical-align: top;}

	page {
		width: 750rpx;
		// height: 100%;
		background: #F8F8F8;
	}
</style>
