<template>
	<!-- 协议签署 -->
	<view class="agreementSigning">
		<view class="header-bg">
		</view>
		<view class="SigningConten">
			<view class="title">服务协议签署</view>
			<u-parse :content="data"></u-parse>
		</view>
		<view class="cardId m-t10">
			<view class="name">{{realname}}</view>
			<u-line />
			<view class="card">
				<text>身份证</text>
				<text>{{cardsn}}</text>
			</view>
			<u-line v-if="mobile" />
			<view class="phone" v-if="mobile">
				<text>手机号</text>
				<text>{{mobile}}</text>
			</view>
		</view>
		<view class="footer m-t10">
			<view class="popup_footer">协议签名</view>
			<u-line />
			<view class="position-relative">
				<image :src="path2" mode="widthFix" class="img" style="width:100%;background:#fff;" v-if="path2"></image>
				<template v-else>
					<view class="position-absolute dd-flex" style="top:0;bottom:0;left:0;right:0;z-index:1;pointer-events:none;font-size:124rpx;opacity:.1;">在此签名</view>
					<canvas class="mycanvas" :disable-scroll="true" canvas-id="mycanvas" @touchstart="touchstart" @touchmove="touchmove" @touchend="touchend"></canvas>
				</template>
				
			</view>
			
			<view class="popup_footerd" v-if="status==0">协议正在审核中</view>
			<view class="f-w400 p-t20" v-else-if="status==1">
			已签署，时间：{{info.technician_tcp&&info.technician_tcp.create_time | formatDate}}
			</view>
			<view class="popup_footerd" @click="finish" v-else>确认签署</view>
		</view>
	</view>
</template>

<script>
	var x = 20;
	var y = 20;
	export default {
		data() {
			return {
				data: '',
				info:'',
				headerStyleHeight: 0,
				headerStyleTop: 0,
				ctx: "",
				points: [], //路径点集合
				showCanvas: false,
				signature: '',
				path: '',
				path2:'',
				realname:'',
				cardsn:'',
				mobile:'',
				status:null,
				isQm:false
			}
		},
		//时间戳的处理
		       filters: {
		           formatDate: function(value) {
		               var date = new Date();
		               //date.setTime(value);
		               var month = date.getMonth() + 1;
		               var hours = date.getHours();
		               if (hours < 10)
		                   hours = "0" + hours;
		               var minutes = date.getMinutes();
		               if (minutes < 10)
		                   minutes = "0" + minutes;
		               var time = date.getFullYear() + "-" + month + "-" + date.getDate() +
		                   " " + hours + ":" + minutes;
		               return time;
		           }
		
		       },
		onLoad() {
			this.ctx = uni.createCanvasContext("mycanvas", this); //创建绘图对象
			//设置画笔样式
			this.ctx.lineWidth = 4;
			this.ctx.lineCap = "round"
			this.ctx.lineJoin = "round"
			
			this.getInfo()
		},
		methods: {
			getInfo(){
				uni.showLoading({title:'加载中'})
				this.$db.http('my.Index/index').then(res => {
					if(res.code==1){
						this.info = res.data
						let tcp = res.data.technician_tcp
						if(tcp){
							this.path2 = tcp.sign
							this.status = tcp.status
							if(tcp.status==2){
								this.path2 = ''
								uni.showToast({title:'服务协议未通过，请重新签署',icon:'none',duration:3000})
							}
						}
						this.init()
						
						let id = res.data.technician_identity
						this.realname = id.realname
						this.cardsn = id.cardsn
						this.mobile = res.data.info.mobile
					}
				})
			},
			init(){
				this.$db.http('my.Index/get_technician_tcp').then(res => {
					if(res.code==1){
						uni.hideLoading()
						this.data = res.data
					}
				})
			},
			
			reWrite() {
				this.clear();
			},
			
			//创建并显示画布
			createCanvas: function() {
				this.showCanvas = true;
				this.ctx = uni.createCanvasContext("mycanvas", this); //创建绘图对象     
				//设置画笔样式
				this.ctx.setFillStyle("#fff");
				this.ctx.fill(); //设置填充
				this.ctx.lineWidth = 20;
				this.ctx.lineCap = "round"
				this.ctx.lineJoin = "round"
			},
			
			//触摸开始，获取到起点
			touchstart: function(e) {
				this.startX = e.changedTouches[0].x
				this.startY = e.changedTouches[0].y
				//设置画笔参数
				//画笔颜色
				this.ctx.setStrokeStyle(this.lineColor)
				//设置线条粗细
				this.ctx.setLineWidth(4)
				//设置线条的结束端点样式
				this.ctx.setLineCap("round") //'butt'、'round'、'square'
				//开始画笔
				this.ctx.beginPath()
			},
			
			//触摸移动，获取到路径点
			touchmove: function(e) {
				//取点
				let temX = e.changedTouches[0].x
				let temY = e.changedTouches[0].y
				//画线条
				this.ctx.moveTo(this.startX, this.startY)
				this.ctx.lineTo(temX, temY)
				this.ctx.stroke()
				this.startX = temX
				this.startY = temY
				this.ctx.draw(true)
			},
			
			touchend() {
				this.isQm = true
			},
			
			draw() {
				let point1 = this.points[0]
				let point2 = this.points[1]
				this.points.shift()
				this.ctx.moveTo(point1.X, point1.Y)
				this.ctx.lineTo(point2.X, point2.Y)
				this.ctx.stroke()
				this.ctx.draw(true)
			},

			//清空画布
			clear() {
				let that = this;
				uni.getSystemInfo({
					success(res) {
						let canvasw = res.windowWidth;
						let canvash = res.windowHeight;
						that.ctx.clearRect(0, 0, canvasw, canvash);
						that.ctx.draw(true);
					},
				})
			},

			//完成绘画
			finish() {
				if(!this.isQm){ uni.showToast({title:'请先签署协议',icon:'none'}); return}
				
				let that = this
				uni.canvasToTempFilePath({
					canvasId: 'mycanvas',
					fileType: 'png',
					quality: 1, //图片质量
					success(res) {
						that.uploadImg(res.tempFilePath)
						that.clear()
						setTimeout(function(){
							uni.redirectTo({
							    url: '/page-mine-subcontract/agreementSigning/agreementSigning'
							});
						},2000)
					}
				});
			},
			//上传图片
			uploadImg(e) {
				let _this = this
				uni.uploadFile({
					url: _this.$cfg['httpUrl']+'index/uploadImage',
					
					filePath: e,
					name: 'file',
					header:{
						token:_this.$db.get('token')
					},
					success: (res) => {
						var _data = JSON.parse(res.data);
						console.log(_data)
						if(_data.code==1){
							_this.path = _data.data
							
							_this.submit()
						}
					}
				});
			},
			submit(){
				let data = {
					openid: this.$db.get('openid'),
					sign: this.path
				}
				uni.showLoading({title:'加载中'})
				this.$db.http('my.Index/sign_tcp',data).then(res => {
					if(res.code==1){
						uni.showToast({title:res.msg})
						setTimeout(()=>{
							uni.navigateTo()
						},1000)
					}
				})
			}
		}
	}
</script>

<style lang="less">
	.agreementSigning {
		.header-bg {
			width: 750rpx;
			height: 448rpx;
			background: linear-gradient(0deg, #F8F8F8 0%, #32B728 17%, #3EC735 100%);
		}

		.SigningConten {
			box-sizing: border-box;
			position: relative;
			width: 686rpx;
			background: #FFFFFF;
			border-radius: 16rpx;
			margin: 0 auto;
			margin-top: -300rpx;
			border-top: #fff solid 1rpx;
			padding: 0 40rpx;
			padding-bottom:40rpx;

			.title {
				text-align: center;
				font-size: 32rpx;
				font-family: PingFang SC;
				font-weight: bold;
				color: #262626;
				margin: 37rpx 0;
			}

			.firstparty {
				font-size: 28rpx;
				font-weight: 600;
				line-height: 44rpx;
				color: #262626;
				margin-bottom: 30rpx;
			}

			.content {
				font-size: 28rpx;
				font-weight: 400;
				line-height: 44rpx;
				color: #262626;
			}

		}

		.cardId {
			position: relative;
			width: 686rpx;
			background: #FFFFFF;
			border-radius: 16rpx;
			margin: 0 auto;

			.name {

				width: 1000%;
				box-sizing: border-box;
				padding: 0 24rpx;
				height: 96rpx;
				font-size: 32rpx;
				line-height: 96rpx;
				font-weight: bold;
				color: #000000;
				opacity: 1;
			}

			.card {
				display: flex;
				box-sizing: border-box;
				padding: 0 24rpx;
				width: 100%;
				height: 96rpx;
				justify-content: space-between;
				align-items: center;
				font-size: 32rpx;
				font-weight: 400;
				color: #262626;
			}

			.phone {
				display: flex;
				box-sizing: border-box;
				padding: 0 24rpx;
				width: 100%;
				height: 96rpx;
				justify-content: space-between;
				align-items: center;
				font-size: 32rpx;
				font-weight: 400;
				color: #262626;
			}
		}

		.footer {
			position: relative;
			width: 686rpx;
			margin: 0 auto;
			font-size: 32rpx;
			font-weight: bold;
			color: #000000;

		}

		.mycanvas {
			width: 100%;
			height: 320rpx;
			background: #fff;

		}

		.popup_footer {
			border-radius: 16rpx 16rpx 0 0;
			width: auto;
			height: 96rpx;
			line-height: 96rpx;
			box-sizing: border-box;
			padding: 0 24rpx;
			background: #fff;

		}

		.popup_footerd {

			width: 686rpx;
			height: 88rpx;
			margin:  0 auto;
			background: linear-gradient(90deg, #46D17E 0%, #3EC735 100%);
			border-radius: 16rpx;
			line-height: 88rpx;
			text-align: center;
			font-size: 32rpx;
			font-weight: 500;
			color: #FFFFFF;
			margin-top: 40rpx;

		}

		.popup_footer_img {

			width: 32rpx;

			height: 32rpx;

		}

		.popup_footer_img1 {

			width: 50rpx;

			height: 50rpx;

		}

		.popup_footer_txt {

			font-size: 28rpx;

			font-weight: 400;

			color: #333333;

			margin-left: 27rpx;

		}

		.re_button {

			width: 79rpx;

			height: 44rpx;

			text-align: center;

			font-size: 24rpx;

			line-height: 45rpx;

			border: 1px solid #979797;

			border-radius: 10rpx;

			color: #333333;

			margin-right: 66rpx;

		}

		.button {

			width: 79rpx;

			height: 44rpx;

			text-align: center;

			font-size: 24rpx;

			line-height: 45rpx;

			background: #EF5350;

			border: 1px solid #EF5350;

			border-radius: 10rpx;

			color: #F6F6F6;

		}

		.btn {

			background: linear-gradient(360deg, #CCCCCC 0%, #F98156 0%, #EF5350 9%, #E53935 100%);

			border-radius: 20rpx;

			margin: 70rpx 88rpx 50rpx 88rpx;

			color: #F6F6F6;

		}

	}
</style>
