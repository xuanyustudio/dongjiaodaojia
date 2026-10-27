<template>
  <div>
    <el-dialog title="回复"
               width="600px"
               class="icon-dialog"
               :model-value="show"
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
						<el-form-item label="内容" prop="content">
							<el-input  v-model="form.content" autoComplete="off" clearable  placeholder="请输入内容"></el-input>
						</el-form-item>
					</el-col>
				</el-row> -->
        <el-row>
          <el-col :span="24">
            <el-form-item label="回复"
                          prop="reply">
              <el-input v-model="form.reply"
                        autoComplete="off"
                        clearable
                        type="textarea"
                        placeholder="请输入回复"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <!-- <el-row >
					<el-col :span="24">
						<el-form-item label="手机" prop="mobile">
							<el-input  v-model="form.mobile" autoComplete="off" clearable  placeholder="请输入手机"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="图片" prop="images">
							<ImagesUpload size="small"  fileType="images" v-model:images="form.images"></ImagesUpload>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="技师id" prop="technician_id">
							<el-input  v-model="form.technician_id" autoComplete="off" clearable  placeholder="请输入技师id"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="用户id" prop="user_id">
							<el-input  v-model="form.user_id" autoComplete="off" clearable  placeholder="请输入用户id"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="用户类型" prop="type">
							<el-select style="width:100%" v-model="form.user_type" filterable clearable placeholder="请选择">
								<el-option key="0" label="用户" :value="1"></el-option>
								<el-option key="1" label="技师" :value="2"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="类型" prop="type">
							<el-select style="width:100%" v-model="form.type" filterable clearable placeholder="请选择">
								<el-option key="0" label="bug" :value="1"></el-option>
								<el-option key="1" label="意见" :value="2"></el-option>
								<el-option key="2" label="吐槽" :value="3"></el-option>
								<el-option key="3" label="其他" :value="4"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="时间" prop="create_time">
							<el-date-picker value-format="yyyy-MM-dd HH:mm:ss" type="datetime" v-model="form.create_time" clearable placeholder="请输入时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row> -->
      </el-form>
      <template #footer><div
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
      </div></template>
    </el-dialog>
  </div>
</template>
<script>
import { update } from '@/api/admin/opinion'
import { parseTime } from '@/utils/common'
import ImagesUpload from '@/components/common/ImagesUpload.vue'
export default {
  name: 'opinionupdate',
  components: {
    ImagesUpload,
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
    info: {
      type: Object,
    },
  },
  data () {
    return {
      form: {
        content: '',
        reply: '',
        mobile: '',
        images: [],
        technician_id: '',
        user_id: '',
        create_time: '',
      },
      loading: false,
      rules: {
      }
    }
  },
  methods: {
    open () {
      this.form = this.info
      this.setDefaultVal('images')
      this.form.create_time = parseTime(this.form.create_time)
    },
    submit () {
      this.$refs['form'].validate(valid => {
        if (valid) {
          this.loading = true
          update(this.form).then(res => {
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
    setDefaultVal (key) {
      if (this.form[key] == null || this.form[key] == '') {
        this.form[key] = []
      }
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
