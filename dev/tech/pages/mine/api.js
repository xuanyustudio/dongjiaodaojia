import request from "../../common/request.js"

// get openid
export function getOpenid(data) {
	console.log(data)
  return request({
    url: '/login/getOpenid',
    method: 'POST',
    data
  })
}
//获取个人信息
export function getMyIndex(data) {
	console.log(data,"打印的my.index")
  return request({
    url: '/my.Index',
    method: 'POST',
    data
  })
}
//设置头像
export function setAlbum(data) {
	console.log(data)
  return request({
    url: '/my.Index/setAlbum',
    method: 'POST',
    data
  })
}

//删除账号
export function clearUser(data) {
	console.log(data)
  return request({
    url: '/my.Index/deleteTechnician',
    method: 'POST',
    data
  })
}