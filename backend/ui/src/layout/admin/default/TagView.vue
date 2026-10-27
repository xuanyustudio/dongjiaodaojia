<template>
    <div id="tags-view-container" class="tags-view-container">
        <el-row>
            <el-col :span="22">
                <scroll-pane ref="scrollPane" class="tags-view-wrapper">
                    <router-link
                    v-for="tag in tags"
                    ref="tag"
                    :key="tag.path"
                    :class="isActive(tag)?'active':''"
                    :to="{ path: tag.path, query: tag.query, fullPath: tag.fullPath }"
                    tag="span"
                    class="tags-view-item"
                    >
                    {{ tag.title }}
                    <span v-if="tag.name !== 'Home'" style="color:#606266; font-size:50%" class="el-icon-close" @click.prevent.stop="closeSelectedTag(tag)" />
                    </router-link>
                </scroll-pane>
            </el-col>
            <el-col :span="2" class="moretag">
                <el-dropdown trigger="click" style="cursor: pointer;">
                    <span class="el-dropdown-link">
                    <span v-if="$store.getters.device!=='mobile'">其它操作</span> <i class="icontool el-icon-arrow-down"></i>
                    </span>
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item icon="el-icon-back" @click.native.prevent="closeLeftTags">关闭左侧</el-dropdown-item>
                        <el-dropdown-item icon="el-icon-right" @click.native.prevent="closeRightTags">关闭右侧</el-dropdown-item>
                        <el-dropdown-item icon="el-icon-close" @click.native.prevent="closeOthersTags">关闭其它</el-dropdown-item>
                        <el-dropdown-item icon="el-icon-circle-close" @click.native.prevent="closeAllTags">全部关闭</el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </el-col>
        </el-row>
    </div>
</template>

<script>
import ScrollPane from './ScrollPane'
export default {
    components: { ScrollPane },
    computed: {
        tags() {
            return this.$store.getters.tags
        },
        routes() {
            return this.$store.getters.addRoutes
        }
    },
    watch: {
        $route() {
            this.addTags()
        }
    },
    data() {
        return {
            visible: false
        }
    },
    mounted() {
        this.initTag()
        this.addTags()
    },
    methods: {
        isActive(route) {
            return route.path === this.$route.path
        },
        addTags() {
            const { name } = this.$route
            if (name) {
                this.$store.dispatch('addTags', this.$route)
            }
            return false
        },
        initTag(){
            for (const tag of this.routes[0].children) {
               if(tag.name == 'Home'){
                    this.$store.dispatch('addTags', tag)
               }
            }
        },
        closeSelectedTag(view) {
            this.$store.dispatch('deletetag', view).then(({ tags }) => {
                if (this.isActive(view)) {
                    this.toLastView(tags, view)
                }
            })
        },
        closeOthersTags() {
            this.$router.push(this.$route).catch(()=>{});
            this.$store.dispatch('delOthersViews', this.$route)
        },
        closeAllTags(view) {
            this.$store.dispatch('delAllViews').then(({ tags }) => {
                this.$router.push('/admin/Home/index.html')
            })
        },
        closeRightTags() {
            this.$store.dispatch('delRightTags', this.$route).then(tags => {
                if (!tags.find(i => i.fullPath === this.$route.fullPath)) {
                    this.toLastView(visitedViews)
                }
            })
        },
        closeLeftTags() {
            this.$store.dispatch('delLeftTags', this.$route).then(tags => {
                if (!tags.find(i => i.fullPath === this.$route.fullPath)) {
                    this.toLastView(visitedViews)
                }
            })
        },
        //关闭当前路由后跳转到下一个路由
        toLastView(tags, view) {
            const latestView = tags.slice(-1)[0]
            if (latestView) {
                if(latestView.name == 'Home'){
                    this.$router.push('/admin/Home/index.html')
                }else{
                    this.$router.push(latestView.fullPath)
                }  
            } else {
               this.$router.push('/admin/Home/index.html')
            }
        },
    },
}
</script>

<style lang="scss" scoped>
.tags-view-container {
  width: 100%;
  margin-bottom:5px;
  margin-top: -10px;
  .moretag{
    line-height:32px;text-align:right;color:#808695;font-size: 14px;
  }
  .tags-view-wrapper {
    .tags-view-item {
      display: inline-block;
      position: relative;
      cursor: pointer;
      height: 26px;
      line-height: 26px;
      color: #808695;
      background: #fff;
      padding: 3px 12px;
      font-size: 14px;
      margin-right: 5px;
      border: 1px solid #EBEEF5;
      &.active {
        color: rgb(64, 158, 255);
        border: 1px solid rgb(64, 158, 255);
      }
      &:hover {
        color:#606266
      }
    }
  }
}
</style>
