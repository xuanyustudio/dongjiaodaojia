<template>
    <div>
        <el-drawer size="250px" title="系统设置" :model-value="show" direction="rtl" :before-close="handleClose">
            <div class="drawer-container">
                <div class="drawer-item">
                    <span>开启 Tags-Views</span>
                    <el-switch v-model="tagsView"  class="drawer-switch" />
                </div>
                
            </div>
        </el-drawer>
    </div>
</template>

<script>
export default {
    props: {
        show: {
            type: Boolean,
            default: false
        },
    },
    computed: {
        tagsView: {
            get() {
                return this.$store.getters.tagsView
            },
            set(val) {
                this.$store.dispatch('changeSetting', {
                    key: 'tagsView',
                    value: val
                })
            }
        },
    },
    methods: {
        handleClose(){
            this.$emit('update:show', false)
        },
        saveSetting() {
            const loading = this.$loading({
                lock: true,
                fullscreen: false,
                text: "正在保存到本地，请稍后...",
                spinner: "el-icon-loading",
                background: "rgba(0, 0, 0, 0.7)"
            });
            localStorage.setItem(
                "layout-setting",
                `{
                    "tagsView":${this.tagsView},
                }`
            );
            setTimeout(loading.close(), 1000)
        },
        resetSetting() {
            this.$loading({
                lock: true,
                fullscreen: false,
                text: "正在清除设置缓存并刷新，请稍后...",
                spinner: "el-icon-loading",
                background: "rgba(0, 0, 0, 0.7)"
            });
            localStorage.removeItem("layout-setting")
            setTimeout("window.location.reload()", 1000)
        }
    },
}
</script>

<style lang="scss" scoped>
.drawer-container {
    padding: 0 24px;
    font-size: 14px;
    line-height: 1.5;
    word-wrap: break-word;

    .drawer-title {
      margin-bottom: 12px;
      color: rgba(0, 0, 0, .85);
      font-size: 14px;
      line-height: 22px;
    }

    .drawer-item {
      color: rgba(0, 0, 0, .65);
      font-size: 14px;
    }

    .drawer-switch {
      float: right
    }
  }
</style>