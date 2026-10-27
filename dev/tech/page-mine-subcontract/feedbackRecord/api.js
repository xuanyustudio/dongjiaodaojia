import request from "../../common/request.js"
// 意见反馈提交
export function feedbackList(data) {
  return request({
    url: '/my.Opinion/list',
    method: 'GET',
    data
  })
}

