<template>
	<view :class="isRecord? 'recordBg emergencyHelp':'emergencyHelp'">
		<!-- 头部大文字 -->
		<view class="bigtitel">
			<view class="">
				严重危害生命财产安全
			</view>
			<view class="">
				求助专属通道
			</view>
		</view>
		<view class="tips">
			平台将为您：保留录音证据、实时位置、通知平台联系人
		</view>
		<view class="img" v-if="!isRecord" @click="startRecord">
			<image :src="imgUrl+'lyimg.png'" mode=""></image>

		</view>
		<view class="img" v-else @click="endRecord">
			<image :src="imgUrl+'lyzimg.png'" mode=""></image>
		</view>
		<view v-if="!isRecord" class="btn" @click="startRecord">
			紧急求助
		</view>
		<view v-else class="btn" style="color: #46D17E;" @click="endRecord">
			录音中
		</view>
		<view class="footer-tisp">
			<view class="">
				生命财产安全收到侵害、严重威胁时使用
			</view>
			<view class="">
				必要时，我们将为您接通110
			</view>
		</view>
		<file></file>
	</view>
</template>

<script>
	// const recorderManager = uni.getRecorderManager();
	// const innerAudioContext = uni.createInnerAudioContext();

	// innerAudioContext.autoplay = true;
	import {callPolice} from "./api.js"
	import file from "../components/file/file.vue"
	export default {
		components: {file},
		data() {
			return {
				imgUrl: this.$store.state.imgUrl,
				audio: "", //保存的录音
				isRecord: false,
	
				longitude:'',
				latitude:''
			}
		},
		onLoad() {
			this.handleLocation()
			// #ifndef H5
			let _this = this
			this.recorderManager = uni.getRecorderManager()
			this.recorderManager.onStop(function (res) {
				_this.uploadImg(res.tempFilePath)
			})
			// #ifdef APP-PLUS
			plus.android.requestPermissions(['android.permission.RECORD_AUDIO'])
			// #endif
			// #endif
		},
		watch: {
			isRecord(newValue) {
				if (newValue) {
					uni.setNavigationBarColor({
						frontColor: '#ffffff',
						backgroundColor: '#0ca27d',
					})
				} else {
					uni.setNavigationBarColor({
						frontColor: '#ffffff',
						backgroundColor: '#f97302',
					})
				}
			}
		},
		methods: {
			//手动设置地址
			handleLocation() {
				let _this = this
				uni.getLocation({
				    type: 'wgs84',
				    success: function (res) {
						_this.longitude = res.longitude
						_this.latitude = res.latitude
				    }
				});
			},
			
			startRecord() {
				// #ifdef H5
				this.startH5Record()
				return
				// #endif
				if (!this.recorderManager) {
					this.handleCallPolice()
					return
				}
				this.isRecord = true
				this.recorderManager.start({
					format: "mp3"
				})
			},
			endRecord() {
				// #ifdef H5
				this.endH5Record()
				return
				// #endif
				if (this.recorderManager) this.recorderManager.stop()
				this.isRecord = false
			},
			startH5Record() {
				const media = navigator.mediaDevices
				if (!media || !media.getUserMedia || typeof MediaRecorder === 'undefined') {
					uni.showToast({ title: '当前浏览器不能录音，已按当前位置报警', icon: 'none' })
					this.handleCallPolice()
					return
				}
				media.getUserMedia({ audio: true }).then(stream => {
					const mime = MediaRecorder.isTypeSupported('audio/webm') ? 'audio/webm' : ''
					this.h5Stream = stream
					this.h5Chunks = []
					this.h5Recorder = new MediaRecorder(stream, mime ? { mimeType: mime } : undefined)
					this.h5Recorder.ondataavailable = e => {
						if (e.data && e.data.size) this.h5Chunks.push(e.data)
					}
					this.h5Recorder.onstop = () => {
						stream.getTracks().forEach(track => track.stop())
						const blob = new Blob(this.h5Chunks, { type: this.h5Recorder.mimeType || 'audio/webm' })
						this.uploadBlob(blob)
					}
					this.h5Recorder.start()
					this.isRecord = true
				}).catch(() => {
					uni.showToast({ title: '没有麦克风权限，已按当前位置报警', icon: 'none' })
					this.handleCallPolice()
				})
			},
			endH5Record() {
				this.isRecord = false
				if (this.h5Recorder && this.h5Recorder.state !== 'inactive') {
					this.h5Recorder.stop()
					return
				}
				this.handleCallPolice()
			},
			uploadBlob(blob) {
				uni.showLoading({ title: '录音正在上传' })
				const fd = new FormData()
				const ext = (blob.type || '').indexOf('webm') >= 0 ? 'webm' : 'mp3'
				fd.append('file', blob, 'alarm.' + ext)
				fetch(this.$cfg.httpUrl + 'index/uploadAudio', {
					method: 'POST',
					headers: { token: this.$db.get('token') },
					body: fd
				}).then(res => res.json()).then(data => {
					uni.hideLoading()
					if (data && data.code == 1) this.audio = data.data
					this.handleCallPolice()
				}).catch(() => {
					uni.hideLoading()
					this.handleCallPolice()
				})
			},
			
			//上传录音
			uploadImg(e) {
				uni.showLoading({title:'录音正在上传'})
				let _this = this
				uni.uploadFile({
					url: _this.$cfg['httpUrl']+'index/uploadAudio',
					filePath: e,
					name: 'file',
					header:{
						token:_this.$db.get('token')
					},
					success: (res) => {
						uni.hideLoading()
						try {
							var _data = JSON.parse(res.data)
							if (_data.code == 1) _this.audio = _data.data
						} catch (err) {}
						_this.handleCallPolice()
					},
					fail: () => {
						uni.hideLoading()
						_this.handleCallPolice()
					}
				});
			},
			// 提交报警信息
			handleCallPolice() {
				console.log('currentSite',this.$store.state.currentSite)
				let data = {
					...this.$store.state.currentSite,
					audio: this.audio,
					lng: this.longitude,
					lat: this.latitude
				}
				console.log('提交的报警信息',data)
				let audio = this.audio
				callPolice(data).then(res => {
					uni.showToast({title: res.msg})
				})
			}
		}
	}
</script>

<style lang="less">
	.emergencyHelp {
		width: 100%;
		min-height: 100vh;
		background: #f97302;
		text-align: center;
		overflow-y: auto;

		.bigtitel {
			font-size: 40rpx;
			font-weight: bold;
			line-height: 64rpx;
			color: #FFFFFF;
			margin-top: 100rpx;
		}

		.tips {
			font-size: 24rpx;
			font-weight: 400;
			line-height: 64rpx;
			color: #FFFFFF;
		}

		.img {
			width: 488rpx;
			height: 488rpx;
			margin: 0 auto;
			margin-top: 80rpx;

			image {
				width: 100%;
				height: 100%;
			}
		}

		.btn {
			width: 176rpx;
			height: 176rpx;
			margin: 0 auto;
			background: linear-gradient(140deg, #FFFFFF 0%, #FFF0D6 100%);
			border-radius: 50%;
			font-size: 32rpx;
			font-weight: 500;
			line-height: 176rpx;
			color: #FFB83C;
			margin-top: 136rpx;
		}

		.footer-tisp {
			font-size: 24rpx;
			font-weight: 400;
			line-height: 44rpx;
			color: #FFFFFF;
			margin: 40rpx 0 60rpx 0;
		}
	}

	.recordBg {
		background: #0ca27d;
	}
</style>
