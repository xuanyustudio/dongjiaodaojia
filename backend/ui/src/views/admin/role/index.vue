<template>
	<div>
		<el-row :gutter="10">
			<el-col :span="24">
				<el-card shadow="never" class="card">
					<div id="charts_one" style="width:100%;min-height:80vh">
						<Search size='small' :searchVisible="searchVisible" :searchData.sync="searchData"  :searchForm = "searchForm" @refesh_list="index"></Search>
						<div class="btn-group" style="margin-bottom:11px;">
							<div>
								<el-button type="success"  size="mini" icon="el-icon-plus"  @click="add(ids)" v-if="checkPermission('/admin/Role/add.html')" >添加</el-button>
								<el-button type="primary" :disabled="single" size="mini" icon="el-icon-edit"  @click="update(ids)" v-if="checkPermission('/admin/Role/update.html')" >修改</el-button>
								<el-button type="danger" :disabled="multiple" size="mini" icon="el-icon-delete"  @click="del(ids)" v-if="checkPermission('/admin/Role/delete.html')" >删除</el-button>
							</div>
							<table-tool :searchVisible.sync="searchVisible"  :fieldList="fieldList" @refesh_list="index" :searchTool="true"></table-tool>
						</div>
						<el-table :row-class-name="rowClass" @selection-change="selection"  @row-click="handleRowClick"  row-key="role_id"  :header-cell-style="{ background: '#eef1f6', color: '#606266' }" v-loading="loading"  ref="multipleTable" border class="eltable" :data="list"  style="width: 100%">
							<el-table-column align="center" type="selection" width="42"></el-table-column>
							<el-table-column align="center" type = '' v-if="fieldList[0].checked" property="role_id" label="编号" show-overflow-tooltip width="70">
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[1].checked" property="name" label="角色名称" show-overflow-tooltip width="">
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[2].checked" property="status" label="状态" show-overflow-tooltip width="">
								<template slot-scope="scope">
									<el-switch @change="updateExt(scope.row,'status')" :active-value="1" :inactive-value="0" v-model="scope.row.status"></el-switch>
								</template>
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[3].checked" property="description" label="描述" show-overflow-tooltip width="">
							</el-table-column>
							<el-table-column :fixed="$store.getters.device !== 'mobile'?'right':false" label="操作" align="center" width="160">
								<template slot-scope="scope">
									<div v-if="scope.row.role_id">
										<el-button size="mini" type="primary" @click="update(scope.row)"  v-if="checkPermission('/admin/Role/update.html')"><i class="el-icon-edit" />修改</el-button>
										<el-button size="mini" type="danger" @click="del(scope.row)"  v-if="checkPermission('/admin/Role/delete.html')"><i class="el-icon-delete" />删除</el-button>
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
	</div>
</template>
<script>
	import {
		index,
		updateExt,
		getUpdateInfo,
		del,
	} from '@/api/admin/role'
	import Search from '@/components/common/Search.vue'
	import Page from '@/components/common/Page.vue'
	import TableTool from '@/components/common/TableTool.vue'
	import Add from '@/views/admin/role/add.vue'
	import Update from '@/views/admin/role/update.vue'
	import { confirm,param2Obj } from '@/utils/common'
	export default {
		name:'role',
		components: {
			Search,
			Page,
			TableTool,
			Add,
			Update,
		},
		data(){
			return {
				dialog: {
					addDialogStatus : false,
					updateDialogStatus : false,
				},
				fieldList:[
					{lable:'编号',checked:true},
					{lable:'角色名称',checked:true},
					{lable:'状态',checked:true},
					{lable:'描述',checked:true},
				],
				ids: [],
				single:true,
				multiple:true,
				list: [],
				updateInfo:{},
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
							{type:'Input',label:'角色名称',prop:'name',width:'150px'},
							{type:'Select',label:'状态',prop:'status',data:[{"key":"正常","val":"1"},{"key":"禁用","val":"0"}],width:'150px'},
						]
					}
				})
			},
			updateExt(row,field){
				if(row.role_id){
					updateExt({role_id:row.role_id,[field]:row[field]}).then(res => {
						this.$message({message: '操作成功', type: 'success'})
					})
				}
			},
			add(){
				this.dialog.addDialogStatus = true
			},
			update(row){
				let id = row.role_id ? row.role_id : this.ids.join(',')
				getUpdateInfo({role_id:id}).then(res => {
					if(res.status == 200){
						this.dialog.updateDialogStatus = true
						this.updateInfo = res.data
					}
				})
			},
			del(row){
				confirm({content:'确定要操作吗'}).then(() => {
					let ids = row.role_id ? row.role_id : this.ids.join(',')
					del({role_id:ids}).then(res => {
						this.$message({message: res.msg, type: 'success'})
						this.index()
					})
				}).catch(() => {})
			},
			selection(selection) {
				this.ids = selection.map(item => item.role_id)
				this.single = selection.length != 1
				this.multiple = !selection.length
			},
			handleRowClick(row, rowIndex,event){
				if(event.target.className !== 'el-input__inner'){
					this.$refs.multipleTable.toggleRowSelection(row)
				}
			},
			rowClass ({ row, rowIndex }) {
				for(let i=0;i<this.ids.length;i++) {
					if (row.role_id === this.ids[i]) {
						return 'rowLight'
					}
				}
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