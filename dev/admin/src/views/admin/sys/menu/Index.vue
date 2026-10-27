<template>
  <div>
    <el-card shadow="never" class="card">
        <el-row>
          <el-menu :default-active="active" router class="el-menu-demo" @select="selectMenu" mode="horizontal">
              <el-menu-item v-for="item in app_list" :key="item.app_id" :index="item.url">
                <router-link :to="item.url">
                  {{item.app_type == 3 ? item.application_name+'内容模型' : item.application_name}}
                </router-link>
              </el-menu-item>
          </el-menu>
        </el-row>
        <el-row v-if="this.$route.query.app_type == 1 || !this.$route.query.app_type" style="margin-bottom:10px; margin-top:11px;">
          <el-col :span="21">
              <el-button type="success" size="small"  @click="addAdmin()"><i class="el-icon-plus"></i>创建</el-button>
              <el-button type="danger" size="small"  @click="deleteMenu()"><i class="el-icon-delete"></i>卸载</el-button>
              <el-button type="warning" size="small"  :disabled="single" @click="field"><i class="el-icon-plus"></i>字段管理</el-button>  
              <el-button type="warning" size="small"  :disabled="single" @click="action"><i class="el-icon-plus"></i>方法管理</el-button>
              <el-button type="info" size="small"  :disabled="single" @click="create"><i class="el-icon-edit-outline"></i>生成代码</el-button>
              <el-select size="small" style="margin-left:5px; width:120px;" class="select" clearable v-model="appid" placeholder="复制到">
                  <el-option v-for="(item,i) in copy_app_list" :key="i" :label="item.application_name" :value="item.app_id"></el-option>
              </el-select>
              <el-button type="primary" size="small" style="margin-left:5px;" @click="copyMenu()"><i class="el-icon-check"></i>确定</el-button>
              <el-select size="small" style="margin-left:5px; width:130px" class="select" @change="selectField" v-model="connect" placeholder="请选择链接库">
                  <el-option v-for="item in connects" :key="item" :label="item" :value="item"></el-option>
              </el-select>
              <el-select size="small" style="margin-left:5px; width:130px" class="select" v-model="tablename" placeholder="请选择数据表">
                  <el-option v-for="(item,i) in tableList" :key="i" :label="item" :value="item"></el-option>
              </el-select>
              <el-button type="primary" size="small" style="margin-left:5px;" @click="createByTable"><i class="el-icon-check"></i>根据表生成</el-button>
          </el-col>
          <el-col :span="3" style="text-align:right">
            <table-tool  :fieldList="fieldList"  @refesh_list="loadlist"  :searchTool="false" :expand_status="true" :expand="expand" @toggleRowExpansion="toggleRowExpansion"></table-tool>
          </el-col>
        </el-row>

        <el-row v-if="this.$route.query.app_type == 2" style="margin-bottom:10px; margin-top:11px;">
          <el-col :span="21">
              <el-button type="success" size="small"  @click="addApi()"><i class="el-icon-plus"></i>创建</el-button>
              <el-button type="danger" size="small"  @click="deleteMenu()"><i class="el-icon-delete"></i>卸载</el-button>
              <el-button type="warning" size="small"  :disabled="single" @click="field"><i class="el-icon-plus"></i>字段管理</el-button>  
              <el-button type="warning" size="small"  :disabled="single" @click="action"><i class="el-icon-plus"></i>接口方法</el-button>
              <el-button type="info" size="small"  :disabled="single" @click="create"><i class="el-icon-edit-outline"></i>生成代码</el-button>
              <el-select size="small" style="margin-left:5px; width:130px" class="select" @change="selectField" v-model="connect" placeholder="请选择链接库">
                  <el-option v-for="item in connects" :key="item" :label="item" :value="item"></el-option>
              </el-select>
              <el-select size="small" style="margin-left:5px; width:130px" class="select" v-model="tablename" placeholder="请选择数据表">
                  <el-option v-for="(item,i) in tableList" :key="i" :label="item" :value="item"></el-option>
              </el-select>
              <el-button type="primary" size="small" style="margin-left:5px;" @click="createByTable"><i class="el-icon-check"></i>根据表生成</el-button>
          </el-col>
          <el-col :span="3" style="text-align:right">
            <table-tool  :fieldList="fieldList"  @refesh_list="loadlist"  :searchTool="false" :expand_status="true" :expand="expand" @toggleRowExpansion="toggleRowExpansion"></table-tool>
          </el-col>
        </el-row>

        <el-row v-if="this.$route.query.app_type == 3" style="margin-bottom:10px; margin-top:11px;">
          <el-col :span="21">
              <el-button type="success" size="small"  @click="addCms()"><i class="el-icon-plus"></i>创建</el-button>
              <el-button type="danger" size="small"  @click="deleteMenu()"><i class="el-icon-delete"></i>卸载</el-button>
              <el-button type="warning" size="small"  :disabled="single" @click="field"><i class="el-icon-plus"></i>字段管理</el-button>  
          </el-col>
          <el-col :span="3" style="text-align:right">
            <table-tool  :fieldList="fieldList"  @refesh_list="loadlist"  :searchTool="false" :expand_status="true" :expand="expand" @toggleRowExpansion="toggleRowExpansion"></table-tool>
          </el-col>
        </el-row>
        <!--表格数据开始-->
        <el-table v-show="this.$route.query.app_type == 1 || !this.$route.query.app_type" :header-cell-style="{ background: '#eef1f6', color: '#606266' }" v-loading="loading" highlight-current-row :default-expand-all="expand" @current-change="handleCurrentChange" :tree-props="{children: 'children'}" row-key="menu_id" ref="multipleTable" border class="eltable" :data="list"   style="width: 100%"  >
          <el-table-column  property="menu_id" align="center" type='' label="编号" width="70"></el-table-column>
          <el-table-column v-if="fieldList[1].checked"  property="title" width="240" label="菜单名称"></el-table-column>
          <el-table-column v-if="fieldList[2].checked" property="controller_name" label="控制器名"></el-table-column>
          <el-table-column v-if="fieldList[3].checked" property="table_name" label="数据表名"></el-table-column>
          <el-table-column v-if="fieldList[4].checked" align="center" property="create_code" label="生成代码">
            <template #default="scope">
              <el-switch :active-value="1" :inactive-value="0" @change="update(scope.row,'create_code')" v-model="scope.row.create_code"></el-switch>
            </template>
          </el-table-column>
          <el-table-column v-if="fieldList[5].checked" align="center" property="status" label="菜单显示">
            <template #default="scope">
              <el-switch :active-value="1" :inactive-value="0" @change="update(scope.row,'status')" v-model="scope.row.status"></el-switch>
            </template>
          </el-table-column>
          <el-table-column v-if="fieldList[6].checked" align="center" property="create_table" label="创建数据库表">
            <template #default="scope">
              <el-tag size="small" :type="scope.row.create_table=='1'?'success':'info'" effect="dark">{{ scope.row.create_table == '1' ? '是' : '否' }}</el-tag>
            </template>
          </el-table-column>
          <el-table-column v-if="fieldList[7].checked" width="90" align="center" property="sortid" label="排序">
            <template #default="scope">
              <el-input style="width:60px;" size="small" placeholder="排序" @blur.stop="update(scope.row,'sortid')" v-model="scope.row.sortid"></el-input>
            </template>
          </el-table-column>
          <el-table-column label="操作" :fixed="$store.getters.device !== 'mobile'?'right':false" align="center" width="180">
            <template #default="scope">
              <el-button size="small" @click="editorAdmin(scope.row)" type="primary" ><i class="el-icon-edit-outline" />修改</el-button>
              <el-button size="small" @click="deleteMenu(scope.row)" type="danger"><i class="el-icon-delete" />卸载</el-button>
            </template>
          </el-table-column>
        </el-table> 

        <el-table v-show="this.$route.query.app_type == 2" :header-cell-style="{ background: '#eef1f6', color: '#606266' }" v-loading="loading" highlight-current-row :default-expand-all="expand" @current-change="handleCurrentChange" :tree-props="{children: 'children'}" row-key="menu_id" ref="multipleTable" border class="eltable" :data="list"   style="width: 100%"  >
          <el-table-column  property="menu_id" align="center" type='' label="编号" width="70"></el-table-column>
          <el-table-column v-if="fieldList[1].checked"  property="title" width="240" label="接口分组名称"></el-table-column>
          <el-table-column v-if="fieldList[2].checked" property="controller_name" label="控制器名"></el-table-column>
          <el-table-column v-if="fieldList[3].checked" property="table_name" label="数据表名"></el-table-column>
          <el-table-column v-if="fieldList[4].checked" align="center" property="create_code" label="生成代码">
            <template #default="scope">
              <el-switch :active-value="1" :inactive-value="0" @change="update(scope.row,'create_code')" v-model="scope.row.create_code"></el-switch>
            </template>
          </el-table-column>
          <el-table-column v-if="fieldList[6].checked" align="center" property="create_table" label="创建数据库表">
            <template #default="scope">
              <el-tag size="small" :type="scope.row.create_table=='1'?'success':'info'" effect="dark">{{ scope.row.create_table == '1' ? '是' : '否' }}</el-tag>
            </template>
          </el-table-column>
          <el-table-column v-if="fieldList[7].checked" width="90" align="center" property="sortid" label="排序">
            <template #default="scope">
              <el-input style="width:60px;" size="small" placeholder="排序" @blur.stop="update(scope.row,'sortid')" v-model="scope.row.sortid"></el-input>
            </template>
          </el-table-column>
          <el-table-column label="操作" :fixed="$store.getters.device !== 'mobile'?'right':false" align="center" width="180">
            <template #default="scope">
              <el-button size="small" @click="editorApi(scope.row)" type="primary" ><i class="el-icon-edit-outline" />修改</el-button>
              <el-button size="small" @click="deleteMenu(scope.row)" type="danger"><i class="el-icon-delete" />卸载</el-button>
            </template>
          </el-table-column>
        </el-table> 

        <!--表格数据开始-->
        <el-table v-show="this.$route.query.app_type == 3" :header-cell-style="{ background: '#eef1f6', color: '#606266' }" v-loading="loading" highlight-current-row :default-expand-all="expand" @current-change="handleCurrentChange" :tree-props="{children: 'children'}" row-key="menu_id" ref="multipleTable" border class="eltable" :data="list"   style="width: 100%"  >
          <el-table-column  property="menu_id" align="center" type='' label="编号" width="70"></el-table-column>
          <el-table-column v-if="fieldList[1].checked"  property="title" width="240" label="模型名称"></el-table-column>
          <el-table-column v-if="fieldList[3].checked" property="table_name" label="数据表名"></el-table-column>
          <el-table-column v-if="fieldList[5].checked" align="center" property="status" label="状态">
            <template #default="scope">
              <el-switch :active-value="1" :inactive-value="0" @change="update(scope.row,'status')" v-model="scope.row.status"></el-switch>
            </template>
          </el-table-column>
          <el-table-column v-if="fieldList[7].checked" width="90" align="center" property="sortid" label="排序">
            <template #default="scope">
              <el-input style="width:60px;" size="small" placeholder="排序" @blur.stop="update(scope.row,'sortid')" v-model="scope.row.sortid"></el-input>
            </template>
          </el-table-column>
          <el-table-column label="操作" :fixed="$store.getters.device !== 'mobile'?'right':false" align="center" width="180">
            <template #default="scope">
              <el-button size="small" @click="editorCms(scope.row)" type="primary" ><i class="el-icon-edit-outline" />修改</el-button>
              <el-button size="small" @click="deleteMenu(scope.row)" type="danger"><i class="el-icon-delete" />卸载</el-button>
            </template>
          </el-table-column>
        </el-table> 
    </el-card>
    <!--添加-->
    <AdminAdd  v-model:show="dialog.addAdminDialogStatus" :app_id="app_id" :page_type_list="page_type_list" :list="list" :connects="connects" size="small" @refesh_list="loadlist"></AdminAdd>
    <CmsAdd v-model:show="dialog.addCmsDialogStatus" :app_id="app_id" :page_type_list="page_type_list" :list="list" :connects="connects" size="small" @refesh_list="loadlist"></CmsAdd>
    <ApiAdd v-model:show="dialog.addApiDialogStatus" :app_id="app_id" :page_type_list="page_type_list" :list="list" :connects="connects" size="small" @refesh_list="loadlist"></ApiAdd>

    <!--修改-->
    <AdminUpdate v-model:show="dialog.updateAdminDialogStatus" :info="info" :page_type_list="page_type_list" :app_id="app_id" :list="list" :connects="connects"  size="small" @refesh_list="loadlist"></AdminUpdate>
    <CmsUpdate v-model:show="dialog.updateCmsDialogStatus" :info="info" :page_type_list="page_type_list" :app_id="app_id" :list="list" :connects="connects"  size="small" @refesh_list="loadlist"></CmsUpdate>
    <ApiUpdate v-model:show="dialog.updateApiDialogStatus" :info="info" :page_type_list="page_type_list" :app_id="app_id" :list="list" :connects="connects"  size="small" @refesh_list="loadlist"></ApiUpdate>
  </div>
</template>
<script>
import {getMenuList,getMenuInfo,getTableList,updateMenuExt,deleteMenu,create,createByTable,copyMenu} from '../sys'
import TableTool from '@/components/common/TableTool.vue'
import AdminAdd from './admin/Add.vue'
import AdminUpdate from './admin/Update.vue'
import CmsAdd from './cms/Add.vue'
import CmsUpdate from './cms/Update.vue'
import ApiAdd from './api/Add.vue'
import ApiUpdate from './api/Update.vue'
import { confirm,param2Obj} from '@/utils/common'
export default {
  name:'Menu',
  components: { AdminAdd,AdminUpdate,CmsAdd,CmsUpdate,ApiAdd,ApiUpdate,TableTool},
  data() {
    return {
      dialog: {
        addAdminDialogStatus : false,
        updateAdminDialogStatus : false,
        addCmsDialogStatus : false,
        updateCmsDialogStatus : false,
        addApiDialogStatus : false,
        updateApiDialogStatus : false,
      },
      expand:true,
      fieldList:[
        {lable:'编号',checked:false},
        {lable:'菜单名称',checked:true},
        {lable:'控制器名',checked:true},
        {lable:'数据表名',checked:true},
        {lable:'生成代码',checked:true},
        {lable:'菜单显示',checked:true},
        {lable:'创建数据库表',checked:true},
        {lable:'排序',checked:true},
      ],
      id:'',
      single:true,
      list: [],
      connects:[],
      connect:'',
      tablename:'',
      tableList:[],
      page_type_list:[],
      app_id:'1',
      appid:'',
      app_type:1,
      app_list:[],
      copy_app_list:[],
      info:{},
      loading: false,
      active:'/admin/Menu/index.html?app_id=1&app_type=1'
    };
  },
  methods: {
    addAdmin() {
       this.dialog.addAdminDialogStatus = true
    },
    editorAdmin(row) {
      getMenuInfo({menu_id:row.menu_id}).then(res => {
          this.dialog.updateAdminDialogStatus = true
          this.info = res.data
      })
    },
    addCms() {
       this.dialog.addCmsDialogStatus = true
    },
    editorCms(row) {
      getMenuInfo({menu_id:row.menu_id}).then(res => {
          this.dialog.updateCmsDialogStatus = true
          this.info = res.data
      })
    },
    addApi() {
       this.dialog.addApiDialogStatus = true
    },
    editorApi(row) {
      getMenuInfo({menu_id:row.menu_id}).then(res => {
          this.dialog.updateApiDialogStatus = true
          this.info = res.data
      })
    },
    field(){
      let app_type = this.$route.query.app_type ? this.$route.query.app_type : this.app_type
      this.$router.push({path:'/admin/Field/index.html',query: {app_id:this.app_id,app_type:app_type,menu_id:this.id}})
    },
    action(){
      let app_type = this.$route.query.app_type ? this.$route.query.app_type : this.app_type
      this.$router.push({path:'/admin/Action/index.html',query: {app_id:this.app_id,app_type:app_type,menu_id:this.id}})
    },
    handleCurrentChange(val) {
        this.id = val.menu_id
        this.single = false
    },
    toggleRowExpansion(){
      this.expand = !this.expand
      this.list.forEach(item=>{
          this.$refs.multipleTable.toggleRowExpansion(item,this.expand)
      })
    },
    selectField(val){
      getTableList({connect:this.connect}).then(res => {
          this.tableList = res.data
      })
    },
    selectMenu(index){
      this.app_id = param2Obj(index).app_id
      this.app_type = param2Obj(index).app_type
      this.active = index
      this.loadlist()
    },
    deleteMenu() {
        confirm({content:'确定要卸载吗?'}).then(() => {
            deleteMenu({menu_id:this.id}).then(res => {
              this.$message({message: '操作成功', type: 'success'})
              this.loadlist()
            })
        }).catch(() => {})
    },
    update(row,field){
      updateMenuExt({menu_id:row.menu_id,[field]:row[field]}).then(res => {
          this.$message({message: '操作成功', type: 'success'})
      })
    },
    create(){
      confirm({content:'确定生成吗?'}).then(() => {
        create({menu_id:this.id}).then(res => {
            this.$message({message: '操作成功', type: 'success'})
        })
      }).catch(() => {})
    },
    createByTable(){
      if(this.tablename == ''){
        this.$message.error('请选择生成表')
      }else{
        confirm({content:'确定生成吗?'}).then(() => {
          createByTable({connect:this.connect,table_name:this.tablename,app_id:this.app_id,app_type:this.app_type}).then(res => {
              this.$message({message: '操作成功', type: 'success'})
              this.loadlist()
          })
        }).catch(() => {})
      }
    },
    copyMenu(){
      if(this.id == '' || this.appid == ''){
        this.$message.error('请选择菜单或者应用')
      }else{
         confirm({content:'确定复制吗?'}).then(() => {
            copyMenu({appid:this.appid,menu_id:this.id}).then(res => {
                this.$message({message: '操作成功', type: 'success'})
                this.loadlist()
            })
        }).catch(() => {})
      }
     
    },
    loadlist() { 
      this.loading = true
      getMenuList({app_id:this.app_id}).then(res => {
          this.list = res.list
          this.connects = res.connects
          this.connect = res.defaultConnect
          this.tableList = res.tableList
          this.app_list = res.app_list
          this.copy_app_list = res.app_list.filter(item=>item.app_type !== 3)
          this.page_type_list = res.page_type_list
          this.loading = false
      })
    },
  },
  mounted() {
      let app_type = this.$route.query.app_type
      let app_id = this.$route.query.app_id
      if(app_type){
        this.app_type = app_type
      }
      if(app_id){
        this.app_id = app_id
        this.active = this.$route.fullPath
      }else{
        this.active = '/admin/Menu/index.html?app_id=1&app_type=1'
      }
      
      this.loadlist()
  },
};
</script>
<style  lang="scss">
@import '@/assets/scss/common.scss'
</style>