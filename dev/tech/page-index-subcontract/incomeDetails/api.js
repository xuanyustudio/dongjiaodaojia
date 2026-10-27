import request from "../../common/request.js"
// 获取收入明细
export function incomeList(data) {
  return request({
    url: '/index/incomeList',
    method: 'POST',
    data
  })
};
// 收入统计
export function incomeStatistics(data) {
  return request({
    url: '/index/incomeStatistics',
    method: 'POST',
    data
  })
}