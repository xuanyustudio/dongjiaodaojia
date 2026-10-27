import request from '@/api/request'


//数据列表
export function index(params) {
	return request({ url: '/Technicianapply/index',method: 'post',data: params})
}
//修改排序开关
export function updateExt(params) {
	return request({ url: '/Technicianapply/updateExt',method: 'post',data: params})
}
//添加
export function add(params) {
	return request({ url: '/Technicianapply/add',method: 'post',data: params})
}
//处理
export function update(params) {
	return request({ url: '/Technicianapply/update',method: 'post',data: params})
}
//删除
export function del(params) {
	return request({ url: '/Technicianapply/delete',method: 'post',data: params})
}
//查看详情
export function detail(params) {
	return request({ url: '/Technicianapply/detail',method: 'post',data: params})
}
