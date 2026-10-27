<template>
  <div>
    <el-row :gutter="10">
      <el-col :span="24">
        <el-card shadow="never"
                 class="card">
          <div id="charts_one"
               style="width:100%;min-height:80vh">
            <Search size='small'
                    :searchVisible="searchVisible"
                    v-model:searchData="searchData"
                    :searchForm="searchForm"
                    @refesh_list="index"></Search>
            <div class="btn-group"
                 style="margin-bottom:11px;">
              <div>
                <el-button type="danger"
                           :disabled="multiple"
                           size="small"
                          
                           @click="del(ids)"
                           v-if="checkPermission('/admin/Opinion/delete.html')"><i class="el-icon-delete"></i>删除</el-button>
              </div>
              <table-tool v-model:searchVisible="searchVisible"
                          :fieldList="fieldList"
                          @refesh_list="index"
                          :searchTool="true"></table-tool>
            </div>
            <el-table :row-class-name="rowClass"
                      @selection-change="selection"
                      @row-click="handleRowClick"
                      row-key="id"
                      :header-cell-style="{ background: '#eef1f6', color: '#606266' }"
                      v-loading="loading"
                      ref="multipleTable"
                      border
                      class="eltable"
                      :data="list"
                      style="width: 100%">
              <el-table-column align="center"
                               type="selection"
                               width="42"></el-table-column>
              <el-table-column align="center"
                               type=''
                               v-if="fieldList[0].checked"
                               property="id"
                               label="ID"
                               show-overflow-tooltip
                               width="70">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[1].checked"
                               property="content"
                               label="内容"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[2].checked"
                               property="mobile"
                               label="手机"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[3].checked"
                               property="images"
                               label="图片"
                               show-overflow-tooltip
                               width="">
                <template #default="scope">
                  <div v-if="scope.row.images && scope.row.images.indexOf('[') != -1"
                       class="demo-image__preview">
                    <el-image style="margin-right:5px"
                              v-for="(item,i) in JSON.parse(scope.row.images)"
                              class="table_list_pic"
                              :src="item"
                              :key="i"
                              :preview-src-list="[item]"></el-image>
                  </div>
                </template>
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[4].checked"
                               property="user_type"
                               label="用户类型"
                               show-overflow-tooltip
                               width="">
                <template #default="scope">
                  <span v-if="scope.row.user_type == '1'">用户</span>
                  <span v-if="scope.row.user_type == '2'">技师</span>
                </template>
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[5].checked"
                               property="type"
                               label="类型"
                               show-overflow-tooltip
                               width="">
                <template #default="scope">
                  <el-tag type="primary"
                          v-if="scope.row.type == '1'"
                          size="small"
                          effect="dark">bug</el-tag>
                  <el-tag type="primary"
                          v-if="scope.row.type == '2'"
                          size="small"
                          effect="dark">意见</el-tag>
                  <el-tag type="primary"
                          v-if="scope.row.type == '3'"
                          size="small"
                          effect="dark">吐槽</el-tag>
                  <el-tag type="primary"
                          v-if="scope.row.type == '4'"
                          size="small"
                          effect="dark">其他</el-tag>
                </template>
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[6].checked"
                               property="create_time"
                               label="时间"
                               show-overflow-tooltip
                               width="">
                <template #default="scope">
                  {{parseTime(scope.row.create_time)}}
                </template>
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[7].checked"
                               property="user.nickname"
                               label="用户昵称"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[8].checked"
                               property="technician.realname"
                               label="技师姓名"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[8].checked"
                               property="reply"
                               label="回复内容"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column :fixed="$store.getters.device !== 'mobile'?'right':false"
                               label="操作"
                               align="center"
                               width="160">
                <template #default="scope">
                  <div v-if="scope.row.id">
                    <el-button size="small"
                               type="primary"
                               @click="update(scope.row)"
                               v-if="checkPermission('/admin/Opinion/update.html')"><i class="el-icon-edit" />回复</el-button>
                    <el-button size="small"
                               type="danger"
                               @click="del(scope.row)"
                               v-if="checkPermission('/admin/Opinion/delete.html')"><i class="el-icon-delete" />删除</el-button>
                  </div>
                </template>
              </el-table-column>
            </el-table>
            <Page :total="page_data.total"
                  v-model:page="page_data.page"
                  v-model:limit="page_data.limit"
                  @pagination="index" />
          </div>
        </el-card>
      </el-col>
    </el-row>
    <!--添加-->
    <Add v-model:show="dialog.addDialogStatus"
         size="small"
         @refesh_list="index"></Add>
    <!--修改-->
    <Update :info="updateInfo"
            v-model:show="dialog.updateDialogStatus"
            size="small"
            @refesh_list="index"></Update>
    <!--查看详情-->
    <Detail :info="detailInfo"
            v-model:show="dialog.detailDialogStatus"
            size="small"
            @refesh_list="index"></Detail>
  </div>
</template>
<script>
import {
  index,
  updateExt,
  getUpdateInfo,
  del,
} from '@/api/admin/opinion'
import Search from '@/components/common/Search.vue'
import Page from '@/components/common/Page.vue'
import TableTool from '@/components/common/TableTool.vue'
import Add from '@/views/admin/opinion/add.vue'
import Update from '@/views/admin/opinion/update.vue'
import Detail from '@/views/admin/opinion/detail.vue'
import { confirm, param2Obj } from '@/utils/common'
export default {
  name: 'opinion',
  components: {
    Search,
    Page,
    TableTool,
    Add,
    Update,
    Detail,
  },
  data () {
    return {
      dialog: {
        addDialogStatus: false,
        updateDialogStatus: false,
        detailDialogStatus: false,
      },
      fieldList: [
        { lable: 'ID', checked: true },
        { lable: '内容', checked: true },
        { lable: '手机', checked: true },
        { lable: '图片', checked: true },
        { lable: '用户类型', checked: true },
        { lable: '类型', checked: true },
        { lable: '时间', checked: true },
        { lable: '用户昵称', checked: true },
        { lable: '技师姓名', checked: true },
      ],
      ids: [],
      single: true,
      multiple: true,
      list: [],
      updateInfo: {},
      detailInfo: {},
      loading: false,
      page_data: {
        limit: 20,
        page: 1,
        total: 20,
      },
      searchVisible: true,
      searchForm: [],
      searchData: {},
    }
  },
  methods: {
    index () {
      let param = { limit: this.page_data.limit, page: this.page_data.page }
      Object.assign(param, this.searchData, param2Obj(this.$route.fullPath))
      this.loading = true
      index(param).then(res => {
        this.list = res.data.data
        this.page_data.total = res.data.total
        this.loading = false
        if (this.page_data.page == 1) {
          this.searchForm = [
            { type: 'Select', label: '用户类型', prop: 'user_type', data: [{ "key": "用户", "val": "1" }, { "key": "技师", "val": "2" }], width: '150px' },
            { type: 'Select', label: '类型', prop: 'type', data: [{ "key": "bug", "val": "1" }, { "key": "意见", "val": "2" }, { "key": "吐槽", "val": "3" }, { "key": "其他", "val": "4" }], width: '150px' },
          ]
        }
      })
    },
    updateExt (row, field) {
      if (row.id) {
        updateExt({ id: row.id, [field]: row[field] }).then(res => {
          this.$message({ message: '操作成功', type: 'success' })
        })
      }
    },
    add () {
      this.dialog.addDialogStatus = true
    },
    update (row) {
      let id = row.id ? row.id : this.ids.join(',')
      getUpdateInfo({ id: id }).then(res => {
        if (res.status == 200) {
          this.dialog.updateDialogStatus = true
          this.updateInfo = res.data
        }
      })
    },
    del (row) {
      confirm({ content: '确定要操作吗' }).then(() => {
        let ids = row.id ? row.id : this.ids.join(',')
        del({ id: ids }).then(res => {
          this.$message({ message: res.msg, type: 'success' })
          this.index()
        })
      }).catch(() => { })
    },
    detail (row) {
      this.dialog.detailDialogStatus = true
      this.detailInfo = { id: row.id ? row.id : this.ids.join(',') }
    },
    selection (selection) {
      this.ids = selection.map(item => item.id)
      this.single = selection.length != 1
      this.multiple = !selection.length
    },
    handleRowClick (row, rowIndex, event) {
      if (event.target.className !== 'el-input__inner') {
        this.$refs.multipleTable.toggleRowSelection(row)
      }
    },
    rowClass ({ row, rowIndex }) {
      for (let i = 0; i < this.ids.length; i++) {
        if (row.id === this.ids[i]) {
          return 'rowLight'
        }
      }
    },
  },
  mounted () {
    this.index()
  }
}
</script>
<style  lang="scss">
@import "@/assets/scss/common.scss";
</style>