import request from "../../common/request.js"
// 我的钱包
export function wallet(data) {
  return request({
    url: '/index/wallet',
    method: 'POST',
    data
  })
};