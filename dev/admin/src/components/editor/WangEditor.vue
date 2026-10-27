<template>
  <div id="editor" ref="editor"></div>
</template>
 
<script>
	import E from 'wangeditor'
	export default {
    name:'WangEditor',
    props:{
      wangEditorContent:{
        type:String,
      },
      isClear: {
        type: Boolean,
        default: false
      },
      upload_config_id:{
        type:Number,
      }
    },
		data() {
		  return {
		    editor: "",
        syncing: false
		  }
    },
    methods: {
      applyContent(val) {
        if (!this.editor || !this.editor.txt) return
        const next = val || ''
        if (this.editor.txt.html() === next) return
        this.syncing = true
        this.editor.txt.html(next)
        this.syncing = false
      }
    },
    watch: {
      isClear(val) {
        if(val) {
          this.editor.txt.clear()
        }
      },
      wangEditorContent(val) {
        this.applyContent(val)
      }
    },
		mounted() {
		  this.$nextTick(function() {
		    const _this = this
        this.editor = new E(this.$refs.editor)
        this.editor.config.uploadImgMaxLength = 1
        this.editor.config.uploadFileName = 'file'
        this.editor.config.uploadImgAccept = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']
        this.editor.config.uploadImgMaxSize = 10 * 1024 * 1024
        this.editor.config.uploadImgServer = '#'
        this.editor.config.onchange = (html)=>{
          if (_this.syncing) return
          _this.$emit('update:wangEditorContent',html)
        }
        this.editor.create()
        this.applyContent(this.wangEditorContent)

        this.editor.config.uploadImgHooks = {
          error: function(xhr, editor, resData) {
              console.log('error', xhr, resData)
          },
          customInsert: function(insertImgFn, result) {
              insertImgFn(result.data)
          }
        }

        this.editor.config.customUploadImg = function (resultFiles, insertImgFn) {
            let formdata = new FormData()
            formdata.append('file', resultFiles[0])
            formdata.append('edit', true)
            formdata.append('upload_config_id', _this.upload_config_id)
            _this.$axios.post('/Upload/upload', formdata).then(res => {
              insertImgFn(res.data.data)
            })
        }
		  });
		}
	}
</script>