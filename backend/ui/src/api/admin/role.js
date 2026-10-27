import request from '@/api/request'


//数据列表
export function index(params) {
	return request({ url: '/Role/index',method: 'post',data: params})
}
//修改排序开关
export function updateExt(params) {
	return request({ url: '/Role/updateExt',method: 'post',data: params})
}
//添加
export function add(params) {
	return request({ url: '/Role/add',method: 'post',data: params})
}
//修改
export function update(params) {
	return request({ url: '/Role/update',method: 'post',data: params})
}
export function getUpdateInfo(params) {
	return request({ url: '/Role/getUpdateInfo',method: 'post',data: params})
}
//删除
export function del(params) {
	return request({ url: '/Role/delete',method: 'post',data: params})
}
