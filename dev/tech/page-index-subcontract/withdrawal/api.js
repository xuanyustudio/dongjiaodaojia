import request from "../../common/request.js"
// 我的钱包
export function withdraw(data) {
  return request({
    url: '/index/withdraw',
    method: 'POST',
    data
  })
};
//申请提现
export function withdrawApply(data) {
  return request({
    url: '/index/withdrawApply',
    method: 'POST',
    data
  })
};