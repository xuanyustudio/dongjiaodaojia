// 获取一周时间
function weekDate(dayNum) {
	var oDate = new Date();
	//获取当前时间
	var dayArr = [];

	//定义一个数组存储所以时间 
	for (var i = 0; i < dayNum; i++) {
		let dayObj = {}
		let date = new Date(oDate.getFullYear(), oDate.getMonth(), oDate.getDate() + i)
		dayObj.monthDay = (1 + date.getMonth() < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1) + "-" +  (date.getDate() < 10 ? '0' + (date.getDate()) : date.getDate())//月日
		dayObj.day = switchDay(date.getDay()) //星期
		dayObj.year = date.getFullYear()
		
		dayArr.push(dayObj);
		//把未来几天的时间放到数组里 
	}

	return dayArr; //返回一个数组。
}
function switchDay(day) {
	switch (day) {
		case 1:
			return "周一"

			break;
		case 2:
			return "周二"

			break;
		case 3:
			return "周三"

			break;
		case 4:
			return "周四"

			break;
		case 5:
			return "周五"

			break;
		case 6:
			return "周六"

			break;
		case 0:
			return "周日"

			break;

		default:
			break;
	}
}
export default weekDate;
