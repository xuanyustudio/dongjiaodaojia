<template>
	<div>
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :model-value="show" :before-close="closeForm" append-to-body>
			<table class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:50px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">用户姓名：</td>
						<td>
							{{info.name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">用户名：</td>
						<td>
							{{info.user}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">密码：</td>
						<td>
							{{info.pwd}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">所属角色：</td>
						<td>
							{{info.role_id}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">备注：</td>
						<td>
							{{info.note}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">状态：</td>
						<td>
							<span v-if="info.status == '1'">正常</span>
							<span v-if="info.status == '0'">禁用</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">创建时间：</td>
						<td>
							{{parseTime(info.create_time)}}
						</td>
					</tr>
				</tbody>
			</table>
		</el-dialog>
	</div>
</template>
<script>
	export default {
		name:'Adminuserdetail',
		props: {
			show: {
				type: Boolean,
				default: true
			},
			size: {
				type: String,
				default: 'mini'
			},
			info: {
				type: Object,
			},
		},
		data() {
			return {
			}
		},
		methods: {
			closeForm(){
				this.$emit('update:show', false)
			}
		}
	}
</script>
<style  lang="scss">
@import '@/assets/scss/common.scss'
</style>
