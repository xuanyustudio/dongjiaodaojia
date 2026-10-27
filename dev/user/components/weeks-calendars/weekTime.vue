<template>
	<view class="week-box">
		<!-- <view class="week-ym">
			<text class="ym-text">{{y}}年</text>
			<text class="ym-text">{{m+1}}月</text>
			<text class="ym-text"></text>
		</view> -->
		<view class="week-title">
			<text :class="{'text-active':weekDay==1}">周一</text>
			<text :class="{'text-active':weekDay==2}">周二</text>
			<text :class="{'text-active':weekDay==3}">周三</text>
			<text :class="{'text-active':weekDay==4}">周四</text>
			<text :class="{'text-active':weekDay==5}">周五</text>
			<text :class="{'text-active':weekDay==6}">周六</text>
			<text :class="{'text-active':weekDay==0}">周日</text>
		</view>
		<view class="week-day" @touchstart="touchstart" @touchmove="touchmove" @touchend="touchend">
			<view class="day-body" :class="dayClass">
				<!-- 删除‘:class="thsDayTime > item.y+item.m+item.d?'active':''"’此代码可关闭禁用状态 -->
				<view class="day" :class="thsDayTime > item.y+item.m+item.d?'active':''" v-for="(item,index) in days"
					:key="index">
					<text :class="item.sel?'sel':''" @tap="change(item)">{{m+1}}月{{item.d}}日</text>
				</view>
			</view>
		</view>
		<view class="week-time">
			<!-- :class="index + 8 <= nowH ? 'gayer':''" -->
			<view @click="changeSelectTime(item,index)" class="week-time-view" v-for="(item,index) in useTime"
				:class="item.gayer ? 'gayer': index === selectTime ? 'green' : ''" :key="index">
				<text>{{ item.time}}</text>
				<text
					:class="(index => nowH) &&  item.fullness ? 'yellow':'' ">{{ item.fullness ? '暂不可约' : '空闲' }}</text>
				<!-- 这里是判断，根据index + 8 是因为一开始的逻辑就是定的死数据，现在是活数据，所以判断不成立，nowH是当前的时间点 -->
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		props: {
			id: {
				type: Number,
				default: 0
			}
		},
		data() {

			return {
				/* 移动所需参数 */
				dangqian: "",
				startX: 0,
				startY: 0,
				endX: 0,
				endY: 0,
				status: false,
				/* end */
				thsDayTime: '',
				y: '',
				m: '',
				d: '',
				thsDay: '', // 传给父组件的额数据
				date: '',
				days: [],
				move: true,
				dayClass: '',
				weekDay: '',
				nowH: 0,
				date: "",
				useTime: [],
				selectTime: null,
				all: [],
				ser: '',
				hours: [],
				Adate: '',
				ceshi: '',
				Aidosatime: '', // TODO 每个人的时间段
				duii: ''
			}
		},
		onLoad() {
			console.log(this.Aidosatime, "对比")
			console.log(this.ceshi, "对比")
		},
		onShow() {
			console.log(this.Aidosatime, "对比")
			console.log(this.ceshi, "对比")
		},
		watch: {
			id() {
				if (this.date) this.getTime()
			}
		},
		created() {
			// 获取当前小时
			let h = new Date().getHours();
			this.nowH = h;
			this.init(new Date());
			this.weekDay = new Date().getDay();
			let y = new Date().getFullYear();
			let m = new Date().getMonth() + 1;
			let d = new Date().getDate();
			let s = m.toString().length == 1 ? '0' + m.toString() : m;
			let r = d.toString().length == 1 ? '0' + d.toString() : d;
			this.thsDayTime = y.toString() + s + r;
			this.date = `${m}-${d}`;
		},
		methods: {
			/* 监听滑动开始 */
			touchstart(e) {
				this.weekDay = null
				e.preventDefault();
				this.startX = e.touches[0].pageX;
				this.startY = e.touches[0].pageY;
			},
			/* 监听滑动移动 */
			touchmove(e) {
				this.status = true;
				this.endX = e.touches[0].pageX;
				this.endY = e.touches[0].pageY;
			},
			/* 监听滑动结束 */
			touchend(e) {
				let X = this.endX - this.startX,
					Y = this.endY - this.startY;
				if (this.status) {
					if (X > 0 && Math.abs(X) > Math.abs(Y)) {
						//向右
						this.prevWeek()
					} else if (X < 0 && Math.abs(X) > Math.abs(Y)) {
						//向左
						this.nextWeek()
					} else if (Y > 0 && Math.abs(Y) > Math.abs(X)) {
						//向下
					} else if (Y < 0 && Math.abs(Y) > Math.abs(X)) {
						//向上
					} else {
						//没有
					}
					this.status = false;
				} else {
					// console.log('没有');
				}
			},
			/* END */

			init(date) {
				// this.thsDay = date.getDate()
				this.thsDay = date.getFullYear() + '-' + this.fill(Number(date.getMonth()) + 1) + '-' + this.fill(date
					.getDate())
				this.$emit("change", this.thsDay); // 实时时间传给父组件
				console.log(this.thsDay, "this.thsDay")
				this.date = this.format(date);
				console.log(this.date, "this.date")
				this.setDays(date);
				this.getTime();
				this.Adate = this.date
			},
			changeSelectTime(item, index) {
				this.Aidosatime = item.time
				console.log(this.Aidosatime, "Aidosatime")
				console.log(this.ceshi, "this.date1")
				this.selectTime = index
				var xuanzhong = this.Adate + ' ' + this.useTime[this.selectTime].time
				console.log(xuanzhong, 'ase')
				var timestamp = Date.parse(new Date());
				timestamp = timestamp / 1000;
				var xuanzhongs = Date.parse(xuanzhong);
				xuanzhongs = xuanzhongs / 1000
				//当前时间戳为：1403149534
				console.log(timestamp, xuanzhong, 's');
				if (xuanzhongs < timestamp) {
					uni.showToast({
						title: '不能选择小于当前时间',
						icon: 'none'
					})
					return
				}
				if (item.gayer || item.fullness) {
					return;
				}

				console.log(this.date, "this.date")
				this.$emit('changeServiceTime', {
					time: `(${this.Adate}) ${this.useTime[this.selectTime].time}`,
					serd: `${this.Adate}`

				})
			},
			isInteger(obj) {
				return obj % 1 === 0
			},
			getTime() {

				this.useTime = [];
				// 	获取当前日期
				let dateser = new Date();
				let cTime = this.date;
				let nowTime = new Date().getFullYear() + '-' + this.fill(Number(new Date().getMonth()) + 1) + '-' + this
					.fill(new Date().getDate())
				let nowH = new Date().getHours();
				// 获取当前时间点
				let nowdate = new Date();
				let h = nowdate.getHours()
				let m = nowdate.getMinutes()
				this.ceshi = h + ":" + m
				let arr = this.date.split('-');
				// 获取已用时间
				this.$http.post('api/order/dateList', {
					date: arr[1] + '-' + arr[2], //参数为点击的月日
					id: this.id
				}).then(res => {
					console.log(res, "dateList")
					this.all = res.server_hour_range // 客户时间
					this.hours = res.list // 忙时时间
					this.all.forEach((item, index) => { //js遍历客户时间
						this.ser = item
						//this.duii = this.ser + ':00'
						if (this.isInteger(this.ser)) {
							this.ser = this.ser + ':00';
						} else {
							this.ser = this.ser - 0.5;
							this.ser = this.ser + ':30';
						}

						this.useTime.push({
							time: this.ser, // 拿到客户时间
							fullness: false,
							gayer: false
						})

						var item2 = item;
						var newTime = [];
						this.useTime.forEach(item => { // 便利客户时间
							this.dangqian = item.time
							var sert = this.thsDay + ' ' + this.dangqian // 个人时间表
							var timestamp = Date.parse(new Date()); // 当前时间
							timestamp = timestamp / 1000; // 转为时间戳。让当前时间，和个人信息提供的时间作比较
							var tigong = Date.parse(sert);
							tigong = tigong / 1000
							if (timestamp > tigong) { // 如果当前时间大于提供的时间就让背景为灰色，不可选中
								item.gayer = false
								item.fullness = false
							} else {
								this.hours.forEach(item3 => {
									if (item3.hour.split(':')[0] == item.time.split(':')[
										0]) {
										item.gayer = true
										item.fullness = true
									}
								})
								
								newTime.push(item);
							}
						})
						
						this.useTime = newTime;


					});



				})
			},
			setDays(date) {
				let y = this.y = date.getFullYear(),
					m = this.m = date.getMonth(),
					d = this.d = date.getDate(),
					prevDate = new Date(y, m, d - 7),
					start = this.getWeek1(prevDate);
				this.days = [];
				for (let i = 0; i < 21; ++i) {
					let arr = start.split("-");
					this.days[i] = {
						y: arr[0],
						m: arr[1],
						d: arr[2],
						sel: start == this.thsDay
					};
					start = this.addDate(start, 1);
				}
			},
			change(d) {
				/* 删除此代码可关闭不可点击当前时间之前的功能能 */
				if (this.thsDayTime > d.y + d.m + d.d) {
					uni.showToast({
						title: '预约时间不得小于当天',
						duration: 3000,
						icon: 'none'
					})
					return
				}
				/* END */
				let date = new Date(d.y, d.m - 1, d.d)
				this.weekDay = date.getDay()
				this.init(date);
				this.$emit("back", this.date)
			},
			format(date) {
				let y = date.getFullYear();
				let m = date.getMonth() + 1;
				let d = date.getDate();
				return y + '-' + this.fill(m) + '-' + this.fill(d);
			},
			fill(a) {
				return 10 > a ? '0' + (0 | a) : a
			},
			getWeek(y, m, d) {
				return new Date(y, m, d).getDay();
			},
			getWeek1(date) {
				let nowTime = date.getTime();
				let day = date.getDay() == 0 ? 7 : date.getDay();
				let oneDayLong = 24 * 60 * 60 * 1000;
				let time = nowTime - (day - 1) * oneDayLong;
				let week1 = new Date(time);
				return this.format(week1);
			},
			addDate(date, days) {
				if (days == undefined || days == '') {
					days = 1;
				}
				var date = new Date(date);
				date.setDate(date.getDate() + days);
				let y = date.getFullYear();
				let m = date.getMonth() + 1;
				let d = date.getDate();
				return y + '-' + this.fill(m) + '-' + this.fill(d);
			},
			moveDay(y, m, d) {
				d = d == 0 ? '00' : d
				let date = new Date(y, m, d || this.d)
				this.setDays(date);
			},
			prevYear() {
				this.moveDay(this.y - 1, this.m)
			},
			nextYear() {
				this.moveDay(this.y + 1, this.m)
			},
			prevMonth() {
				this.moveDay(this.y, this.m - 1)
			},
			nextMonth() {
				this.moveDay(this.y, this.m + 1)
			},
			prevWeek() {
				if (this.move) {
					this.move = false;
					this.dayClass = "to-left";
					let ths = this;
					setTimeout(function() {
						ths.dayClass = "";
						ths.moveDay(ths.y, ths.m, ths.d - 7)
						ths.move = true;
					}, 1000)
				}
			},
			nextWeek() {
				if (this.move) {
					this.move = false;
					this.dayClass = "to-right";
					let ths = this;
					setTimeout(function() {
						ths.dayClass = "";
						ths.moveDay(ths.y, ths.m, ths.d + 7)
						ths.move = true;
					}, 1000)
				}
			}
		}
	}
</script>

<style>
	/* 周日期样式*/
	.week-box {
		position: relative;
		margin: 0 0rpx;
		/* background: #FFFFFF; */
		color: #FFFFFF;
		font-size: 24rpx;
		/* box-shadow: 0 0 10rpx #e6e6e6; */
		border-radius: 20rpx;
	}

	.text-active {
		/* background-color: #FFFFFF;
		color: #18B567;
		border-radius: 10rpx ; */
	}

	.week-ym {
		font-size: 30rpx;
		line-height: 72rpx;

		/* border-bottom: 2rpx solid #dadada; */
		text-align: center;
	}

	.week-ym .ym-text {
		/* margin: 0 30upx; */
	}

	.week-ym .i {
		display: inline-block;
		width: 0;
		height: 0;
		border-color: transparent;
		border-style: dashed;
		border-width: 16rpx;
		margin-top: -6rpx;
		vertical-align: middle;
	}

	.week-ym .next-i {
		margin-right: 10rpx;
		border-left-color: #376EF1
	}

	.week-ym .prev-i {
		margin-left: 10rpx;
		border-right-color: #376EF1
	}

	.week-title {
		/* height: 60rpx; */
		line-height: 54rpx;
		color: #FFFFFF;
		display: flex;
		justify-content: space-around;
		position: absolute;
		width: 100%;

	}

	.week-title text {
		/* display: inline-block; */
		/* width: 14.28%; */
		text-align: center;
		padding: 0 24.5rpx
	}

	.week-day {
		overflow: hidden;
		position: relative;
		height: 180rpx;
		padding-top: 50rpx;

	}

	.week-day .day-body {
		position: absolute;
		width: 300%;
		left: -100%;

	}

	.week-day .day {
		text-align: center;
		display: inline-block;
		width: 4.7619%;
		height: 56rpx;
		line-height: 56rpx;
		color: #FFFFFF;
		font-size: 20rpx;
		font-weight: 600;

	}

	.week-day .day.active {
		/* background-color: #e5e5e5; */
		color: #93DCB7;
	}

	.week-day .sel {
		display: inline-block;
		/* width: 56rpx; */
		/* background: #FFFFFF; */
		border-radius: 0 0 10rpx 10rpx;
		/* padding: 5rpx 5rpx; */
		/* border-radius: 50%; */
		padding: 0 11rpx;
		color: #18b567;
		background-color: #FFFFFF;
		color: #18B567;
		border-radius: 10rpx;


	}

	.week-time {
		display: flex;
		flex-wrap: wrap;
	}

	.week-time-view {
		width: 153rpx;
		height: 112rpx;
		background-color: rgba(255, 255, 255, 1);
		border-radius: 16rpx;
		display: flex;
		flex-direction: column;
		margin-right: 18rpx;
		margin-bottom: 10rpx;
		color: rgba(80, 80, 80, 1);
		font-size: 28rpx;
		font-weight: bold;
		align-items: center;
		justify-content: center;
	}

	.week-time-view text:last-of-type {
		font-size: 24rpx;
		font-weight: normal;
		color: rgba(24, 181, 103, 1);
	}

	.green {
		box-sizing: border-box;
		background-color: rgba(227, 246, 236, 1);
		border: 2rpx solid rgba(147, 220, 183, 1);
	}

	.gayer {
		background-color: rgba(228, 228, 228, 1);
		color: rgba(210, 210, 210, 1);
	}

	.gayer text:last-of-type {
		color: rgba(201, 201, 201, 1);
	}

	.yellow {
		color: rgba(243, 151, 53, 1) !important;
	}


	.to-left {
		animation: toLeft 1s ease-in infinite;
	}

	.to-right {
		animation: toRight 1s ease-in infinite;
	}

	@-webkit-keyframes toLeft {
		from {
			left: -100%;
		}

		to {
			left: 0%;
		}
	}

	@-webkit-keyframes toRight {
		from {
			left: -100%;
		}

		to {
			left: -200%;
		}
	}
</style>
