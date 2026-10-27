import request from "../../common/request.js"
// 提现记录
export function withdrawList(data) {
  return request({
    url: '/index/withdrawList',
    method: 'POST',
    data
  })
};
