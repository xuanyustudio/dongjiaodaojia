import request from "../../common/request.js"
// 录音报警
export function callPolice(data) {
  return request({
    url: '/index/callPolice',
    method: 'POST',
    data
  })
}

