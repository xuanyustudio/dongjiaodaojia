import request from '@/api/request'


//数据列表
export function index(params) {
	return request({ url: '/Member/index',method: 'post',data: params})
}
//修改排序开关
export function updateExt(params) {
	return request({ url: '/Member/updateExt',method: 'post',data: params})
}
//添加
export function add(params) {
	return request({ url: '/Member/add',method: 'post',data: params})
}
//修改
export function update(params) {
	return request({ url: '/Member/update',method: 'post',data: params})
}
export function getUpdateInfo(params) {
	return request({ url: '/Member/getUpdateInfo',method: 'post',data: params})
}
//删除
export function del(params) {
	return request({ url: '/Member/delete',method: 'post',data: params})
}
//查看详情
export function detail(params) {
	return request({ url: '/Member/detail',method: 'post',data: params})
}
