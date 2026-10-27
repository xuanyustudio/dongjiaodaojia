import request from "../../common/request.js"

//获取个人信息
export function getMyIndex(data) {
  return request({
    url: '/my.Index',
    method: 'POST',
    data
  })
}

