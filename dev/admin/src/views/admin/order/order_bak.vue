<template>
  <div style="">
    <div style="margin-bottom:11px;">
      <el-row :gutter="12">
        <el-col :span="6">
          <el-card shadow="hover">
            <div class="card-wrapper">
              <div class="card-box">
                <div class="card-left">
                  <div style="text-align: center;line-height: 70px;">
                    <i class="el-icon-s-goods"
                       style="color:#FFFFFF;font-size:28px;"></i>
                  </div>
                </div>
              </div>
              <div class="card-right">
                <div class="num"> 198</div>
                <div class="text">总订单数</div>
              </div>
            </div>
          </el-card>
        </el-col>
        <el-col :span="6">
          <el-card shadow="hover">
            <div class="card-wrapper">
              <div class="card-box"
                   style="background: #fff3e0;">
                <div class="card-left"
                     style="background: #ffab2b;">
                  <div style="text-align: center;line-height: 70px;">
                    <i class="el-icon-s-promotion"
                       style="color:#FFFFFF;font-size:28px;"></i>
                  </div>
                </div>
              </div>
              <div class="card-right">
                <div class="num"> 198</div>
                <div class="text">今日订单数</div>
              </div>
            </div>
          </el-card>
        </el-col>
        <el-col :span="6">
          <el-card shadow="hover">
            <div class="card-wrapper">
              <div class="card-box"
                   style=" background: #eaf9e1;">
                <div class="card-left"
                     style="background: #6dd230;">
                  <div style="text-align: center;line-height: 70px;">
                    <i class="el-icon-s-marketing"
                       style="color:#FFFFFF;font-size:28px;"></i>
                  </div>
                </div>
              </div>
              <div class="card-right">
                <div class="num"> 198</div>
                <div class="text">总订单金额</div>
              </div>
            </div>
          </el-card>
        </el-col>
        <el-col :span="6">
          <el-card shadow="hover">
            <div class="card-wrapper">
              <div class="card-box"
                   style="background: #ffeaf4;">
                <div class="card-left"
                     style="background: #ff85c0;">
                  <div style="text-align: center;line-height: 70px;">
                    <i class="el-icon-s-claim"
                       style="color:#FFFFFF;font-size:28px;"></i>
                  </div>
                </div>
              </div>
              <div class="card-right">
                <div class="num"> 198</div>
                <div class="text">今日付款</div>
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
          <!--头部-->
          <div id="charts_one"
               style="width:100%;min-height:80vh">
            <div style="margin-bottom:11px;">
              <label class="el-form-item__label">订单状态：</label>
              <el-radio-group v-model="radio1"
                              size="small"
                              @change="orderType">
                <el-radio-button label="8">全部</el-radio-button>
                <el-radio-button label="0">待付款</el-radio-button>
                <el-radio-button label="1">待接单</el-radio-button>
                <el-radio-button label="2">服务中</el-radio-button>
                <el-radio-button label="3">订单完成</el-radio-button>
                <el-radio-button label="4">已评价</el-radio-button>
                <el-radio-button label="5">退款</el-radio-button>
                <el-radio-button label="6">取消</el-radio-button>
              </el-radio-group>
            </div>
            <div style="margin-bottom:11px;">
              <label class="el-form-item__label">支付方式：</label>
              <el-radio-group v-model="radio2"
                              size="small"
                              @change="payType">
                <el-radio-button label="8">全部</el-radio-button>
                <el-radio-button label="1">微信</el-radio-button>
                <el-radio-button label="2">支付宝</el-radio-button>
                <el-radio-button label="3">余额</el-radio-button>
              </el-radio-group>
            </div>
            <Search size='small'
                    :searchVisible="searchVisible"
                    v-model:searchData="searchData"
                    :searchForm="searchForm"
                    @refesh_list="index"></Search>
            <div class="btn-group"
                 style="margin-bottom:11px;">
              <div>
                <el-button type="info"
                           :disabled="single"
                           size="small"
                          
                           @click="detail(ids)"
                           v-if="checkPermission('/admin/Order/detail.html')"><i class="el-icon-view"></i>查看详情</el-button>
                <el-button type="warning"
                           size="small"
                          
                           @click="dumpdata(ids)"
                           v-if="checkPermission('/admin/Order/dumpdata.html')"><i class="el-icon-download"></i>导出</el-button>
              </div>
              <table-tool v-model:searchVisible="searchVisible"
                          :fieldList="fieldList"
                          @refesh_list="index"
                          :searchTool="true"></table-tool>
            </div>
            <!--end-->
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
                               width="42">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[0].checked"
                               property="order_sn"
                               label="订单号"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[1].checked"
                               property="pay_money"
                               label="支付金额"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[2].checked"
                               property="total_money"
                               label="总金额"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[3].checked"
                               property="discount"
                               label="优惠金额"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[4].checked"
                               property="technician_money"
                               label="技师收入"
                               show-overflow-tooltip
                               width="">
              </el-table-column>

              <el-table-column align="center"
                               v-if="fieldList[5].checked"
                               property="order_status"
                               label="订单状态"
                               show-overflow-tooltip
                               width="">
                <template #default="scope">
                  <span v-if="scope.row.order_status == '0'">待付款</span>
                  <span v-if="scope.row.order_status == '1'">待接单</span>
                  <span v-if="scope.row.order_status == '2'">服务中</span>
                  <span v-if="scope.row.order_status == '3'">订单完成</span>
                  <span v-if="scope.row.order_status == '4'">已评价</span>
                  <span v-if="scope.row.order_status == '5'">退款</span>
                  <span v-if="scope.row.order_status == '6'">取消</span>
                </template>
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[6].checked"
                               property="user_id"
                               label="用户id"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[7].checked"
                               property="technician_id"
                               label="技师id"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[8].checked"
                               property="service_id"
                               label="服务id"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[9].checked"
                               property="spec_id"
                               label="规格id"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[10].checked"
                               property="coupon_id"
                               label="优惠券id"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[11].checked"
                               property="realname"
                               label="姓名"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[12].checked"
                               property="mobile"
                               label="电话"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[13].checked"
                               property="province"
                               label="省"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[14].checked"
                               property="city"
                               label="市"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[15].checked"
                               property="district"
                               label="区"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[16].checked"
                               property="address"
                               label="详细地址"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[17].checked"
                               property="create_time"
                               label="创建时间"
                               show-overflow-tooltip
                               width="">
                <template #default="scope">
                  {{parseTime(scope.row.create_time)}}
                </template>
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[18].checked"
                               property="appointment_time"
                               label="预约时间"
                               show-overflow-tooltip
                               width="">
                <template #default="scope">
                  {{parseTime(scope.row.appointment_time)}}
                </template>
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[19].checked"
                               property="duration"
                               label="时长"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[20].checked"
                               property="fare"
                               label="车费"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[21].checked"
                               property="refund"
                               label="退款金额"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[22].checked"
                               property="payment"
                               label="支付方式"
                               show-overflow-tooltip
                               width="">
                <template #default="scope">
                  <el-tag type="warning"
                          v-if="scope.row.payment == '1'"
                          size="small"
                          effect="dark">余额</el-tag>
                  <el-tag type="success"
                          v-if="scope.row.payment == '2'"
                          size="small"
                          effect="dark">微信</el-tag>
                  <el-tag type="primary"
                          v-if="scope.row.payment == '3'"
                          size="small"
                          effect="dark">支付宝</el-tag>
                </template>
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[23].checked"
                               property="brokerage"
                               label="佣金"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[24].checked"
                               property="complete_time"
                               label="服务完成时间"
                               show-overflow-tooltip
                               width="">
                <template #default="scope">
                  {{parseTime(scope.row.complete_time)}}
                </template>
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[25].checked"
                               property="cancel_type"
                               label="取消类型"
                               show-overflow-tooltip
                               width="">
                <template #default="scope">
                  <span v-if="scope.row.cancel_type == '0'">用户取消</span>
                  <span v-if="scope.row.cancel_type == '1'">技师取消</span>
                </template>
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[26].checked"
                               property="lat"
                               label="纬度"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[27].checked"
                               property="lng"
                               label="经度"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[28].checked"
                               property="cancel_reason"
                               label="取消原因"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[29].checked"
                               property="receiving_time"
                               label="接单时间"
                               show-overflow-tooltip
                               width="">
                <template #default="scope">
                  {{parseTime(scope.row.receiving_time)}}
                </template>
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[30].checked"
                               property="service_time"
                               label="开始服务时间"
                               show-overflow-tooltip
                               width="">
                <template #default="scope">
                  {{parseTime(scope.row.service_time)}}
                </template>
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[31].checked"
                               property="user.nickname"
                               label="下单用户"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[32].checked"
                               property="technician.realname"
                               label="服务技师"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[33].checked"
                               property="service.name"
                               label="服务类型"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[34].checked"
                               property="servicespec.name"
                               label="服务项目"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[35].checked"
                               property="coupon.name"
                               label="优惠券"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column :fixed="$store.getters.device !== 'mobile'?'right':false"
                               label="操作"
                               align="center"
                               width="80">
                <template #default="scope">
                  <div v-if="scope.row.id">
                    <el-button size="small"
                               type="info"
                               @click="detail(scope.row)"
                               v-if="checkPermission('/admin/Order/detail.html')"><i class="el-icon-view" />查看详情</el-button>
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
    <el-dialog title="导出进度条"
               :visible="dumpshow"
               :before-close="closedialog"
               width="500px">
      <el-progress :percentage="percentage"></el-progress>
    </el-dialog>
  </div>
</template>
<script>
import {
  index,
  updateExt,
  getUpdateInfo,
  del,
  dumpdata,
} from '@/api/admin/order'
import Search from '@/components/common/Search.vue'
import Page from '@/components/common/Page.vue'
import TableTool from '@/components/common/TableTool.vue'
import Add from '@/views/admin/order/add.vue'
import Update from '@/views/admin/order/update.vue'
import Detail from '@/views/admin/order/detail.vue'
import { confirm, param2Obj } from '@/utils/common'
import * as XLSX from 'xlsx'
export default {
  name: 'order',
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
      radio1: '8',
      radio2: '8',
      // radio3: '上海',
      // radio4: '上海',
      dialog: {
        addDialogStatus: false,
        updateDialogStatus: false,
        detailDialogStatus: false,
      },
      fieldList: [
        { lable: '订单号', checked: true },
        { lable: '支付金额', checked: true },
        { lable: '总金额', checked: true },
        { lable: '优惠金额', checked: true },
        { lable: '技师收入', checked: true },
        { lable: '订单状态', checked: true },
        { lable: '用户id', checked: true },
        { lable: '技师id', checked: true },
        { lable: '服务id', checked: true },
        { lable: '规格id', checked: true },
        { lable: '优惠券id', checked: true },
        { lable: '姓名', checked: true },
        { lable: '电话', checked: true },
        { lable: '省', checked: true },
        { lable: '市', checked: true },
        { lable: '区', checked: true },
        { lable: '详细地址', checked: true },
        { lable: '创建时间', checked: true },
        { lable: '预约时间', checked: true },
        { lable: '时长', checked: true },
        { lable: '车费', checked: true },
        { lable: '退款金额', checked: true },
        { lable: '支付方式', checked: true },
        { lable: '佣金', checked: true },
        { lable: '服务完成时间', checked: true },
        { lable: '取消类型', checked: true },
        { lable: '纬度', checked: true },
        { lable: '经度', checked: true },
        { lable: '取消原因', checked: true },
        { lable: '接单时间', checked: true },
        { lable: '开始服务时间', checked: true },
        { lable: '下单用户', checked: true },
        { lable: '服务技师', checked: true },
        { lable: '服务类型', checked: true },
        { lable: '服务项目', checked: true },
        { lable: '优惠券', checked: true },
      ],
      ids: [],
      single: true,
      multiple: true,
      list: [],
      updateInfo: {},
      detailInfo: {},
      exceldata: [],
      dumppage: 1,
      ws: {},
      dumpshow: false,
      percentage: '',
      filename: '',
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
    orderType (e) {
      console.log(e)
      if (e != 8) {
        this.searchData.order_status = e
        console.log(this.searchData)

      } else {
        delete this.searchData.order_status
      }
      this.index()
    },
    payType (e) {
      if (e != 8) {
        this.searchData.payment = e
        console.log(this.searchData)

      } else {
        delete this.searchData.payment
      }
      this.index()
      console.log(e)
    },
    index () {
      let param = { limit: this.page_data.limit, page: this.page_data.page }
      Object.assign(param, this.searchData, param2Obj(this.$route.fullPath))
      console.log(this.searchData)
      this.loading = true
      index(param).then(res => {
        this.list = res.data.data
        this.page_data.total = res.data.total
        this.loading = false
        if (this.page_data.page == 1) {
          this.searchForm = [
            { type: 'Input', label: '订单号：', prop: 'order_sn', width: '150px' },
            // { type: 'Select', label: '订单状态', prop: 'order_status', data: [{ "val": "0", "key": "待付款" }, { "val": "1", "key": "待接单" }, { "val": "2", "key": "服务中" }, { "val": "3", "key": "订单完成" }, { "val": "4", "key": "已评价" }, { "val": "5", "key": "退款" }, { "val": "6", "key": "取消" }], width: '150px' },
            { type: 'datetime', label: '创建时间：', prop: 'create_time', width: '150px' },
            // { type: 'Select', label: '支付方式', prop: 'payment', data: [{ "val": "1", "key": "余额" }, { "val": "2", "key": "微信" }, { "val": "3", "key": "支付宝" }], width: '150px' },
            { type: 'datetime', label: '服务完成时间：', prop: 'complete_time', width: '150px' },
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
    dumpdata () {
      confirm({ content: '确定要操作吗' }).then(() => {
        this.dumpshow = true
        this.confirmdumpdata()
      }).catch(() => { })
    },
    confirmdumpdata () {
      dumpdata({ page: this.dumppage }).then(res => {
        if (res.data.length > 0) {
          if (this.dumppage == 1) {
            this.exceldata.push(res.header)
          }
          res.data.forEach((item) => {
            this.exceldata.push(Object.values(item))
          })
          this.percentage = res.percentage
          this.filename = res.filename
          this.ws = XLSX.utils.aoa_to_sheet(this.exceldata)
          this.dumppage = this.dumppage + 1
          this.confirmdumpdata()
        } else {
          let wb = XLSX.utils.book_new()
          XLSX.utils.book_append_sheet(wb, this.ws, '订单列表')
          XLSX.writeFile(wb, this.filename)
          this.dumpshow = false
        }
      })
    },
    closedialog () {
      this.dumpshow = false
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
.card-wrapper {
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
      font-size: 30px;
      font-weight: 400;
    }
    .text {
      font-size: 14px;
      color: #98a9bc;
      padding-top: 10px;
    }
  }
}
</style>