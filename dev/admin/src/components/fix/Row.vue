<template>
  <div :class="classes" :style="rowStyle">
    <slot />
  </div>
</template>

<script>
import { computed, provide } from 'vue'
import { rowContextKey } from 'element-plus/es/components/row/src/constants.mjs'

export default {
  name: 'ElRow',
  props: {
    tag: { type: String, default: 'div' },
    gutter: { type: Number, default: 0 },
    justify: { type: String, default: 'start' },
    align: { type: String, default: '' }
  },
  setup(props) {
    provide(rowContextKey, {
      gutter: computed(() => props.gutter)
    })
    const rowStyle = computed(() => {
      if (!props.gutter) return {}
      const margin = `-${props.gutter / 2}px`
      return { marginLeft: margin, marginRight: margin }
    })
    const classes = computed(() => [
      'el-row',
      props.justify && props.justify !== 'start' ? `is-justify-${props.justify}` : '',
      props.align ? `is-align-${props.align}` : ''
    ])
    return { rowStyle, classes }
  }
}
</script>

<style>
.el-row > :not(.el-col) {
  flex: 1 1 100%;
  max-width: 100%;
}
</style>
