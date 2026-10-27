import request from '@/api/request'


//数据列表
export function index(params) {
	return request({ url: '/Servicespec/index',method: 'post',data: params})
}
//修改排序开关
export function updateExt(params) {
	return request({ url: '/Servicespec/updateExt',method: 'post',data: params})
}
//添加
export function add(params) {
	return request({ url: '/Servicespec/add',method: 'post',data: params})
}
//修改
export function update(params) {
	return request({ url: '/Servicespec/update',method: 'post',data: params})
}
export function getUpdateInfo(params) {
	return request({ url: '/Servicespec/getUpdateInfo',method: 'post',data: params})
}
//删除
export function del(params) {
	return request({ url: '/Servicespec/delete',method: 'post',data: params})
}
//查看详情
export function detail(params) {
	return request({ url: '/Servicespec/detail',method: 'post',data: params})
}
//获取sql语句定义的字段信息
export function getFieldList(params) {
	return request({ url: '/Servicespec/getFieldList',method: 'post',data: params})
}
