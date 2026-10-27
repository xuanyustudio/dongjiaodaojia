import request from "../../common/request.js"
// 忙时日期
export function busyDate(data) {
  return request({
    url: '/index/busyDate',
    method: 'POST',
    data
  })
}
//忙时
export function busyHour(data) {
  return request({
    url: '/index/busyHour',
    method: 'POST',
    data
  })
}

//设置忙时
export function setBusyHour(data) {
  return request({
    url: '/index/setBusyHour',
    method: 'POST',
    data
  })
}
