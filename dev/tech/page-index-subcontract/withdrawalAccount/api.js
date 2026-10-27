import request from "../../common/request.js"
// 保存账户
export function aliBind(data) {
  return request({
    url: '/index/aliBind',
    method: 'POST',
    data
  })
};

// 我的钱包
export function withdraw(data) {
  return request({
    url: '/index/withdraw',
    method: 'POST',
    data
  })
};