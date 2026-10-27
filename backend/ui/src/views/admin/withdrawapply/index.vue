<template>
  <div>
    <div style="margin-bottom:11px;">
      <el-row :gutter="12">
        <el-col :span="6">
          <el-card shadow="hover">
            <div class="card-wrpper">
              <div class="card-box">
                <div class="card-left">
                  <div style="text-align: center;line-height: 70px;">
                    <i class="el-icon-s-goods"
                       style="color:#FFFFFF;font-size:28px;"></i>
                  </div>
                </div>
              </div>
              <div class="card-right">
                <div class="num"> {{count.num1}}</div>
                <div class="text">待提现金额</div>
              </div>
            </div>
          </el-card>
        </el-col>
        <el-col :span="6">
          <el-card shadow="hover">
            <div class="card-wrpper">
              <div class="card-box"
                   style="background: #fff3e0;">
                <div class="card-left"
                     style="background: #ffab2b;">
                  <div style="text-align: center;line-height: 70px;">
                    <i class="el-icon-s-finance"
                       style="color:#FFFFFF;font-size:28px;"></i>
                  </div>
                </div>
              </div>
              <div class="card-right">
                <div class="num"> {{count.num2}}</div>
                <div class="text">佣金总金额</div>
              </div>
            </div>
          </el-card>
        </el-col>
        <el-col :span="6">
          <el-card shadow="hover">
            <div class="card-wrpper">
              <div class="card-box"
                   style=" background: #eaf9e1;">
                <div class="card-left"
                     style="background: #6dd230;">
                  <div style="text-align: center;line-height: 70px;">
                    <i class="el-icon-document-checked"
                       style="color:#FFFFFF;font-size:28px;"></i>
                  </div>
                </div>
              </div>
              <div class="card-right">
                <div class="num"> {{count.num3}}</div>
                <div class="text">已提现金额</div>
              </div>
            </div>
          </el-card>
        </el-col>
        <el-col :span="6">
          <el-card shadow="hover">
            <div class="card-wrpper">
              <div class="card-box"
                   style="background: #ffeaf4;">
                <div class="card-left"
                     style="background: #ff85c0;">
                  <div style="text-align: center;line-height: 70px;">
                    <i class="el-icon-document-delete"
                       style="color:#FFFFFF;font-size:28px;"></i>
                  </div>
                </div>
              </div>
              <div class="card-right">
                <div class="num"> {{count.num4}}</div>
                <div class="text">未提现金额</div>
              </div>
            </div>
          </el-card>
        </el-col>
      </el-row>
    </div>
    <el-row :gutter="10">
      <el-col :span="24">
        <el-card shadow="never"
                 class="card">
          <div id="charts_one"
               style="width:100%;min-height:80vh">
            <!-- <Search size='small'
                    :searchVisible="searchVisible"
                    :searchData.sync="searchData"
                    :searchForm="searchForm"
                    @refesh_list="index"></Search> -->
            <div style="margin-bottom:11px;">
              <label class="el-form-item__label">账户类型：</label>
              <el-radio-group v-model="radio1"
                              size="small"
                              @change="orderType">
                <el-radio-button label="8">全部</el-radio-button>
                <el-radio-button label="4">支付宝</el-radio-button>
                <el-radio-button label="2">微信</el-radio-button>
                <el-radio-button label="1">余额</el-radio-button>
              </el-radio-group>
            </div>

            <div style="margin-bottom:11px;">
              <label class="el-form-item__label">提现状态：</label>
              <el-radio-group v-model="radio2"
                              size="small"
                              @change="payType">
                <el-radio-button label="8">全部</el-radio-button>
                <el-radio-button label="1">待提现</el-radio-button>
                <el-radio-button label="2">提现成功</el-radio-button>
                <el-radio-button label="3">提现失败</el-radio-button>
              </el-radio-group>
            </div>
            <div class="btn-group"
                 style="margin-bottom:11px;">
              <div>
                <el-button type="danger"
                           :disabled="multiple"
                           size="mini"
                           icon="el-icon-delete"
                           @click="del(ids)"
                           v-if="checkPermission('/admin/Withdrawapply/delete.html')">删除</el-button>
              </div>
              <table-tool :searchVisible.sync="searchVisible"
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
                               v-if="fieldList[0].checked"
                               property="sn"
                               label="提现单号"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <!-- <el-table-column align="center"
                               v-if="fieldList[1].checked"
                               property="user_id"
                               label="用户信息"
                               show-overflow-tooltip
                               width="">
                <template slot-scope="scope">
                  <el-tag size="small"
                          type="warning"
                          v-if="scope.row.user_type == '1'">用户</el-tag>
                  <el-tag size="small"
                          v-if="scope.row.user_type == '2'">技师</el-tag>

                </template>
              </el-table-column> -->
              <el-table-column align="center"
                               v-if="fieldList[2].checked"
                               property="real_name"
                               label="真实姓名"
                               show-overflow-tooltip
                               width="">

              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[3].checked"
                               property="account"
                               label="账号"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[4].checked"
                               property="type"
                               label="类型"
                               show-overflow-tooltip
                               width="">
                <template slot-scope="scope">
                  <span v-if="scope.row.type == '4'">支付宝</span>
                  <span v-if="scope.row.type == '2'">微信</span>
                  <span v-if="scope.row.type == '1'">钱包余额</span>
                </template>
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[5].checked"
                               property="user_type"
                               label="用户类型"
                               show-overflow-tooltip
                               width="">
                <template slot-scope="scope">
                  <span v-if="scope.row.user_type == '1'">用户</span>
                  <span v-if="scope.row.user_type == '2'">技师</span>
                </template>
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[6].checked"
                               property="money"
                               label="提现金额"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[7].checked"
                               property="left_money"
                               label="实际获得金额"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[8].checked"
                               property="poundage"
                               label="手续费"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[9].checked"
                               property="status"
                               label="提现状态"
                               show-overflow-tooltip
                               width="">
                <template slot-scope="scope">
                  <el-tag type="info"
                          v-if="scope.row.status == '1'"
                          size="mini"
                          effect="dark">待提现</el-tag>
                  <el-tag type="success"
                          v-if="scope.row.status == '2'"
                          size="mini"
                          effect="dark">提现成功</el-tag>
                  <el-tag type="danger"
                          v-if="scope.row.status == '3'"
                          size="mini"
                          effect="dark">提现失败</el-tag>
                </template>
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[10].checked"
                               property="create_time"
                               label="申请时间"
                               show-overflow-tooltip
                               width="">
                <template slot-scope="scope">
                  {{parseTime(scope.row.create_time)}}
                </template>
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[11].checked"
                               property="technician.realname"
                               label="技师姓名"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <!-- <el-table-column align="center"
                               v-if="fieldList[12].checked"
                               property="user.nickname"
                               label="用户昵称"
                               show-overflow-tooltip
                               width="">
              </el-table-column> -->
              <el-table-column :fixed="$store.getters.device !== 'mobile'?'right':false"
                               label="操作"
                               align="center"
                               width="160">
                <template slot-scope="scope">
                  <div v-if="scope.row.id">
                    <el-button size="mini"
                               type="primary"
                               @click="update(scope.row)"
                               v-if="checkPermission('/admin/Withdrawapply/update.html')"><i class="el-icon-edit" />编辑</el-button>
                    <el-button size="mini"
                               type="danger"
                               @click="del(scope.row)"
                               v-if="checkPermission('/admin/Withdrawapply/delete.html')"><i class="el-icon-delete" />删除</el-button>
                  </div>
                </template>
              </el-table-column>
            </el-table>
            <Page :total="page_data.total"
                  :page.sync="page_data.page"
                  :limit.sync="page_data.limit"
                  @pagination="index" />
          </div>
        </el-card>
      </el-col>
    </el-row>
    <!--添加-->
    <Add :show.sync="dialog.addDialogStatus"
         size="small"
         @refesh_list="index"></Add>
    <!--修改-->
    <Update :info="updateInfo"
            :show.sync="dialog.updateDialogStatus"
            size="small"
            @refesh_list="index"></Update>
    <!--查看详情-->
    <Detail :info="detailInfo"
            :show.sync="dialog.detailDialogStatus"
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
  count
} from '@/api/admin/withdrawapply'
import Search from '@/components/common/Search.vue'
import Page from '@/components/common/Page.vue'
import TableTool from '@/components/common/TableTool.vue'
import Add from '@/views/admin/withdrawapply/add.vue'
import Update from '@/views/admin/withdrawapply/update.vue'
import Detail from '@/views/admin/withdrawapply/detail.vue'
import { confirm, param2Obj } from '@/utils/common'
export default {
  name: 'withdrawapply',
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
        { lable: '提现单号', checked: true },
        { lable: '用户id；技师id', checked: true },
        { lable: '真实姓名', checked: true },
        { lable: '账号', checked: true },
        { lable: '类型', checked: true },
        { lable: '用户类型', checked: true },
        { lable: '提现金额', checked: true },
        { lable: '实际获得金额', checked: true },
        { lable: '手续费', checked: true },
        { lable: '提现状态', checked: true },
        { lable: '申请时间', checked: true },
        { lable: '技师姓名', checked: true },
        { lable: '用户昵称', checked: true },
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
      radio1: 8,
      radio2: 8,
      radio3: 8,
      count: {
        num1: 0,
        num2: 0,
        num3: 0,
        num4: 0,
      }
    }
  },
  methods: {
    orderType (e) {
      console.log(e)
      if (e != 8) {
        this.searchData.type = e

      } else {
        delete this.searchData.type
      }
      this.index()
    },
    payType (e) {
      console.log(e)
      if (e != 8) {
        this.searchData.status = e

      } else {
        delete this.searchData.status
      }
      this.index()
    },
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
            { type: 'Select', label: '类型', prop: 'type', data: [{ "key": "支付宝", "val": "4" }, { "key": "微信", "val": "2" }, { "key": "钱包余额", "val": "1" }], width: '150px' },
            { type: 'Select', label: '用户类型', prop: 'user_type', data: [{ "key": "用户", "val": "1" }, { "key": "技师", "val": "2" }], width: '150px' },
            { type: 'Input', label: '实际获得金额', prop: 'left_money', width: '150px' },
            { type: 'Select', label: '提现状态', prop: 'status', data: [{ "key": "待提现", "val": "1" }, { "val": "2", "key": "提现成功" }, { "val": "3", "key": "提现失败" }], width: '150px' },
          ]
        }
      })

      count().then(res => {
        this.count = res.data
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
.card-wrpper {
  display: flex;
  .card-box {
    width: 80px;
    height: 80px;
    background-color: #e4ecff;
    border-radius: 50%;
    margin-right: 20px;
    .card-left {
      margin: auto;
      margin-top: 10px;
      width: 60px;
      height: 60px;
      background-color: #4d7cfe;
      border-radius: 50%;
    }
  }
  .card-right {
    margin: auto 0;
    .num {
      font-size: 20px;
      font-weight: 400;
    }
    .text {
      font-size: 14px;
      color: #98a9bc;
      padding-top: 10px;
    }
  }
}
.table-head {
  background: #f0faff;
  padding: 10px 10px;
  text-align: center;
  color: #797979;
}
.table-list {
  .item-head {
    margin-top: 12px;
    background: #f5f5f5;
    padding: 10px 10px;
    display: flex;
    color: #797979;
  }
}
.flex {
  display: flex;
  justify-content: center;
  align-items: center;
}
.item-center {
  height: 100px;
  color: #797979;
  border: #f5f5f5 1px solid;
}
.grid-content {
  border: #f5f5f5 1px solid;
  // line-height: 100px;
  align-items: center;
}
.center {
  display: flex;
  align-items: center;
  height: 100px;
  justify-content: center;
}
</style>