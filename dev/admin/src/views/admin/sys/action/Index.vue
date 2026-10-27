<template>
  <div>
    <el-card shadow="never" class="card">
      <template #header><div class="clearfix">
        <span>{{menu_title}} 方法列表</span>
        <el-button size="small" @click="backup" type="primary" style="float:right"><i class="el-icon-back"></i>返回</el-button>
      </div></template>
      <div v-if="this.$route.query.app_type == 1" id="charts_one" style="width:100%;min-height:500px">
          <!--按钮开始-->
          <div class="btn-group" style="margin-bottom:10px; margin-left:-3px">
            <div>
                <el-button type="success" size="small"  @click="addAdmin()"><i class="el-icon-plus"></i>新增</el-button>
                <el-button type="danger" size="small"  :disabled="multiple" @click="deleteAction(ids)"><i class="el-icon-delete"></i>删除</el-button>
                <el-button type="info" size="small"   @click="create"><i class="el-icon-edit-outline"></i>生成代码</el-button>
                <el-select size="small" style="margin-left:5px; width:150px;" class="select" multiple collapse-tags v-model="select_actions" placeholder="选择方法">
                  <el-option v-for="(item,i) in default_actions" :key="i" :label="item.name" :value="item.type"></el-option>
                </el-select>
              <el-button type="primary" size="small" style="margin-left:5px;" @click="quckCreateAction"><i class="el-icon-check"></i>快速创建</el-button>
            </div>
            <table-tool :fieldList="fieldList" :searchTool="false" @refesh_list="loadlist"></table-tool>
          </div>
          <!--表格数据开始-->
          <el-table row-key="id" :header-cell-style="{ background: '#eef1f6', color: '#606266' }" :row-class-name="rowClass" v-loading="loading" ref="multipleTable" border class="eltable" :data="list" @selection-change="selection"  @row-click="handleRowClick" style="width: 100%"  >
            <el-table-column align="center" type="selection" width="42"></el-table-column>
            <el-table-column v-if="fieldList[0].checked" property="id" label="编号" width="70"></el-table-column>
            <el-table-column v-if="fieldList[1].checked" property="name" label="操作名称">
              <template #default="scope">
                <el-input size="small" placeholder="操作名称" @blur.stop="update(scope.row,'name')" v-model="scope.row.name"></el-input>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[2].checked" property="action_name" label="方法名称">
              <template #default="scope">
                <el-input size="small" placeholder="方法名称" @blur.stop="update(scope.row,'action_name')" v-model="scope.row.action_name"></el-input>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[3].checked" property="type" width="150" label="方法类型">
              <template #default="scope">
                <el-select v-model="scope.row.type" size="small" @change="update(scope.row,'type')" clearable filterable placeholder="请选择">
                  <el-option v-for="item in action" :key="item.name" :label="item.name" :value="item.type"></el-option>
                </el-select>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[5].checked"  align="center" property="server_create_status" label="方法生成">
              <template #default="scope">
                <el-switch @change="update(scope.row,'server_create_status')" :active-value="1" :inactive-value="0" v-model="scope.row.server_create_status"></el-switch>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[5].checked" align="center" property="vue_create_status" label="vue生成">
              <template #default="scope">
                <el-switch @change="update(scope.row,'vue_create_status')" :active-value="1" :inactive-value="0" v-model="scope.row.vue_create_status"></el-switch>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[5].checked" align="center" property="group_button_status" label="按钮组显示">
              <template #default="scope">
                <el-switch @change="update(scope.row,'group_button_status')" :active-value="1" :inactive-value="0" v-model="scope.row.group_button_status"></el-switch>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[5].checked" align="center" property="list_button_status" label="按钮列表显示">
              <template #default="scope">
                <el-switch @change="update(scope.row,'list_button_status')" :active-value="1" :inactive-value="0" v-model="scope.row.list_button_status"></el-switch>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[6].checked" property="button_color" width="120" label="按钮背景色">
              <template #default="scope">
                <el-select v-model="scope.row.button_color" @change="update(scope.row,'button_color')" clearable size="small" filterable placeholder="请选择">
                  <el-option key="1" style="background:#409eff" label="primary" value="primary"></el-option>
                  <el-option key="2" style="background:#67c23a" label="success" value="success"></el-option>
                  <el-option key="3" style="background:#909399" label="info" value="info"></el-option>
                  <el-option key="4" style="background:#e6a23c" label="warning" value="warning"></el-option>
                  <el-option key="5" style="background:#f56c6c" label="danger" value="danger"></el-option>
                </el-select>
              </template>
            </el-table-column>
            <el-table-column align="center" property="dialog_size" label="配置信息">
              <template #default="scope">
                <el-input v-if="scope.row.type == 7" style="width:80px" size="small" placeholder="状态值" @blur.stop="update(scope.row,'status_val')" v-model="scope.row.status_val"></el-input>
                <el-input v-else style="width:80px" size="small" placeholder="弹窗大小" @blur.stop="update(scope.row,'dialog_size')" v-model="scope.row.dialog_size"></el-input>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[9].checked" align="center" property="sortid" label="排序">
              <template #default="scope">
                <el-input style="width:52px" @blur.stop="update(scope.row,'sortid')"  size="small" placeholder="排序" v-model="scope.row.sortid"></el-input>
              </template>
            </el-table-column>
            <el-table-column :fixed="$store.getters.device !== 'mobile'?'right':false" label="操作" width="120">
              <template #default="scope">
                <el-button size="small" style="padding:7px 5px" type="primary" @click="editorAdmin(scope.row)"><i class="el-icon-edit-outline" />修改</el-button>
                <el-button size="small" style="padding:7px 5px" @click="deleteAction(scope.row)" type="danger"><i class="el-icon-delete" />删除</el-button>
              </template>
            </el-table-column>
          </el-table> 
          <Page :total="page_data.total" v-model:page="page_data.page" v-model:limit="page_data.limit" @pagination="loadlist" />
      </div>

      <div v-if="this.$route.query.app_type == 2" id="charts_one" style="width:100%;min-height:500px">
          <!--按钮开始-->
          <div class="btn-group" style="margin-bottom:10px; margin-left:-3px">
            <div>
                <el-button type="success" size="small"  @click="addApi()"><i class="el-icon-plus"></i>新增</el-button>
                <el-button type="danger" size="small"  :disabled="multiple" @click="deleteAction(ids)"><i class="el-icon-delete"></i>删除</el-button>
                <el-button type="info" size="small"   @click="create"><i class="el-icon-edit-outline"></i>生成代码</el-button>
            </div>
            <table-tool :fieldList="fieldList" :searchTool="false" @refesh_list="loadlist"></table-tool>
          </div>
          <!--表格数据开始-->
          <el-table row-key="id" :header-cell-style="{ background: '#eef1f6', color: '#606266' }" :row-class-name="rowClass" v-loading="loading" ref="multipleTable" border class="eltable" :data="list" @selection-change="selection"  @row-click="handleRowClick" style="width: 100%"  >
            <el-table-column align="center" type="selection" width="42"></el-table-column>
            <el-table-column v-if="fieldList[0].checked" property="id" label="编号" width="70"></el-table-column>
            <el-table-column v-if="fieldList[1].checked" property="name" label="操作名称">
              <template #default="scope">
                <el-input size="small" placeholder="操作名称" @blur.stop="update(scope.row,'name')" v-model="scope.row.name"></el-input>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[2].checked" property="action_name" label="方法名称">
              <template #default="scope">
                <el-input size="small" placeholder="方法名称" @blur.stop="update(scope.row,'action_name')" v-model="scope.row.action_name"></el-input>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[3].checked" property="type" width="150" label="方法类型">
              <template #default="scope">
                <el-select v-model="scope.row.type" size="small" @change="update(scope.row,'type')" clearable filterable placeholder="请选择">
                  <el-option v-for="item in apiaction" :key="item.name" :label="item.name" :value="item.type"></el-option>
                </el-select>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[5].checked"  align="center" property="server_create_status" label="方法生成">
              <template #default="scope">
                <el-switch @change="update(scope.row,'server_create_status')" :active-value="1" :inactive-value="0" v-model="scope.row.server_create_status"></el-switch>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[5].checked" align="center" property="api_auth" label="token鉴权">
              <template #default="scope">
                <el-switch @change="update(scope.row,'api_auth')" :active-value="1" :inactive-value="0" v-model="scope.row.api_auth"></el-switch>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[5].checked" align="center" property="sms_auth" label="短信鉴权">
              <template #default="scope">
                <el-switch @change="update(scope.row,'sms_auth')" :active-value="1" :inactive-value="0" v-model="scope.row.sms_auth"></el-switch>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[5].checked" align="center" property="img_auth" label="图片验证码鉴权">
              <template #default="scope">
                <el-switch @change="update(scope.row,'img_auth')" :active-value="1" :inactive-value="0" v-model="scope.row.img_auth"></el-switch>
              </template>
            </el-table-column>
            <el-table-column align="center" property="cache_time" label="缓存时间">
              <template #default="scope">
                <el-input size="small" placeholder="缓存时间单位秒" @blur.stop="update(scope.row,'cache_time')" v-model="scope.row.cache_time"></el-input>
              </template>
            </el-table-column>
            <el-table-column align="center" property="sortid" label="排序">
              <template #default="scope">
                <el-input style="width:52px" @blur.stop="update(scope.row,'sortid')"  size="small" placeholder="排序" v-model="scope.row.sortid"></el-input>
              </template>
            </el-table-column>
            <el-table-column :fixed="$store.getters.device !== 'mobile'?'right':false" label="操作" width="120">
              <template #default="scope">
                <el-button size="small" style="padding:7px 5px" type="primary" @click="editorApi(scope.row)"><i class="el-icon-edit-outline" />修改</el-button>
                <el-button size="small" style="padding:7px 5px" @click="deleteAction(scope.row)" type="danger"><i class="el-icon-delete" />删除</el-button>
              </template>
            </el-table-column>
          </el-table> 
          <Page :total="page_data.total" v-model:page="page_data.page" v-model:limit="page_data.limit" @pagination="loadlist" />
      </div>
    </el-card>

    <!--添加-->
    <AddAdmin v-model:show="dialog.addAdminDialogStatus" :action="action" size="small" @refesh_list="loadlist"></AddAdmin>
    <AddApi v-model:show="dialog.addApiDialogStatus" :action="apiaction" size="small" @refesh_list="loadlist"></AddApi>

    <!--修改-->
    <UpdateAdmin v-model:show="dialog.updateAdminDialogStatus" :info="info" :action="action" size="small" @refesh_list="loadlist"></UpdateAdmin>
    <UpdateApi v-model:show="dialog.updateApiDialogStatus" :info="info" :action="apiaction" size="small" @refesh_list="loadlist"></UpdateApi>
  </div>
</template>
<script>
import Sortable from 'sortablejs'
import { confirm } from '@/utils/common'
import {actionList,deleteAction,updateActionExt,getActionInfo,updateActionSort,quckCreateAction,create} from '../sys'
import Page from '@/components/common/Page.vue'
import TableTool from '@/components/common/TableTool.vue'
import AddAdmin from './admin/Add.vue'
import UpdateAdmin from './admin/Update.vue'
import AddApi from './api/Add.vue'
import UpdateApi from './api/Update.vue'
export default {
  name:'Action',
  components: { Page,AddAdmin,UpdateAdmin,AddApi,UpdateApi,TableTool,Sortable},
  data() {
    return {
      dialog: {
        addAdminDialogStatus : false,
        updateAdminDialogStatus : false,
        addApiDialogStatus : false,
        updateApiDialogStatus : false,
      },
      fieldList:[
        {lable:'编号',checked:false},
        {lable:'操作名称',checked:true},
        {lable:'方法名称',checked:true},
        {lable:'方法类型',checked:true},
        {lable:'方法生成',checked:true},
        {lable:'vue生成',checked:true},
        {lable:'按钮组显示',checked:true},
        {lable:'按钮列表显示',checked:true},
        {lable:'按钮背景色',checked:true},
        {lable:'配置信息',checked:true},
        {lable:'排序',checked:true},
      ],
      menu_title:'',
      ids: {},
      single:true,
      multiple: true,
      list: [],
      action:[],
      apiaction:[],
      info:{},
      select_actions:[],
      default_actions:[],
      loading: false,
      page_data: {
        limit: 20,
        page: 1,
        total: 50,
      },
    };
  },
  methods: {
    addAdmin() {
       this.dialog.addAdminDialogStatus = true;
    },
    editorAdmin(row) {
      let id = row.id ? row.id : this.ids.join(',')
      getActionInfo({id:id,menu_id:this.$route.query.menu_id}).then(res => {
        if(res.status == 200){
            this.dialog.updateAdminDialogStatus = true
            this.info = res.data
        }else{
            this.$message.error('获取信息失败!')
        }
      })
    },
    addApi() {
       this.dialog.addApiDialogStatus = true;
    },
    editorApi(row) {
      let id = row.id ? row.id : this.ids.join(',')
      getActionInfo({id:id,menu_id:this.$route.query.menu_id}).then(res => {
        if(res.status == 200){
            this.dialog.updateApiDialogStatus = true
            this.info = res.data
        }else{
            this.$message.error('获取信息失败!')
        }
      })
    },
    selection(selection) {
       this.ids = selection.map(item => item.id)
       this.single = selection.length != 1
       this.multiple = !selection.length
    },
    handleRowClick(row, rowIndex){
       this.$refs.multipleTable.toggleRowSelection(row)
    },
    rowClass ({ row, rowIndex }) {
      for(let i=0;i<this.ids.length;i++) {
          if (row.id === this.ids[i]) {
              return 'rowLight'
          }
      }
    },
    rowDrop() {
      const table = this.$refs.multipleTable
      const el = table && table.$el && table.$el.querySelectorAll('.el-table__body-wrapper > table > tbody')[0]
      if (!el) return
      this.sortable = Sortable.create(el, {
        ghostClass: 'sortable-ghost',
        setData: function (dataTransfer) {
          dataTransfer.setData('Text', '')
        },
        onEnd: e => {
        const targetRow = this.list.splice(e.oldIndex, 1)[0]
        this.list.splice(e.newIndex, 0, targetRow)
        let currentId = this.list[e.newIndex].id
        let preId,nextId
        if( this.list[e.newIndex-1]){
          preId = this.list[e.newIndex-1].id
        }else {
          preId = ""
        }
        if( this.list[e.newIndex+1]){
            nextId = this.list[e.newIndex+1].id
        }else {
            nextId = ""
        }
        updateActionSort({currentId:currentId,preId:preId,nextId:nextId,menu_id:this.$route.query.menu_id}).then(res => {
          if(res.status == 200){
              this.loadlist()
              this.$message({message: '操作成功', type: 'success'})
          }else{
              this.$message.error('操作失败!')
          }
        })
        }
      })
    },
    backup(){
      this.$router.push({path:'/admin/Menu/index.html',query: {
        app_id:this.$route.query.app_id,
        app_type:this.$route.query.app_type,
      }})
    },
    deleteAction(row) {
        confirm({content:'确定删除选定项'}).then(() => {
            let ids = row.id ? row.id : this.ids
            deleteAction({id:ids,menu_id:this.$route.query.menu_id}).then(res => {
              if(res.status == 200){
                  this.$message({message: '操作成功', type: 'success'})
                  this.loadlist()
              }else{
                  this.$message.error('操作失败!')
              }
            })
        }).catch(() => {})
    },
    update(row,field){
      updateActionExt({id:row.id,[field]:row[field]}).then(res => {
        if(res.status !== 200){
            this.$message.error('操作失败!')
        }else{
            this.$message({message: '操作成功', type: 'success'})
        }
      })
    },
    quckCreateAction(){
      if(this.select_actions.length == 0){
          this.$message.error('请选择创建方法!')
      }else{
          confirm({content:'确定创建方法吗'}).then(() => {
            quckCreateAction({actions:this.select_actions,menu_id:this.$route.query.menu_id}).then(res => {
              if(res.status == 200){
                  this.loadlist()
                  this.select_actions = []
                  this.$message({message: res.exits_status?'操作成功,部分方法已存在 无法创建!':'操作成功!', type: 'success'})
              }else{
                  this.$message.error('操作失败!')
              }
            })
          }).catch(() => {})
      }
    },
    create(){
      confirm({content:'确定生成吗?'}).then(() => {
        create({menu_id:this.$route.query.menu_id}).then(res => {
            this.$message({message: '操作成功', type: 'success'})
        })
      }).catch(() => {})
    },
    loadlist() { 
      let param = {
        limit:this.page_data.limit,
        page:this.page_data.page,
        menu_id:this.$route.query.menu_id
      }
      this.loading = true
      actionList(param).then(res => {
          this.menu_title = res.menu_title
          this.list = res.data.data
          this.action =  res.actionList.filter(item=>item.show_admin)
          this.apiaction =  res.actionList.filter(item=>item.show_api)
          this.default_actions = res.actionList.filter(item=>item.default_create)
          this.page_data.total = res.data.total
          this.loading = false
      })
    },
  },
  mounted() {
    this.loadlist()
    this.rowDrop()
  },
};
</script>
<style lang="scss">
@import '@/assets/scss/common.scss'
</style>