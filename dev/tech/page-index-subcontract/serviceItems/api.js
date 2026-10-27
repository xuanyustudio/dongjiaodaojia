import request from "../../common/request.js"
//设置服务项目
export function setService(data) {
  return request({
    url: '/my.Index/set_service',
    method: 'POST',
    data
  })
}
//得到服务项目
export function getService(data) {
  return request({
    url: '/my.Index/get_service',
    method: 'GET',
    data
  })
}



