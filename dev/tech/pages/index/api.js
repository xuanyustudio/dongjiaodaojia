import request from "../../common/request.js"
import cfg from "../../common/config.js"
// 首页
export function index(data) {
  return request({
    url: '/index/index',
    method: 'POST',
    data
  })
}

// 平台客服电话，读本地配置
export function getPhone(data) {
  return uni.request({
    url: cfg.httpUrl + 'index/cs',
    method: 'GET',
    data
  })
}

// get openid
export function getOpenid(data) {
  return request({
    url: '/login/getOpenid',
    method: 'POST',
    data
  })
}

//切换状态
export function setStatus(data) {
  return request({
    url: '/index/status',
    method: 'POST',
    data
  })
}
//获取个人信息
export function getMyIndex(data) {
	console.log(data)
  return request({
    url: '/my.Index',
    method: 'POST',
    data
  })
}