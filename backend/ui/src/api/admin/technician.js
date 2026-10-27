import request from '@/api/request'


//数据列表
export function index(params) {
	return request({ url: '/Technician/index',method: 'post',data: params})
}
//修改排序开关
export function updateExt(params) {
	return request({ url: '/Technician/updateExt',method: 'post',data: params})
}
//添加
export function add(params) {
	return request({ url: '/Technician/add',method: 'post',data: params})
}
//修改
export function update(params) {
	return request({ url: '/Technician/update',method: 'post',data: params})
}
export function getUpdateInfo(params) {
	return request({ url: '/Technician/getUpdateInfo',method: 'post',data: params})
}
//删除
export function del(params) {
	return request({ url: '/Technician/delete',method: 'post',data: params})
}
//查看详情
export function detail(params) {
	return request({ url: '/Technician/detail',method: 'post',data: params})
}
//单认证


export function updateAuth(params) {
	return request({ url: '/Technician/updateAuth',method: 'post',data: params})
}


export function updateTgrade(params) {
	return request({ url: '/Technician/updateTgrade',method: 'post',data: params})
}

export function updateFquantity(params) {
	return request({ url: '/Technician/updateFquantity',method: 'post',data: params})
}

export function updateWork(params) {
	return request({ url: '/Technician/updateWork',method: 'post',data: params})
}

//查看详情
export function count() {
	return request({ url: '/Technician/count',method: 'get'})
}