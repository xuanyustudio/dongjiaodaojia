import request from "../../common/request.js"
// 首页
export function signIn(data) {
  return request({
    url: '/index/signIn',
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