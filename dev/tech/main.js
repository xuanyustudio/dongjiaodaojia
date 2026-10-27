import App from './App'
import uView from '@/uni_modules/uview-ui'
//引入vuex
import store from './store'
import Db from './store/db'
import cfg from 'common/config.js'

import tmVuetify from "./tm-vuetify"; 
Vue.use(tmVuetify)

Vue.use(uView)
//把vuex定义成全局组件
Vue.prototype.$store = store
Vue.prototype.$db = Db;
Vue.prototype.$cfg = cfg;
// uni.getStorage({
// 	key: 'token',
// 	success: function(res) {
// 		if (res.data) {
// 			store.commit("changeToken",res.data)
// 		}
// 	}
// });
//封装的手机号验证码验证
import check from './store/check/index.js'
Vue.prototype.check=check

uni.getStorage({
	key: 'token',
	success: function(res) {
		if (res.data) {
			store.commit("changeOpenid",res.data)
		}
	}
});
// #ifndef VUE3
import Vue from 'vue'
Vue.config.productionTip = false
App.mpType = 'app'
const app = new Vue({
	...App,
	store
})
app.$mount()
// #endif

// #ifdef VUE3
import {
	createSSRApp
} from 'vue'
export function createApp() {
	const app = createSSRApp(App)
	return {
		app
	}
}
// #endif
