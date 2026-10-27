const check = {
	username(data) {
		if(!(/^1[3456789]\d{9}$/.test(data))){
			uni.showToast({
			    title: "手机号码不正确",
				icon:'none'
			});     
			return false; 
		} 
		return true
	},
	sms_code(data) {
		if(data.length<6) {
			uni.showToast({
			    title: "验证码长度不能小于6位",
				icon:'none'
			});   
			return false;  
		}
		return true
	}
	
}

export default check;
