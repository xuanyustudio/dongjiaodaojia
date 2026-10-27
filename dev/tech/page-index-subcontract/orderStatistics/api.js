import request from "../../common/request.js"
// 订单统计
export function orderStatistics(data) {
  return request({
    url: '/index/orderStatistics',
    method: 'POST',
    data
  })
};