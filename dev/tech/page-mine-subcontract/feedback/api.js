import request from "../../common/request.js"
// 意见反馈提交
export function feedbackAdd(data) {
  return request({
    url: '/my.Opinion/add',
    method: 'POST',
    data
  })
}

