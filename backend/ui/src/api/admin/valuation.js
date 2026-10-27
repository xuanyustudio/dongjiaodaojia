import request from '@/api/request'


//出行费用
export function index(params) {
	return request({ url: '/Valuation/index',method: 'post',data: params})
}
export function getInfo(params) {
	return request({ url: '/Valuation/getInfo',method: 'post',data: params})
}
