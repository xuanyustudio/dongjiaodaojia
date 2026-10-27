import request from '@/api/request'


//数据列表
export function index(params) {
	return request({ url: '/Order/index',method: 'post',data: params})
}
//修改排序开关
export function updateExt(params) {
	return request({ url: '/Order/updateExt',method: 'post',data: params})
}
//添加
export function add(params) {
	return request({ url: '/Order/add',method: 'post',data: params})
}
//修改
export function update(params) {
	return request({ url: '/Order/update',method: 'post',data: params})
}
export function getUpdateInfo(params) {
	return request({ url: '/Order/getUpdateInfo',method: 'post',data: params})
}
//删除
export function del(params) {
	return request({ url: '/Order/delete',method: 'post',data: params})
}
//查看详情
export function detail(params) {
	return request({ url: '/Order/detail',method: 'post',data: params})
}
//导出
export function dumpdata(params) {
	return request({ url: '/Order/dumpdata',method: 'post',data: params})
}

//查看详情
export function count() {
	return request({ url: '/Order/count',method: 'get'})
}

//退款
export function operateRefund(params) {
	return request({ url: '/Order/operateRefund',method: 'post',data: params})
}
