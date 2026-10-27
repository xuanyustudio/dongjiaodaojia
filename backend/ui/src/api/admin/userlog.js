import request from '@/api/request'


//数据列表
export function index(params) {
	return request({ url: '/Userlog/index',method: 'post',data: params})
}
//修改排序开关
export function updateExt(params) {
	return request({ url: '/Userlog/updateExt',method: 'post',data: params})
}
//添加
export function add(params) {
	return request({ url: '/Userlog/add',method: 'post',data: params})
}
//修改
export function update(params) {
	return request({ url: '/Userlog/update',method: 'post',data: params})
}
export function getUpdateInfo(params) {
	return request({ url: '/Userlog/getUpdateInfo',method: 'post',data: params})
}
//删除
export function del(params) {
	return request({ url: '/Userlog/delete',method: 'post',data: params})
}
//查看详情
export function detail(params) {
	return request({ url: '/Userlog/detail',method: 'post',data: params})
}
