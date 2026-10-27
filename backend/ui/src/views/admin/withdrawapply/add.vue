<template>
  <div>
    <el-dialog title="添加"
               width="600px"
               class="icon-dialog"
               :visible.sync="show"
               @open="open"
               :before-close="closeForm"
               append-to-body>
      <el-form :size="size"
               ref="form"
               :model="form"
               :rules="rules"
               :label-width="$store.getters.device !== 'mobile'?'16%':'90px'">
        <!-- <el-row >
					<el-col :span="24">
						<el-form-item label="提现单号" prop="sn">
							<el-input  v-model="form.sn" autoComplete="off" clearable  placeholder="请输入提现单号"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="用户id；技师id" prop="user_id">
							<el-input  v-model="form.user_id" autoComplete="off" clearable  placeholder="请输入用户id；技师id"></el-input>
						</el-form-item>
					</el-col>
				</el-row> -->
        <el-row>
          <el-col :span="24">
            <el-form-item label="真实姓名"
                          prop="real_name">
              <el-input v-model="form.real_name"
                        autoComplete="off"
                        clearable
                        placeholder="请输入真实姓名"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="24">
            <el-form-item label="账号"
                          prop="account">
              <el-input v-model="form.account"
                        autoComplete="off"
                        clearable
                        placeholder="请输入账号"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="24">
            <el-form-item label="类型"
                          prop="type">
              <el-select style="width:100%"
                         v-model="form.type"
                         filterable
                         clearable
                         placeholder="请选择">
                <el-option key="0"
                           label="支付宝"
                           :value="4"></el-option>
                <el-option key="1"
                           label="微信"
                           :value="2"></el-option>
                <el-option key="2"
                           label="钱包余额"
                           :value="1"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>
        <!-- <el-row >
					<el-col :span="24">
						<el-form-item label="用户类型" prop="type">
							<el-select style="width:100%" v-model="form.user_type" filterable clearable placeholder="请选择">
								<el-option key="0" label="用户" :value="1"></el-option>
								<el-option key="1" label="技师" :value="2"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row> -->
        <!-- <el-row >
					<el-col :span="24">
						<el-form-item label="提现金额" prop="money">
							<el-input  v-model="form.money" autoComplete="off" clearable  placeholder="请输入提现金额"></el-input>
						</el-form-item>
					</el-col>
				</el-row> -->
        <el-row>
          <el-col :span="24">
            <el-form-item label="实际获得金额"
                          prop="left_money">
              <el-input v-model="form.left_money"
                        autoComplete="off"
                        clearable
                        placeholder="请输入实际获得金额"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <!-- <el-row >
					<el-col :span="24">
						<el-form-item label="收款二维码" prop="money_qr_code">
							<el-input  v-model="form.money_qr_code" autoComplete="off" clearable  placeholder="请输入收款二维码"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="手续费" prop="poundage">
							<el-input  v-model="form.poundage" autoComplete="off" clearable  placeholder="请输入手续费"></el-input>
						</el-form-item>
					</el-col>
				</el-row> -->
        <el-row>
          <el-col :span="24">
            <el-form-item label="提现状态"
                          prop="status">
              <el-radio-group v-model="form.status">
                <el-radio :label="1">待提现</el-radio>
                <el-radio :label="2">提现成功</el-radio>
                <el-radio :label="3">提现失败</el-radio>
              </el-radio-group>
            </el-form-item>
          </el-col>
        </el-row>
        <!-- <el-row >
					<el-col :span="24">
						<el-form-item label="申请时间" prop="create_time">
							<el-date-picker value-format="yyyy-MM-dd HH:mm:ss" type="datetime" v-model="form.create_time" clearable placeholder="请输入申请时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="更新时间" prop="update_time">
							<el-input  v-model="form.update_time" autoComplete="off" clearable  placeholder="请输入更新时间"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="支付单号" prop="payment_no">
							<el-input  v-model="form.payment_no" autoComplete="off" clearable  placeholder="请输入支付单号"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="支付时间" prop="payment_time">
							<el-input  v-model="form.payment_time" autoComplete="off" clearable  placeholder="请输入支付时间"></el-input>
						</el-form-item>
					</el-col>
				</el-row> -->
        <el-row>
          <el-col :span="24">
            <el-form-item label="审核备注"
                          prop="deion">
              <el-input type="textarea"
                        autoComplete="off"
                        v-model="form.deion"
                        :autosize="{ minRows: 2, maxRows: 4}"
                        clearable
                        placeholder="请输入审核备注" />
            </el-form-item>
          </el-col>
        </el-row>
        <!-- <el-row >
					<el-col :span="24">
						<el-form-item label="转账凭证" prop="transfer_voucher">
							<el-input  v-model="form.transfer_voucher" autoComplete="off" clearable  placeholder="请输入转账凭证"></el-input>
						</el-form-item>
					</el-col>
				</el-row> -->
        <el-row>
          <el-col :span="24">
            <el-form-item label="打款时间"
                          prop="transfer_time">
              <el-date-picker value-format="yyyy-MM-dd HH:mm:ss"
                              type="datetime"
                              v-model="form.transfer_time"
                              clearable
                              placeholder="请输入打款时间"></el-date-picker>
            </el-form-item>
          </el-col>
        </el-row>
        <!-- <el-row >
					<el-col :span="24">
						<el-form-item label="提现银行" prop="bank">
							<el-input  v-model="form.bank" autoComplete="off" clearable  placeholder="请输入提现银行"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="提现银行支行" prop="subbank">
							<el-input  v-model="form.subbank" autoComplete="off" clearable  placeholder="请输入提现银行支行"></el-input>
						</el-form-item>
					</el-col>
				</el-row> -->
      </el-form>
      <div slot="footer"
           class="dialog-footer">
        <el-button :size="size"
                   :loading="loading"
                   type="primary"
                   @click="submit">
          <span v-if="!loading">确 定</span>
          <span v-else>提 交 中...</span>
        </el-button>
        <el-button :size="size"
                   @click="closeForm">取 消</el-button>
      </div>
    </el-dialog>
  </div>
</template>
<script>
import { add } from '@/api/admin/withdrawapply'
export default {
  name: 'withdrawapplyadd',
  components: {
  },
  props: {
    show: {
      type: Boolean,
      default: false
    },
    size: {
      type: String,
      default: 'small'
    },
  },
  data () {
    return {
      form: {
        sn: '',
        user_id: '',
        real_name: '',
        account: '',
        money: '',
        left_money: '',
        money_qr_code: '',
        poundage: '',
        status: 1,
        create_time: '',
        update_time: '',
        payment_no: '',
        payment_time: '',
        deion: '',
        transfer_voucher: '',
        transfer_time: '',
        bank: '',
        subbank: '',
      },
      loading: false,
      rules: {
      }
    }
  },
  methods: {
    open () {
    },
    submit () {
      this.$refs['form'].validate(valid => {
        if (valid) {
          this.loading = true
          add(this.form).then(res => {
            if (res.status == 200) {
              this.$message({ message: '操作成功', type: 'success' })
              this.$emit('refesh_list')
              this.closeForm()
            }
          }).catch(() => {
            this.loading = false
          })
        }
      })
    },
    closeForm () {
      this.$emit('update:show', false)
      this.loading = false
      if (this.$refs['form'] !== undefined) {
        this.$refs['form'].resetFields()
      }
    },
  }
}
</script>
<style scoped lang="scss">
@import "@/assets/scss/common.scss";
</style>
