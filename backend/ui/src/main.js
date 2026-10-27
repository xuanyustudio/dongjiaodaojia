import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import '@/assets/scss/reset.scss'
import '@/assets/scss/index.scss'
import ElementUI from 'element-ui'
import VueCookies from 'vue-cookies'
import axios from 'axios'
import 'element-ui/lib/theme-chalk/index.css'
import 'element-ui/lib/theme-chalk/display.css';
import '@/assets/icon/iconfont.css'
import '@/assets/icon/iconfont.js'
import echarts from 'echarts'

import { parseTime,formatStr,checkPermission} from "@/utils/common.js"

import './permission'


Vue.use(VueCookies)
Vue.use(ElementUI)
Vue.prototype.$axios = axios
Vue.prototype.$echarts = echarts

Vue.config.productionTip = false
Vue.prototype.parseTime = parseTime
Vue.prototype.formatStr = formatStr
Vue.prototype.checkPermission = checkPermission

new Vue({
  router,
  store,
  render: h => h(App),
}).$mount('#app')
