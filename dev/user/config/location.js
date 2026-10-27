export default {
	getLocation(lo, la, func) {
		return new Promise((resolve, reject) => {
			uni.request({
				url: 'https://restapi.amap.com/v3/geocode/regeo',
				method: 'GET',
				data: {
					key: 'f30314d5255acac205b476382a598faf',
					location: lo + ',' + la
				},
				success(res) {
					if (res.statusCode == 200 && res.data) {
						var json = res.data; //JSON.parse(res);
						console.log('高德地图解析成功', json);
						if (json.status == 1 && json.info == 'OK') {
							var reg = json.regeocode;
							var address = {
								"country": reg.addressComponent.country,
								"province": reg.addressComponent.province,
								"city": reg.addressComponent.city,
								"district": reg.addressComponent.district,
								"street": reg.addressComponent.streetNumber.street,
								"streetNum": reg.addressComponent.streetNumber.number,
								"poiName": reg.addressComponent.streetNumber.street + reg
									.addressComponent.streetNumber.number,
								"cityCode": reg.addressComponent.citycode,
								"longitude": lo,
								"latitude": la
							};
							// reg.formatted_address
							console.log('组合数据',address)
							resolve(address)

							// return address;

						} else {
							console.log('高德地图解析成功，但是参数有点不对',
								res)

							uni.showToast({
								title: '经纬度解析失败'
							})
							reject(null)
							// return null;
						}
					}
				},
				fail(err) {
					uni.showToast({
						title: '致命错误'
					})
					console.log('高德地图解析失败',
						err)
					reject(null)
				}
			})
		})
	}
}
