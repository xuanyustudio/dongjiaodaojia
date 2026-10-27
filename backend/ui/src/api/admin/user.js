import request from '@/api/request'


//数据列表
export function index(params) {
	return request({ url: '/User/index',method: 'post',data: params})
}
//修改排序开关
export function updateExt(params) {
	return request({ url: '/User/updateExt',method: 'post',data: params})
}
//添加
export function add(params) {
	return request({ url: '/User/add',method: 'post',data: params})
}
//修改
export function update(params) {
	return request({ url: '/User/update',method: 'post',data: params})
}
export function getUpdateInfo(params) {
	return request({ url: '/User/getUpdateInfo',method: 'post',data: params})
}
//删除
export function del(params) {
	return request({ url: '/User/delete',method: 'post',data: params})
}
//查看详情
export function detail(params) {
	return request({ url: '/User/detail',method: 'post',data: params})
}
//导出
export function dumpdata(params) {
	return request({ url: '/User/dumpdata',method: 'post',data: params})
}
