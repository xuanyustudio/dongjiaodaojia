import request from "../../common/request.js"
// 获取订单列表
export function orderList(data) {
  return request({
    url: '/order/orderList',
    method: 'POST',
    data
  })
}

