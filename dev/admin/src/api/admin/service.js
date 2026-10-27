import request from '@/api/request'


//数据列表
export function index(params) {
	return request({ url: '/Service/index',method: 'post',data: params})
}
//修改排序开关
export function updateExt(params) {
	return request({ url: '/Service/updateExt',method: 'post',data: params})
}
//添加
export function add(params) {
	return request({ url: '/Service/add',method: 'post',data: params})
}
//修改
export function update(params) {
	return request({ url: '/Service/update',method: 'post',data: params})
}
export function getUpdateInfo(params) {
	return request({ url: '/Service/getUpdateInfo',method: 'post',data: params})
}
//删除
export function del(params) {
	return request({ url: '/Service/delete',method: 'post',data: params})
}
//查看详情
export function detail(params) {
	return request({ url: '/Service/detail',method: 'post',data: params})
}
