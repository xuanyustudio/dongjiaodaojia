import request from "../../common/request.js"
export function login(data) {
  return request({
    url: '/login/login1',
    method: 'POST',
    data
  })
}
export function getOpenid(data) {
	console.log(data)
  return request({
    url: '/login/getOpenid',
    method: 'POST',
    data
  })
}
export function sms(data) {
	console.log(data)
  return request({
    // url: '/index/smsCode',
	url:'../api/user/smsCode',
    method: 'POST',
    data
  })
}