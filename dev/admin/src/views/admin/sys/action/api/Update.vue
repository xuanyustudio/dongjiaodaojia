<template>
<div>
    <el-dialog title="创建操作方法" width="700px" class="icon-dialog" :model-value="show" @open="open" :before-close="closeForm">
        <el-form :size="size" ref="form" :model="form" :rules="rules" label-width="90px">
            <el-tabs v-model="activeName">
                <el-tab-pane style="padding-top:10px"  label="基本信息" name="基本信息">    
                    <el-row>
                        <el-form-item label="方法类型" prop="type">
                            <el-select style="width:100%" v-model="form.type" @change="selectType" filterable placeholder="请选择">
                                <el-option v-for="(item,i) in actionList" :key="i" :label="item.name" :value="item.type"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-row>
                    <el-row>
                        <el-col :span="12">
                            <el-form-item label="方法名称" prop="name">
                                <el-input v-model="form.name" clearable placeholder="方法中文名称"  />
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item label="英文名称" prop="action_name">
                                <el-input v-model="form.action_name" clearable placeholder="方法英文名称"/>
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <el-row v-if="form.type==50">
                        <el-form-item label="登录方式" prop="login_type">
                            <el-select style="width:100%" v-model="form.other_config.login_type" filterable placeholder="请选择">
                                <el-option label="账号密码登录" value="1"></el-option>
                                <el-option label="短信验证码登录" value="2"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-row>
                    <el-row v-if="form.type==50">
                        <el-row v-if="form.other_config.login_type == 1">
                            <el-col :span="12">
                                <el-form-item label="用户名字段" prop="userField">
                                    <el-select style="width:100%" v-model="form.other_config.userField" filterable placeholder="请选择字段">
                                        <el-option v-for="(vo,i) in post_fields" :key="i"  :value="vo.field">{{vo.title}}({{vo.field}})</el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item label="密码字段" prop="pwdField">
                                    <el-select style="width:100%" v-model="form.other_config.pwdField" filterable placeholder="请选择字段">
                                        <el-option v-for="(vo,i) in post_fields" :key="i"  :value="vo.field">{{vo.title}}({{vo.field}})</el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                        </el-row>
                        <el-row v-if="form.other_config.login_type == 2">
                            <el-col :span="24">
                                <el-form-item label="手机号字段" prop="smsField">
                                    <el-select style="width:100%" v-model="form.other_config.smsField" filterable placeholder="请选择字段">
                                        <el-option v-for="(vo,i) in post_fields" :key="i"  :value="vo.field">{{vo.title}}({{vo.field}})</el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                        </el-row>
                    </el-row>
                    <el-row v-if="form.type==51">
                        <el-form-item label="短信平台" prop="sms_partenr">
                            <el-select style="width:100%" v-model="form.other_config.sms_partenr" filterable placeholder="请选择">
                                <el-option v-for="(item,i) in sms_list" :key="i" :label="item.name" :value="item.type"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-row>
                    <el-row v-if="dialog">
                        <el-form-item :label="form.type ==5 || form.type ==50 ? '返回字段' : '操作字段'" prop="fields">
                            <el-checkbox-group v-model="form.fields">
                                <el-checkbox v-for="item in post_fields" :label="item.field" :key="item.field">{{item.title}}</el-checkbox>
                            </el-checkbox-group>
                        </el-form-item>
                    </el-row>
                    <el-row v-if="form.type == 7">
                        <el-form-item label="状态值" prop="status_val">
                            <el-input v-model="form.status_val" placeholder="状态值"/>
                        </el-form-item>
                    </el-row>
                    <el-row v-if="form.type == 1">
                        <el-col :span="12">
                            <el-form-item label="分页大小" prop="type">
                                <el-select v-model="form.pagesize" placeholder="请选择">
                                    <el-option key="1" label="10条每页" value="10"></el-option>
                                    <el-option key="2" label="20条每页" value="20"></el-option>
                                    <el-option key="3" label="50条每页" value="50"></el-option>
                                    <el-option key="4" label="100条每页" value="100"></el-option>
                                    <el-option key="5" label="200条每页" value="200"></el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item label="排序" prop="orderby">
                                <el-input v-model="form.orderby" placeholder="如 id desc"/>
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <el-row v-if="form.type == 1">
                        <el-col :span="24">
                            <el-form-item label="树table配置" prop="tree_config">
                                <el-input v-model="form.tree_config" placeholder="父类字段名"/>
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <el-row v-if="form.type == 1">
                        <el-form-item label="过滤条件" prop="list_filter">
                            <el-row v-for="(item,i) in form.list_filter" :key="i">
                                <el-col :span="12">
                                    <el-form-item class="activeItem">
                                        <el-select style="width:100%" v-model="item.searchField" filterable placeholder="请选择字段">
                                            <el-option v-for="(vo,i) in post_fields" :key="i"  :value="vo.field">{{vo.title}}({{vo.field}})</el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                                <el-col :span="6">
                                    <el-form-item class="activeItem">
                                        <el-input style="position:relative; left:10px" v-model="item.serachVal" placeholder="值"/>
                                    </el-form-item>
                                </el-col> 
                                <el-col :span="6">
                                    <el-button type="danger" size="small" style="position:relative;left:15px"  @click="deleteItem('list_filter',i)"><i class="el-icon-close"></i></el-button>
                                </el-col> 
                            </el-row>
                            <el-button type="success" style="padding:5px 7px" :size="size" @click="addItem('list_filter')"><i class="el-icon-plus"></i>追加</el-button>
                            <el-button v-if="form.list_filter.length > 0" type="warning" icon="el-icon-delete" style="padding:5px 7px" :size="size" @click="clearItem('list_filter')">清空</el-button>
                        </el-form-item>
                    </el-row>
                </el-tab-pane>
                <el-tab-pane v-if="[1,2,3,4,5,11].includes(form.type)" style="padding-top:10px"  label="多表配置" name="多表配置">
                    <el-form-item label="关联模型" prop="with_join">
                        <el-row :gutter="2" v-for="(item,i) in form.with_join" :key="i">
                            <el-col style="margin-left:0px" :span="5">
                                <el-form-item class="activeItem"  prop="fk">
                                    <el-select style="width:100%" v-model="item.fk" filterable placeholder="主表外键">
                                        <el-option v-for="(vo,i) in model_fields" :key="i"  :value="vo.field">{{vo.title}}({{vo.field}})</el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :span="5">
                                <el-form-item class="activeItem" prop="relative_table">
                                    <el-select style="width:100%" v-model="item.relative_table" filterable placeholder="模型">
                                        <el-option v-for="(vo,i) in tableList" :key="i" :value="vo.controller_name">{{vo.controller_name}}</el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :span="5">
                                <el-form-item class="activeItem" prop="pk">
                                    <el-select @focus="getTableFields(i)" style="width:100%" v-model="item.pk" filterable placeholder="关联键">
                                        <el-option v-for="(vo,i) in table_fields" :key="i"  :value="vo.Field">{{vo.Field}}({{vo.Comment}})</el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :span="7">
                                 <el-form-item class="activeItem" prop="fields">
                                    <el-select @focus="getTableFields(i)" style="width:100%" multiple collapse-tags v-model="item.fields" filterable :placeholder="form.type == 1?'关联表查询字段':'操作字段'">
                                        <el-option v-for="(vo,i) in table_fields" :key="i"  :value="vo.Field">{{vo.Field}}({{vo.Comment}})</el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :span="1">
                                <el-button type="danger" size="small" style="position:relative;left:5px"  @click="deleteItem('with_join',i)"><i class="el-icon-close"></i></el-button>
                            </el-col> 
                        </el-row>
                        <el-button type="success" style="padding:5px 7px" :size="size" @click="addItem('with_join')"><i class="el-icon-plus"></i>追加</el-button>
                        <el-button v-if="form.tab_config.length > 0" type="warning" icon="el-icon-delete" style="padding:5px 7px" :size="size" @click="clearItem('with_join')">清空</el-button>
                    </el-form-item>
                    <el-row v-if="form.type==1">
                        <el-form-item label="table列表sql" prop="sql">
                            <el-input v-model="form.sql" type="textarea" placeholder="通过sql语句生成table列表"  />
                        </el-form-item>
                    </el-row>
                </el-tab-pane>
            </el-tabs>
            
        </el-form>
        <template #footer><div class="dialog-footer">
            <el-button :size="size" :loading="loading" type="primary" @click="submit" >
                <span v-if="!loading">确 定</span>
                <span v-else>提 交 中...</span>
            </el-button>
            <el-button :size="size" @click="closeForm">取 消</el-button>
        </div></template>
    </el-dialog>
</div>
</template>

<script>
import {getPostField,getTableFields,updateAction} from '../../sys'
export default {
    name:'UpdateAction',
    props: {
        show: {
            type: Boolean,
            default: false
        },
        size: {
            type: String,
            default: 'small'
        },
        action: {
            type: Array,
        },
        info: {
            type: Object,     
        }
    },
    computed: {
        actionList(){
            return this.action.filter(item=>item.show_api)
        }
    },
    data() {
        return {
            form: {
                server_create_status:1,
                vue_create_status:1,
                select_type:1,
                btn_color:'primary',
                fields:[],
                type:'3',
                menu_id:this.$route.query.menu_id,
                list_filter:[],
                tab_config:[],
                with_join:[],
                other_config:{
                },
            },
            post_fields:[],
            activeName: 'first',
            tableList:[],
            dialog:false,
            button:true,
            loading:false,
            activeName:'基本信息',
            table_fields:[],
            model_fields:[],
            tab_fields:[],
            sms_list:[],
            rules: {
                name: [{ required: true, message: '方法中文名不能为空', trigger: 'blur' }],
                action_name: [{ required: true, message: '方法英文名不能为空', trigger: 'blur' }],
                type: [{ required: true, message: '方法类型不能为空', trigger: 'blur' }],
            },
        }
    },
    methods: {
        submit(){
            this.$refs['form'].validate(valid => {
                if (valid) {
                    this.loading = true
                    updateAction(this.form).then(res => {
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
            this.setDefaultVal('list_filter')
            this.setDefaultVal('tab_config')
            this.setDefaultVal('fields')
            if(this.form.other_config == '' || this.form.other_config == null){
                this.form.other_config = {}
            }else{
                this.form.other_config = JSON.parse(this.info.other_config)
            }
            this.initAction()
            getPostField({menu_id:this.$route.query.menu_id}).then(res => {
                this.post_fields = res.data
                this.model_fields = res.model_fields
                this.tab_fields = res.tab_fields
                this.tableList = res.tableList
                this.sms_list = res.sms_list
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
        setDefaultVal(key){
            if(this.form[key] == null || this.form[key] == ''){
                this.form[key] = []
            }
        },
        selectType(val){
            if(val !== 1){
                this.form.list_filter = []
                this.form.with_join = []
            }

            if(val !== 3 || val !== 4){
                this.form.tab_config = []
            }
            if(val !== 7){
                this.form.dialog_size = ''
            }
            this.initAction()
        }, 
        initAction(){
            this.action.forEach(item=>{
                if(this.form.type == item.type){
                    this.dialog = item.dialog
                    this.button = item.button
                }
            })
        },
        getTableFields(i){
            getTableFields({controller_name:this.form.with_join[i].relative_table}).then(res => {
                this.table_fields = res.filedList
            })
        },
        closeForm(){
            this.$emit('update:show', false)
            this.loading = false
            this.$nextTick(()=>{
                this.$refs['form'].resetFields()
                this.form.other_config = {}
            })
        }
    },
}
</script>
<style scoped>
.el-checkbox-group li {float:left; margin-left:10px; width:150px; margin-top:10px}
.activeItem {margin-bottom:3px}
</style>