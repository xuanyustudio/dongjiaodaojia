<template>
    <el-dialog title="创建字段" width="600px" class="icon-dialog" :visible.sync="show" :before-close="closeForm" @open="open"  append-to-body>
        <el-form :size="size" ref="form" :model="form" :rules="rules" label-width="90px">
            <el-row>
                <el-col :span="24">
                    <el-form-item label="字段标题" prop="title">
                        <el-input v-model="form.title" clearable placeholder="字段中文描述"  />
                    </el-form-item>
                </el-col>
            </el-row>
             <el-row>
                <el-col :span="24">
                    <el-form-item label="字段名称" prop="field">
                        <el-input @blur="checkCmsField" v-model="form.field" clearable placeholder="字段英文名称"/>
                    </el-form-item>
                </el-col>
            </el-row>
            <el-row>
                <el-col :span="12">
                    <el-form-item label="字段类型" prop="type">
                        <el-select style="width:100%" v-model="form.type" @change="selectType" filterable placeholder="请选择">
                            <el-option v-for="(item,i) in field" :key="i" :label="item.name" :value="item.type"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item v-if="form.type == 22" label="层级" prop="address_type">
                        <el-select style="width:100%" v-model="form.other_config.address_type"  placeholder="请选择级层">
                            <el-option  key="0" label="省市区" value="1"></el-option>
                            <el-option  key="1" label="省市" value="2"></el-option>
                            <el-option  key="2" label="省" value="3"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item v-else-if="form.type == 13" label="上传样式" prop="address_type">
                        <el-select style="width:100%" v-model="form.other_config.upload_type"  placeholder="上传样式">
                            <el-option key="0" label="样式1(带缩略图)" value="1"></el-option>
                            <el-option key="1" label="样式2(带输入框)" value="2"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item v-else-if="form.type == 31" label="长度" prop="rand_length">
                        <el-input v-model="form.other_config.rand_length" clearable placeholder="随机数长度"/>
                    </el-form-item>
                    <el-form-item v-else label="默认值" prop="default_value">
                        <el-input v-model="form.default_value" clearable placeholder="同步数据表默认值"/>
                    </el-form-item>
                </el-col>
            </el-row>
            <el-row v-if="form.type == 9 || form.type == 11 || form.type == 12">
                <el-form-item label="日期格式" prop="datetime_config">
                    <el-select style="width:100%" :size="size" v-model="form.datetime_config" clearable  filterable placeholder="请选择日期格式">
                            <el-option key="1" label="年月日时分秒" value="datetime"></el-option>
                            <el-option key="2" label="年月日" value="date"></el-option>
                            <el-option key="3" label="年" value="year"></el-option>
                            <el-option key="4" label="月" value="month"></el-option>
                            <el-option key="5" label="多个日期" value="dates"></el-option>
                    </el-select>
                </el-form-item>
            </el-row>
            <el-row v-if="list_item">
                <el-form-item label="选项配置" prop="item_config">
                    <el-row v-for="(item,i) in form.item_config" :key="i">
                        <el-col :span="8">
                            <el-form-item class="activeItem">
                                <el-input  v-model="item.key" placeholder="选项名称"/>
                            </el-form-item>
                        </el-col>
                        <el-col :span="6">
                            <el-form-item class="activeItem">
                                <el-input style="position:relative;left:5px;" v-model="item.val" placeholder="选项值"/>
                            </el-form-item>
                        </el-col>
                        <el-col :span="6">
                            <el-form-item class="activeItem">
                                <el-select style="position:relative;left:10px;" v-model="item.label_color" size="small" clearable placeholder="请选择背景色">
                                    <el-option key="1" style="background:#409eff" label="primary" value="primary"></el-option>
                                    <el-option key="2" style="background:#67c23a" label="success" value="success"></el-option>
                                    <el-option key="3" style="background:#909399" label="info" value="info"></el-option>
                                    <el-option key="4" style="background:#e6a23c" label="warning" value="warning"></el-option>
                                    <el-option key="5" style="background:#f56c6c" label="danger" value="danger"></el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :span="2">
                            <el-button type="danger" size="mini" style="position:relative;left:15px"  icon="el-icon-close" @click="deleteItem('item_config',i)"></el-button>
                        </el-col> 
                    </el-row>
                    <div>
                        <el-button type="success" icon="el-icon-plus" style="padding:5px 7px" :size="size" @click="addItem('item_config')">追加</el-button>
                        <el-button v-if="form.item_config.length > 0" type="warning" icon="el-icon-delete" style="padding:5px 7px" :size="size" @click="clearItem('item_config')">清空</el-button>
                        <el-select v-if="form.item_config.length === 0"  :size="size" style="height:25px; light:25px; margin-left:20px;" v-model="default_config" @change="setDefaultItem"  placeholder="请选择默认配置">
                            <el-option v-for="(item,i) in itemList" :key="i" :label="item.name" :value="item.item"></el-option>
                        </el-select>
                    </div>
                </el-form-item>
            </el-row>
            <el-row>
                <el-col :span="8">
                    <el-form-item label="数据结构" prop="datatype">
                        <el-select v-model="form.datatype" filterable @change="setFieldLength"  placeholder="请选择">
                            <el-option v-for="(item,i) in propertyField" :key="i" :label="item.name" :value="item.name"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="字段长度" prop="length">
                        <el-input v-model="form.length" placeholder="字段长度"/>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="字段索引" prop="indexdata">
                        <el-select v-model="form.indexdata" filterable placeholder="请选择">
                            <el-option key="1" label="普通索引" value="normal"></el-option>
                            <el-option key="2" label="唯一索引" value="unique"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
            </el-row>
        </el-form>
        <div slot="footer" class="dialog-footer">
            <el-button :size="size" :loading="loading" type="primary" @click="submit" >
                <span v-if="!loading">确 定</span>
                <span v-else>提 交 中...</span>
            </el-button>
            <el-button :size="size" @click="closeForm">取 消</el-button>
        </div>
    </el-dialog>
</template>


<script>
import {updateField,getConfigList,getTablesByMenuId,checkCmsField} from '../../sys'
export default {
    name:'updateCmsField',
    props: {
        show: {
            type: Boolean,
            default: false
        },
        size: {
            type: String,
            default: false
        },
        field: {
            type: Array,
        },
        info: {
            type: Object,
        },
        itemList: {
            type: Array,
        }
    },
    data() {
        return {
            form: {
                post_status:'1',
                create_table_field:'1',
                list_show:'5',
                menu_id:this.$route.query.menu_id,
                validate:[],
                item_config:[],
                other_config:{},
                datatype:'',
                length:'',
                belong_table:'',
                page_type:1,
                create_table_field:1
            },
            iconDialogStatus:false,
            currentIconModel:'',
            activeName: 'first',
            loading:false,
            list_item:false,
            propertyField:[],
            default_config:'',
            default_rules:'',
            ruleList:[],
            tableList:[],
            rules: {
                title: [{ required: true, message: '字段中文名不能为空', trigger: 'blur' }],
                field: [{ required: true, message: '字段英文名不能为空', trigger: 'blur' }],
                type: [{ required: true, message: '字段类型不能为空', trigger: 'blur' }],
                login_fields: [{ required: true, message: '请配置登录账号密码字段', trigger: 'blur' }],
            },
        }
    },
    methods: {
        submit(){
            this.$refs['form'].validate(valid => {
                if (valid) {
                    this.loading = true
                    updateField(this.form).then(res => {
                        if(res.status == '200'){
                            this.$message({message: '修改成功', type: 'success'})
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
            if(this.form.other_config == '' || this.form.other_config == null){
                this.form.other_config = {}
            }else{
                this.form.other_config = JSON.parse(this.info.other_config)
            }
            this.setDefaultVal('item_config')
            this.field.forEach(item=>{
                if(this.form.type == item.type){
                    this.list_item = item.item
                }
            })
            
            getConfigList().then(res => {
                this.ruleList = res.ruleList
                this.propertyField = res.propertyField
            })
        },
        selectType(){
            this.field.forEach(item=>{
                if(this.form.type == item.type){
                    this.propertyField.forEach(vo=>{
                        if(item.property == vo.type){
                            this.form.datatype = vo.name
                            this.form.length = vo.maxlen
                        }
                    })
                    this.list_item = item.item
                    if(!item.item){
                        this.form.item_config = []
                    }
                }
            })
        },
        setDefaultRule(){
            this.form.rule = this.default_rules
        },
        setFieldLength(){
            this.propertyField.forEach(item =>{
                if(this.form.datatype == item.name){
                    this.form.length = item.maxlen
                }
            })
        },
        addItem(key){
            this.setDefaultVal(key)
            this.form[key].push({})
        },
        deleteItem(key,index){
            this.form[key].splice(index,1)
        },
        clearItem(key){
            this.form[key] = []
        },
        setDefaultItem(val){
            this.form['item_config'] = val
        },
        setDefaultVal(key){
            if(this.form[key] == null || this.form[key] == ''){
                this.form[key] = []
            }
        },
        iconOpen(model){
            this.iconDialogStatus = true
            this.currentIconModel = model
        },
        selectIcon(icon){
            this.form.other_config.pre_icon = icon
        },
        getTablesByMenuId(){
            getTablesByMenuId({menu_id:this.$route.query.menu_id}).then(res => {
                this.tableList = res.data
            })
        },
        setPostStatus(){
            this.form.post_status = 0
        },
        checkCmsField(){
            checkCmsField({field:this.form.field})
        },
        closeForm(){
            this.$emit('update:show', false)
            this.loading = false
            this.$nextTick(()=>{
                this.$refs['form'].resetFields()
                this.default_rules = ''
                this.form.other_config = {}
            })
        }
    },
}
</script>
<style scoped>
.activeItem {margin-bottom:3px}
.el-checkbox-group li {float:left; margin-left:10px; width:150px; margin-top:10px}
</style>