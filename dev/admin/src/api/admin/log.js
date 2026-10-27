import request from '@/api/request'


//数据列表
export function index(params) {
	return request({ url: '/Log/index',method: 'post',data: params})
}
//删除
export function del(params) {
	return request({ url: '/Log/delete',method: 'post',data: params})
}
//查看详情
export function detail(params) {
	return request({ url: '/Log/detail',method: 'post',data: params})
}
//导出
export function dumpdata(params) {
	return request({ url: '/Log/dumpdata',method: 'post',data: params})
}
