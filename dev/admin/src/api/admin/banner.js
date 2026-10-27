import request from '@/api/request'


//数据列表
export function index(params) {
	return request({ url: '/Banner/index',method: 'post',data: params})
}
//修改排序开关
export function updateExt(params) {
	return request({ url: '/Banner/updateExt',method: 'post',data: params})
}
//添加
export function add(params) {
	return request({ url: '/Banner/add',method: 'post',data: params})
}
//修改
export function update(params) {
	return request({ url: '/Banner/update',method: 'post',data: params})
}
export function getUpdateInfo(params) {
	return request({ url: '/Banner/getUpdateInfo',method: 'post',data: params})
}
//删除
export function del(params) {
	return request({ url: '/Banner/delete',method: 'post',data: params})
}
//查看详情
export function detail(params) {
	return request({ url: '/Banner/detail',method: 'post',data: params})
}
//导入
export function importData(params) {
	return request({ url: '/Banner/importData',method: 'post',data: params})
}
//导出
export function dumpdata(params) {
	return request({ url: '/Banner/dumpdata',method: 'post',data: params})
}
