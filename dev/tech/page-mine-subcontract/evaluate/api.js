import request from "../../common/request.js"
// 获取评论类型
export function getType() {
  return request({
    url: '/my.Comment/get_type',
    method: 'GET',
  })
}
// 获取评论列表
export function getList(data) {
  return request({
    url: '/my.Comment/list',
    method: 'GET',
    data
  })
}


