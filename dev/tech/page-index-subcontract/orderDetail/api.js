import request from "../../common/request.js"
// 获取订单列表
export function orderDetails(data) {
  return request({
    url: '/order/orderDetails',
    method: 'POST',
    data
  })
}

