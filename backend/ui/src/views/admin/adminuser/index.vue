<template>
	<div>
		<el-row :gutter="10">
			<el-col :span="24">
				<el-card shadow="never" class="card">
					<div id="charts_one" style="width:100%;min-height:80vh">
						<Search size='small' :searchVisible="searchVisible" :searchData.sync="searchData"  :searchForm = "searchForm" @refesh_list="index"></Search>
						<div class="btn-group" style="margin-bottom:11px;">
							<div>
								<el-button type="success"  size="mini" icon="el-icon-plus"  @click="add(ids)" v-if="checkPermission('/admin/Adminuser/add.html')" >添加</el-button>
								<el-button type="primary" :disabled="single" size="mini" icon="el-icon-edit"  @click="update(ids)" v-if="checkPermission('/admin/Adminuser/update.html')" >修改</el-button>
								<el-button type="danger" :disabled="single" size="mini" icon="el-icon-delete"  @click="del(ids)" v-if="checkPermission('/admin/Adminuser/delete.html')" >删除</el-button>
								<el-button type="info" :disabled="single" size="mini" icon="el-icon-lock"  @click="resetPwd(ids)" v-if="checkPermission('/admin/Adminuser/resetPwd.html')" >重置密码</el-button>
							</div>
							<table-tool :searchVisible.sync="searchVisible"  :fieldList="fieldList" @refesh_list="index" :searchTool="true"></table-tool>
						</div>
						<el-table highlight-current-row @current-change="selection"  row-key="user_id"  :header-cell-style="{ background: '#eef1f6', color: '#606266' }" v-loading="loading"  ref="multipleTable" border class="eltable" :data="list"  style="width: 100%">
							<el-table-column align="center" type = '' v-if="fieldList[0].checked" property="user_id" label="编号" show-overflow-tooltip width="70">
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[1].checked" property="name" label="用户姓名" show-overflow-tooltip width="">
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[2].checked" property="user" label="用户名" show-overflow-tooltip width="">
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[3].checked" property="role.name" label="所属分组" show-overflow-tooltip width="">
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[4].checked" property="note" label="备注" show-overflow-tooltip width="">
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[5].checked" property="status" label="状态" show-overflow-tooltip width="">
								<template slot-scope="scope">
									<el-switch @change="updateExt(scope.row,'status')" :active-value="1" :inactive-value="0" v-model="scope.row.status"></el-switch>
								</template>
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[6].checked" property="create_time" label="创建时间" show-overflow-tooltip width="">
								<template slot-scope="scope">
									{{parseTime(scope.row.create_time,'{y}-{m}-{d}')}}
								</template>
							</el-table-column>
							<el-table-column :fixed="$store.getters.device !== 'mobile'?'right':false" label="操作" align="center" width="160">
								<template slot-scope="scope">
									<div v-if="scope.row.user_id">
										<el-button size="mini" type="primary" @click="update(scope.row)"  v-if="checkPermission('/admin/Adminuser/update.html')"><i class="el-icon-edit" />修改</el-button>
										<el-button size="mini" type="danger" @click="del(scope.row)"  v-if="checkPermission('/admin/Adminuser/delete.html')"><i class="el-icon-delete" />删除</el-button>
									</div>
								</template>
							</el-table-column>
						</el-table>
						<Page :total="page_data.total" :page.sync="page_data.page" :limit.sync="page_data.limit" @pagination="index" />
					</div>
				</el-card>
			</el-col>
		</el-row>
		<!--添加-->
		<Add  :show.sync="dialog.addDialogStatus" size="small" @refesh_list="index"></Add>
		<!--修改-->
		<Update :info="updateInfo" :show.sync="dialog.updateDialogStatus" size="small" @refesh_list="index"></Update>
		<!--重置密码-->
		<ResetPwd :info="resetPwdInfo" :show.sync="dialog.resetPwdDialogStatus" size="small" @refesh_list="index"></ResetPwd>
	</div>
</template>
<script>
	import {
		index,
		updateExt,
		getUpdateInfo,
		del,
	} from '@/api/admin/adminuser'
	import Search from '@/components/common/Search.vue'
	import Page from '@/components/common/Page.vue'
	import TableTool from '@/components/common/TableTool.vue'
	import Add from '@/views/admin/adminuser/add.vue'
	import Update from '@/views/admin/adminuser/update.vue'
	import ResetPwd from '@/views/admin/adminuser/resetPwd.vue'
	import { confirm,param2Obj } from '@/utils/common'
	export default {
		name:'adminuser',
		components: {
			Search,
			Page,
			TableTool,
			Add,
			Update,
			ResetPwd,
		},
		data(){
			return {
				dialog: {
					addDialogStatus : false,
					updateDialogStatus : false,
					resetPwdDialogStatus : false,
				},
				fieldList:[
					{lable:'编号',checked:true},
					{lable:'用户姓名',checked:true},
					{lable:'用户名',checked:true},
					{lable:'所属分组',checked:true},
					{lable:'备注',checked:true},
					{lable:'状态',checked:true},
					{lable:'创建时间',checked:true},
				],
				ids: [],
				single:true,
				list: [],
				updateInfo:{},
				resetPwdInfo:{},
				loading: false,
				page_data: {
					limit: 20,
					page: 1,
					total:20,
				},
				searchVisible:true,
				searchForm:[],
				searchData:{},
			}
		},
		methods: {
			index(){
				let param = {limit:this.page_data.limit,page:this.page_data.page}
				Object.assign(param, this.searchData,param2Obj(this.$route.fullPath))
				this.loading = true
				index(param).then(res => {
					this.list = res.data.data
					this.page_data.total = res.data.total
					this.loading = false
					if(this.page_data.page == 1){
						this.searchForm = [
							{type:'Input',label:'用户姓名',prop:'name',width:'150px'},
							{type:'Input',label:'用户名',prop:'user',width:'150px'},
							{type:'Select',label:'所属角色',prop:'role_id',data:res.sql_field_data.role_ids,width:'150px'},
							{type:'Select',label:'状态',prop:'status',data:[{"key":"正常","val":"1"},{"key":"禁用","val":"0"}],width:'150px'},
						]
					}
				})
			},
			updateExt(row,field){
				if(row.user_id){
					updateExt({user_id:row.user_id,[field]:row[field]}).then(res => {
						this.$message({message: '操作成功', type: 'success'})
					})
				}
			},
			add(){
				this.dialog.addDialogStatus = true
			},
			update(row){
				let id = row.user_id ? row.user_id : this.ids.join(',')
				getUpdateInfo({user_id:id}).then(res => {
					if(res.status == 200){
						this.dialog.updateDialogStatus = true
						this.updateInfo = res.data
					}
				})
			},
			del(row){
				confirm({content:'确定要操作吗'}).then(() => {
					let ids = row.user_id ? row.user_id : this.ids.join(',')
					del({user_id:ids}).then(res => {
						this.$message({message: res.msg, type: 'success'})
						this.index()
					})
				}).catch(() => {})
			},
			resetPwd(row){
				this.dialog.resetPwdDialogStatus = true
				this.resetPwdInfo = {user_id:row.user_id ? row.user_id : this.ids.join(',')}
			},
			selection(selection) {
				this.ids = []
				this.ids.push(selection.user_id)
				this.single = false
			},
		},
		mounted(){
			this.index()
		}
	}
</script>
<style  lang="scss">
@import '@/assets/scss/common.scss'
</style>