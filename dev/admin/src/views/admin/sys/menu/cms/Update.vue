<template>
    <el-dialog title="修改内容模型" width="580px" class="icon-dialog" :model-value="show" @open="open" :before-close="closeForm" append-to-body>
        <el-form  :size="size" ref="form" :model="form" :rules="rules" label-width="90px">
            <el-row>
                <el-col :span="24">
                    <el-form-item label="模型名称" prop="title">
                        <el-input v-model="form.title" clearable placeholder="菜单名称"/>
                    </el-form-item>
                </el-col>
            </el-row>
            <el-row>
                <el-col :span="24">
                    <el-form-item label="数据表名" prop="table_name">
                        <el-input v-model="form.table_name" clearable placeholder="数据表名称"  />
                    </el-form-item>
                </el-col>
            </el-row>
            <el-row>
                <el-col :span="24">
                    <el-form-item label="主键ID" prop="pk">
                        <el-input v-model="form.pk" clearable placeholder="数据表主键"/>
                    </el-form-item>
                </el-col>
            </el-row>
        </el-form>
        <template #footer><div class="dialog-footer">
            <el-button :size="size" :loading="loading" type="primary" @click="submit" >
                <span v-if="!loading">确 定</span>
                <span v-else>提 交 中...</span>
            </el-button>
            <el-button :size="size" @click="closeForm">取 消</el-button>
        </div></template>
    </el-dialog>
</template>

<script>
import {updateMenu} from '../../sys'
export default {
    name:'updateCms',
    props: {
        show: {
            type: Boolean,
            default: false
        },
        size: {
            type: String,
            default: 'small'
        },
        info:{
            type:Object,
        },
    },
    data() {
        return {
            form: {
                page_type:1,
            },
            loading:false,
            rules: {
                title: [{ required: true, message: '模型名称不能为空', trigger: 'blur' }],
            },
        }
    },
    methods: {
        submit(){
            this.$refs['form'].validate(valid => {
                if (valid) {
                    this.loading = true
                    updateMenu(this.form).then(res => {
                        if(res.status == '200'){
                            this.$message({message: '操作成功', type: 'success'})
                            this.$emit('refesh_list')
                            this.closeForm()
                        }else{
                            this.loading = false
                        }
                    }).catch(()=>{
                        this.loading = false
                    })
                }
            })
        },
        open(){
            this.form = this.info
        },
        closeForm(){
            this.$emit('update:show', false)
            this.loading = false
            if (this.$refs['form']!==undefined) {
                this.$refs['form'].resetFields()
            }
        }
    },
}
</script>