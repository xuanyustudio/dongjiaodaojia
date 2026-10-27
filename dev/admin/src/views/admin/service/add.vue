<template>
  <div>
    <el-dialog title="添加"
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
        <el-row>
          <el-col :span="24">
            <el-form-item label="名称"
                          prop="name">
              <el-input v-model="form.name"
                        autoComplete="off"
                        clearable
                        placeholder="请输入名称"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="24">
            <el-form-item label="封面"
                          prop="cover">
              <ImagesUpload size="small"
                            fileType="image"
                            v-model:image="form.cover"></ImagesUpload>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="24">
            <el-form-item label="轮播"
                          prop="banner">
              <ImagesUpload size="small"
                            fileType="images"
                            v-model:images="form.banner"></ImagesUpload>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="24">
            <el-form-item label="销量"
                          prop="sales">
              <el-input-number controls-position="right"
                               style="width:200px;"
                               autoComplete="off"
                               v-model="form.sales"
                               clearable
                               :min="0"
                               placeholder="销量" />
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="24">
            <el-form-item label="项目介绍"
                          prop="introduce">
              <WangEditor v-if="show"
                          :isClear="isClear"
                          v-model:wangEditorContent="form.introduce"></WangEditor>
            </el-form-item>
          </el-col>
        </el-row>
        <!--下单说明s-->
        <el-row v-for="(domain, index) in dynamicValidateForm.explainKey">
          <el-col :span="24">
            <el-form-item label="下单说明">
              <el-input v-model="domain.name"></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="说明内容">
              <el-input v-model="domain.content"
                        type="textarea"
                        :rows="2"></el-input>

            </el-form-item>
          </el-col>
        </el-row>
        <el-button type="primary"
                   style="margin-top: 10px;margin-left: 90px;margin-bottom: 10px;"
                   size="small"
                   @click="addexplain">新增下单说明</el-button>
        <!--下单说明-->
        <el-row>
          <el-col :span="24">

          </el-col>
        </el-row>
        <el-row>
          <el-col :span="24">
            <el-form-item label="排序"
                          prop="sort">
              <el-input-number controls-position="right"
                               style="width:200px;"
                               autoComplete="off"
                               v-model="form.sort"
                               clearable
                               :min="0"
                               placeholder="排序" />
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="24">
            <el-form-item label="评分"
                          prop="praise">
              <el-input-number controls-position="right"
                               style="width:200px;"
                               autoComplete="off"
                               v-model="form.praise"
                               clearable
                               :min="0"
                               placeholder="评分" />
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="24">
            <el-form-item label="是否显示"
                          prop="show">
              <el-switch :active-value="1"
                         :inactive-value="0"
                         v-model="form.show"></el-switch>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="24">
            <el-form-item label="原价"
                          prop="original_price">
              <el-input-number controls-position="right"
                               style="width:200px;"
                               autoComplete="off"
                               v-model="form.original_price"
                               clearable
                               :min="0"
                               placeholder="原价" />
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="24">
            <el-form-item label="现价"
                          prop="price">
              <el-input-number controls-position="right"
                               style="width:200px;"
                               autoComplete="off"
                               v-model="form.price"
                               clearable
                               :min="0"
                               placeholder="现价" />
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="24">
            <el-form-item label="折扣"
                          prop="discount">
              <el-input-number controls-position="right"
                               style="width:200px;"
                               autoComplete="off"
                               v-model="form.discount"
                               clearable
                               :min="0"
                               placeholder="折扣" />
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="24">
            <el-form-item label="时长（分）"
                          prop="duration">
              <el-input prefix-icon="el-icon-video-play"
                        v-model="form.duration"
                        autoComplete="off"
                        clearable
                        placeholder="请输入时长（分）"><template #append>分钟</template></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="24">
            <el-form-item label="标签"
                          prop="label">
              <Tag v-model:tagList="form.label"></Tag>
            </el-form-item>
          </el-col>
        </el-row>
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
import { add } from '@/api/admin/service'
import ImagesUpload from '@/components/common/ImagesUpload.vue'
import WangEditor from '@/components/editor/WangEditor.vue'
import Tag from '@/components/common/Tag.vue'
export default {
  name: 'serviceadd',
  components: {
    ImagesUpload,
    WangEditor,
    Tag,
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
        name: '',
        cover: '',
        banner: [],
        introduce: '',
        update_time: '',
        show: 1,
        duration: '',
        label: [],
      },
      explain: [1],
      dynamicValidateForm: {
        explainKey: [],
        explainVaule: ''
      },

      isClear: false,
      loading: false,
      rules: {
        sales: [
          { pattern: /^[0-9]*$/, message: '销量格式错误' }
        ],
        original_price: [
          { pattern: /^(\d+(\.\d+)?)?$/, message: '原价格式错误' }
        ],
        price: [
          { pattern: /^(\d+(\.\d+)?)?$/, message: '现价格式错误' }
        ],
        discount: [
          { pattern: /^[0-9]*$/, message: '折扣格式错误' }
        ],
        duration: [
          { pattern: /^[0-9]*$/, message: '时长（分）格式错误' }
        ],
      }
    }
  },
  methods: {
    addexplain () {
      this.dynamicValidateForm.explainKey.push({
        name: '',
        content: '',
      });
      // this.explain.push(this.explain.length + 1)
      // var key = this.explain.length + 1
      // this.explainKey[this.explain.length] = '下单说明'
      // console.log(this.explainKey)
    },
    open () {
    },
    submit () {
      console.log(this.dynamicValidateForm.explainKey)
      this.form.explain = this.dynamicValidateForm.explainKey
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
      this.isClear = true
    },
  }
}
</script>
<style scoped lang="scss">
@import "@/assets/scss/common.scss";
</style>
