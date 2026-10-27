import request from "../../common/request.js"
// 身份认证
export function submitCertificates(data) {
  return request({
    url: '/my.Index/setIdentity',
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

