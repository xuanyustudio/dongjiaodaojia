<template>
	<!-- 信用分 -->
	<view class="creditscore">
		<!-- 头部信用分可视表 -->
		<view class="visualCreditscore">
			<!-- 表 -->
			<view class="surface">
				<canvas :style="canvasStyle" canvas-id="myCanvas" id="myCanvas"></canvas>
			</view>
			<view class="visualCreditscore-text">
				<view class="position-relative">
					<image :src="imgUrl+'active-xyf.png'" v-if="data.credit<20" class="img position-absolute" mode="widthFix" style="width:100%;top:0;left:0;"></image>
					<view class="position-relative">极差</view>
				</view>
				<view class="position-relative">
					<image :src="imgUrl+'active-xyf.png'" v-if="data.credit>=20 && data.credit<40" class="img position-absolute" mode="widthFix" style="width:100%;top:0;left:0;"></image>
					<view class="position-relative">较差</view>
				</view>
				<view class="position-relative">
					<image :src="imgUrl+'active-xyf.png'" v-if="data.credit>=40 && data.credit<60" class="img position-absolute" mode="widthFix" style="width:100%;top:0;left:0;"></image>
					<view class="position-relative">中等</view>
				</view>
				<view class="position-relative">
					<image :src="imgUrl+'active-xyf.png'" v-if="data.credit>=60 && data.credit<80" class="img position-absolute" mode="widthFix" style="width:100%;top:0;left:0;"></image>
					<view class="position-relative">良好</view>
				</view>
				<view class="position-relative">
					<image :src="imgUrl+'active-xyf.png'" v-if="data.credit>=80" class="img position-absolute" mode="widthFix" style="width:100%;top:0;left:0;"></image>
					<view class="position-relative">优秀</view>
				</view>
			</view>
			<view class="visualCreditscore-bg">
				<image :src="imgUrl+'xyf.png'" mode=""></image>
			</view>
			<view class="scoreInterpretation">
				<view class="scoreInterpretation-header">
					<view class="header-left">
						分数解读
					</view>
					<view class="header-right">
						<view class="header-right-text">
							详情规则
						</view>
						<u-icon name="arrow-right" color="#46D17E" size="20"></u-icon>
					</view>
				</view>
				<view class="scoreInterpretation-text">
					信用分是平台考察技术服务质量和信用情况的综合指标
					信用分的高低将决定技师在平台优先接单顺序
				</view>
			</view>
		</view>

		<!-- 信用分记录 -->
		<view class="creditscoreJl">
			<view class="creditscoreJl-header">
				信用分记录
				<u-line />
			</view>
			<view class="creditscoreJl-content" v-for="item in list">
				<!-- 年月 -->
				<view class="content-left">{{item.time}}</view>
				<!-- 信用分+1列表 -->
				<view class="content-list">
					<view class="content-item" v-for="item2 in item.list">
						<!-- 圆圈 -->
						<view class="circle"></view>
						<view class="add numadd">{{item2.num}}</view>
						<view class="content-item-text">{{item2.name}}</view>
						<!-- day 日期 -->
						<view class="day">{{item2.complete_time.slice(5)}}</view>
					</view>
				</view>
			</view>
			<u-loadmore :status="status" />
		</view>
	</view>
</template>

<script>
	const info = uni.getSystemInfoSync()
	const WINDOW_WIDTH = info.windowWidth ? info.windowWidth : info.screenWidth //整个屏幕的宽度，为了适应宽度变化
	const WINDOW_HEIGHT = 240 //canvas高度
	const MIDDLE_WIDTH = WINDOW_WIDTH / 2 //圆心x轴
	const MIDDLE_HEIGHT = WINDOW_HEIGHT / 2 //圆心y轴
	const RADIUS = 100 //半径
	const HALFRADIUS = RADIUS / 2 //半径一半
	export default {
		data() {
			return {
				imgUrl: this.$store.state.imgUrl,
				data:'',
				load: 0,
				list:[],
				list2:[],
				page:1,
				status: 'loading'
			}
		},
		watch: {
			// 监听数据变化
			load(newVal, oldVal) {
				if (newVal != oldVal) {
					this.initCanvas()
				}
			}
		},
		computed: {
			canvasStyle() {
				return `width:${WINDOW_WIDTH}px;height:${WINDOW_HEIGHT}px;`

			}
		},
		onReady() {
			this.initCanvas();
		},
		onLoad() {
			this.init()
		},
		onReachBottom() {
			if(this.status == 'loading'){
				this.init()
			}
		},
		methods: {
			initCanvas() {
				const ctx = uni.createCanvasContext('myCanvas')
				// 画半圆
				this.drawCircle(ctx)
				// 画刻度线
				this.drawTickLine(ctx)
				ctx.draw()
			},
			drawCircle(ctx) {
				//每次加载前清除画布
				ctx.clearRect(0, 0, WINDOW_WIDTH, WINDOW_HEIGHT);
				ctx.beginPath();
				// 灰色底框
				ctx.strokeStyle = "rgba(255,255,255,0.3)"; //底框的背景颜色
				ctx.lineWidth = 15; //底框的宽度
				ctx.lineCap = "round"; //设置两条为圆
				//底框显示的位置想x,y,r,start,end
				ctx.arc(MIDDLE_WIDTH, MIDDLE_HEIGHT + HALFRADIUS, RADIUS, Math.PI, 2 * Math.PI);
				ctx.stroke(); //绘制底框，空心圆

				// 开始新的路径
				ctx.beginPath();
				var lingrad = ctx.createLinearGradient(MIDDLE_WIDTH - RADIUS, MIDDLE_HEIGHT + HALFRADIUS, MIDDLE_WIDTH +
					RADIUS, MIDDLE_HEIGHT);
				lingrad.addColorStop(0.2, '#caffc6');
				lingrad.addColorStop(0.4, '#d6ffd3');
				lingrad.addColorStop(0.7, '#f7fef6');
				lingrad.addColorStop(1, '#ffffff');
				// 绘制新的圆环
				ctx.strokeStyle = lingrad;
				ctx.lineWidth = 15;
				//结束的角度
				var endAngle = 90 + (this.load * 180 / 100) / 2;
				ctx.arc(MIDDLE_WIDTH, MIDDLE_HEIGHT + HALFRADIUS, RADIUS, Math.PI, endAngle * Math.PI / 90);
				ctx.stroke();
				// 设置中间的文字字体和大小
				ctx.font = "50px serif";
				ctx.fillStyle = "white";
				ctx.textAlign = 'center';
				// 设置图形文字
				ctx.fillText(this.load, MIDDLE_WIDTH, MIDDLE_HEIGHT + HALFRADIUS + 20);
				ctx.font = "18px serif";
				ctx.fillText('信用分', MIDDLE_WIDTH, MIDDLE_HEIGHT + HALFRADIUS - 30);
			},
			drawTickLine(ctx) {
				ctx.font = "14px SimHei"
				ctx.textAlign = 'start';
				ctx.fillStyle = "rgba(255,255,255,0.8)"

				ctx.beginPath()
				ctx.moveTo(MIDDLE_WIDTH - RADIUS + 15, MIDDLE_HEIGHT + HALFRADIUS)
				ctx.lineTo(MIDDLE_WIDTH - RADIUS + 30, MIDDLE_HEIGHT + HALFRADIUS)
				ctx.fillText(0, MIDDLE_WIDTH - RADIUS + 35, MIDDLE_HEIGHT + HALFRADIUS + 3.5)
				ctx.moveTo(MIDDLE_WIDTH, MIDDLE_HEIGHT - RADIUS + HALFRADIUS + 15)
				ctx.lineTo(MIDDLE_WIDTH, MIDDLE_HEIGHT - RADIUS + HALFRADIUS + 30)
				ctx.fillText(50, MIDDLE_WIDTH - 7, MIDDLE_HEIGHT - RADIUS + HALFRADIUS + 45)
				ctx.moveTo(MIDDLE_WIDTH + RADIUS - 15, MIDDLE_HEIGHT + HALFRADIUS)
				ctx.lineTo(MIDDLE_WIDTH + RADIUS - 30, MIDDLE_HEIGHT + HALFRADIUS)
				ctx.fillText(100, MIDDLE_WIDTH + RADIUS - 55, MIDDLE_HEIGHT + HALFRADIUS + 3.5)
				ctx.closePath()

				ctx.lineWidth = 0.5
				ctx.strokeStyle = '#fff'
				ctx.stroke()
			},
			init(){
				uni.showLoading({title:'加载中'})
				this.$db.http('my.Index/creditLog',{page: this.page}).then(res => {
					if(res.code==1){
						uni.hideLoading()
						
						let list = res.data.list
						if(this.page>1){
							this.list2 = this.list2.concat(list)
						}else{
							this.data = res.data
							this.list2 = list
							
							// 模拟加载
							this.timer=setInterval(()=>{
								this.load+=2;
								if(this.load>=res.data.credit){
									clearInterval(this.timer)
								}
							},30)
						}
						this.zhuan(this.list2)
						if(this.list2.length >= res.data.total){
							this.status = 'nomore'
							return
						}
						this.page++
					}
				})
			},
			zhuan(item){
				let a = [],
					c = []
				for(let i in item){
					a.push(item[i].complete_time.slice(0, 7))
				}
				a = Array.from(new Set(a))
				for(let ii in a){
					let b = {
						time:'',
						list:[]
					}
					b.time = a[ii]
					for(let ai in item){
						if(item[ai].complete_time.indexOf(a[ii]) != -1){
							b.list.push(item[ai])
						}
					}
					c.push(b)
				}
				this.list = c
			}
		}

	}
</script>

<style lang="less">
	.creditscore {
		width: 100%;
		height: 100%;
		overflow-y: auto;
		background: #F8F8F8;

		.visualCreditscore {
			background: #2AB520;
			height: 688rpx;

			.visualCreditscore-text {
				display: flex;
				justify-content: space-between;
				text-align: center;
				width: 504rpx;
				height: 48rpx;
				margin: 0 auto;

				view {
					font-size: 24rpx;
					width: 68rpx;
					line-height: 40rpx;
					height: 48rpx;
					color: #fff;
				}
			}

			.visualCreditscore-bg {
				width: 504rpx;
				height: 32rpx;
				margin: 0 auto;

				image {
					width: 100%;
					height: 100%;
				}
			}
		}

		.scoreInterpretation {
			position: relative;
			top: 40rpx;
			padding: 0 32rpx;
			box-sizing: border-box;
			width: 750rpx;
			height: 214rpx;
			background: #FFFFFF;
			border-radius: 40rpx 40rpx 0 0;
			z-index: 6;
			border-top: 1rpx #2AB520 solid;

			.scoreInterpretation-header {
				display: flex;
				justify-content: space-between;
				align-items: center;
				height: 50rpx;
				margin: 32rpx 0 22rpx 0;

				.header-left {
					font-size: 32rpx;
					font-weight: bold;
					color: #262626;
				}

				.header-right {
					display: flex;
					justify-content: space-between;
					align-items: center;
					font-size: 28rpx;
					font-weight: 400;
					color: #46D17E;
					gap: 18rpx
				}
			}

			.scoreInterpretation-text {
				font-size: 28rpx;
				font-weight: 400;
				color: #666666;
			}
		}

		.creditscoreJl {
			box-sizing: border-box;
			padding: 0 32rpx;
			margin-top: 150rpx;
			background: #FFFFFF;
			padding-bottom:1px;

			.creditscoreJl-header {
				height: 108rpx;
				width: 100%;
				line-height: 108rpx;
				font-size: 32rpx;
				font-weight: bold;
				color: #262626;
				margin-bottom: 48rpx;
			}

			.creditscoreJl-content {
				display: flex;
				justify-content: space-between;

				// margin-top: 46rpx;
				.content-left {
					width: 186rpx;
					font-size: 28rpx;
					font-weight: 400;
					color: #999999;
					margin-right: 24rpx;
				}

				.content-list {
					.content-item {
						position: relative;
						box-sizing: border-box;
						padding-left: 28rpx;
						height: 102rpx;
						width: 530rpx;
						border-left: 6rpx dotted #f7f7f7;
						margin: 24rpx 0;

						.content-item-text {
							font-size: 28rpx;
							font-weight: 400;
							color: #262626;
						}

						.numadd {
							font-size: 32rpx;
							font-weight: bold;
						}

						.add {
							color: #FFB83C;
						}

						.cut {
							color: #7C94FB
						}

						.day {
							position: absolute;
							top: 0;
							right: 0;
							font-size: 24rpx;
							font-weight: 400;
							color: #999999;
						}

						.circle {
							position: absolute;
							top: -16rpx;
							left: -20rpx;
							width: 24rpx;
							height: 24rpx;
							background: #FFFFFF;
							border: 4rpx solid #46D17E;
							border-radius: 50%;
						}
					}
				}
			}

		}
	}
</style>
