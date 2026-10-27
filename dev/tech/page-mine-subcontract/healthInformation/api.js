import request from "../../common/request.js"
// 健康证提交
export function submitCertificates(data) {
  return request({
    url: '/my.Index/setHealthy',
    method: 'POST',
    data
  })
}
//获取个人信息
export function getMyIndex(data) {
  return request({
    url: '/my.Index',
    method: 'POST',
    data
  })
}

