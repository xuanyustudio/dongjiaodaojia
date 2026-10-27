import request from "../../common/request.js"
// 确认签署协议
export function getNewList(data) {
  return request({
    url: '/news/index',
    method: 'POST',
    data
  })
}

