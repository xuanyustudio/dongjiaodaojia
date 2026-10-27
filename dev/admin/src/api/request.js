import axios from 'axios'
import { ElMessage as Message, ElMessageBox as MessageBox } from 'element-plus'
import store from '../store'

axios.defaults.baseURL = '/public/index.php/admin'
axios.defaults.timeout = 20000
axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded'

axios.interceptors.request.use((config) => {
  const token = localStorage.getItem('adminToken')
  if (token) config.headers.Authorization = token
  if (typeof FormData !== 'undefined' && config.data instanceof FormData) {
    if (config.headers && typeof config.headers.set === 'function') {
      config.headers.set('Content-Type', false)
    } else if (config.headers) {
      delete config.headers['Content-Type']
    }
  }
  return config
}, (error) => Promise.reject(error))

axios.interceptors.response.use((res) => {
  const ret = res.data
  if (ret.status !== 200 && !ret.key && !ret.code) {
    if (ret.status == 101) {
      MessageBox.confirm(ret.msg, '系统提示', {
        confirmButtonText: '重新登录',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        store.dispatch('logout').then(() => {
          location.href = '/'
        })
      })
    } else {
      Message({
        showClose: true,
        message: ret.msg || 'Server error',
        type: 'error',
        duration: 2000
      })
    }
    return Promise.reject(ret)
  }
  return res
}, (error) => {
  Message({
    showClose: true,
    message: error.message,
    type: 'error',
    duration: 2000
  })
  return Promise.reject(error)
})

function get(url, data) {
  return axios.get(url, data).then(res => res.data)
}

function post(url, params) {
  return axios.post(url, params).then(res => res.data)
}

export function request({ url, method, data }) {
  if (method == 'get') return get(url, data)
  if (method == 'post') return post(url, data)
}

export default request
