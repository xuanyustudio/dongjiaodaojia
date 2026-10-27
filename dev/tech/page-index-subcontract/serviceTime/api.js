import request from "../../common/request.js"
// 设置服务时间
export function setServiceDate(data) {
  return request({
    url: '/my.Index/set_service_data',
    method: 'POST',
    data
  })
}

