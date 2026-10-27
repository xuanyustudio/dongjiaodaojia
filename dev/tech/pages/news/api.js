import request from "../../common/request.js"
// 消息列表
export function getNewList(data) {
  return request({
    url: '/news/index',
    method: 'POST',
    data
  })
}

