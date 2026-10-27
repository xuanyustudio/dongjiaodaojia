import request from "../../common/request.js"
// 基本信息
export function getUserInfo(data) {
  return request({
    url: '/my.Index',
    method: 'POST',
    data
  })
}
// 设置基本信息
export function setInformation(data) {
  return request({
    url: '/my.Index/setData',
    method: 'POST',
    data
  })
}

