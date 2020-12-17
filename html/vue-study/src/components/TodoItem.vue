<template>
  <li>{{content}} <a href="javascript:;" @click="remove">删除</a></li>
</template>

<script>
export default {

  props: ['content', 'index'],
  methods: {
    remove(){
      this.$emit('remove', this.index)
    },
  }

}
</script>


<style scoped>

</style>
