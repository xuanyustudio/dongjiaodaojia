import { ElMessageBox } from 'element-plus'
import store from '../store'

// 日期格式化
export function parseTime(time, pattern) {
	if (arguments.length === 0 || !time) {
		return null
	}
	const format = pattern || '{y}-{m}-{d} {h}:{i}:{s}'
	let date
	if (typeof time === 'object') {
		date = time
	} else {
		if ((typeof time === 'string') && (/^[0-9]+$/.test(time))) {
			time = parseInt(time)
		} else if (typeof time === 'string') {
			time = time.replace(new RegExp(/-/gm), '/');
		}
		if ((typeof time === 'number') && (time.toString().length === 10)) {
			time = time * 1000
		}
		date = new Date(time)
	}
	const formatObj = {
		y: date.getFullYear(),
		m: date.getMonth() + 1,
		d: date.getDate(),
		h: date.getHours(),
		i: date.getMinutes(),
		s: date.getSeconds(),
		a: date.getDay()
	}
	const time_str = format.replace(/{(y|m|d|h|i|s|a)+}/g, (result, key) => {
		let value = formatObj[key]
		// Note: getDay() returns 0 on Sunday
		if (key === 'a') { return ['日', '一', '二', '三', '四', '五', '六'][value] }
		if (result.length > 0 && value < 10) {
			value = '0' + value
		}
		return value || 0
	})
	return time_str
}

//element confirm重新封装
export function confirm(param) {
    // 默认参数
    let config = {
        tip: '提示',
        content: '你确定要执行此操作么吗？',
        btn: { confirm: '确定', cancel: '取消', },
        type: 'warning'
    }
    // 如果有参数传入并且长度大于0 则替换原来的指定默认配置 
    if (param && Object.keys(param).length) {
        for (let item in param) {
            config[item] = param[item]
        }
    }
    return new Promise((resolve) => {
        ElMessageBox.confirm(config.content, config.tip, {
            confirmButtonText: config.btn.confirm,
            cancelButtonText: config.btn.cancel,
            type: config.type,
            dangerouslyUseHTMLString: true
        }).then(() => {
            resolve()
        }).catch(() => {
            //vm.$message({ type: 'info', message: '已取消' })
        })
    })
}

//获取复选框 下拉多选的键名 根据值
export function formatStr(val,data){
	if(val){
		const fieldConfig = typeof(data)=='string' ? JSON.parse(data) : data
		let str = ''
		val.split(",").forEach(item => {
			fieldConfig.forEach(vo=>{
				if(item == vo.val){
					str += ',' +vo.key
				}
			})
		})
		return str.substr(1)
	}
}

//json对象转为url参数
export function param(json) {
	if (!json) return ''
	return cleanArray(
	  Object.keys(json).map(key => {
		if (json[key] === undefined) return ''
		return encodeURIComponent(key) + '=' + encodeURIComponent(json[key])
	  })
	).join('&')
}

export function cleanArray(actual) {
	const newArray = []
	for (let i = 0; i < actual.length; i++) {
	  if (actual[i]) {
		newArray.push(actual[i])
	  }
	}
	return newArray
}

export function param2Obj(url) {
	const search = decodeURIComponent(url.split('?')[1]).replace(/\+/g, ' ')
	if (!search) {
	  return {}
	}
	const obj = {}
	const searchArr = search.split('&')
	searchArr.forEach(v => {
	  const index = v.indexOf('=')
	  if (index !== -1) {
		const name = v.substring(0, index)
		const val = v.substring(index + 1, v.length)
		obj[name] = val
	  }
	})
	return obj
}


export function checkPermission(url){
	if(store.getters.role_id == 1 || store.getters.actions.includes(url)){
		return true
	}
}