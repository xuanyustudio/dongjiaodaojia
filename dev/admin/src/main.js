import { createApp } from 'vue'
import ElementPlus from 'element-plus'
import zhCn from 'element-plus/es/locale/lang/zh-cn'
import 'element-plus/dist/index.css'
import '@/assets/element-icons/icon.css'
import App from './App.vue'
import router from './router'
import store from './store'
import '@/assets/scss/reset.scss'
import '@/assets/scss/index.scss'
import '@/assets/icon/iconfont.css'
import '@/assets/icon/iconfont.js'
import * as echarts from 'echarts'
import 'echarts/theme/macarons.js'
import { parseTime, formatStr, checkPermission } from '@/utils/common.js'
import cookies from '@/plugins/cookies'
import axios from 'axios'
import '@/api/request'
import VMdEditor from '@kangc/v-md-editor'
import '@kangc/v-md-editor/lib/style/base-editor.css'
import vuepressTheme from '@kangc/v-md-editor/lib/theme/vuepress.js'
import '@kangc/v-md-editor/lib/theme/style/vuepress.css'
import './permission'
import ElRow from '@/components/fix/Row.vue'

VMdEditor.use(vuepressTheme)

const app = createApp(App)
app.use(store)
app.use(router)
app.use(ElementPlus, { locale: zhCn })
app.component('ElRow', ElRow)
app.use(VMdEditor)
app.config.globalProperties.$axios = axios
app.config.globalProperties.$echarts = echarts
app.config.globalProperties.$cookies = cookies
app.config.globalProperties.parseTime = parseTime
app.config.globalProperties.formatStr = formatStr
app.config.globalProperties.checkPermission = checkPermission
app.mount('#app')
