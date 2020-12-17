<template>
  <div>
    <input v-model="value"/>
    <button @click="add">添加</button>
    <ul>
      <todo-item v-for="(item, index) of items"
        :key="index"
        :content="item"
        :index="index"
        @remove="remove"
      ></todo-item>
    </ul>

  </div>
</template>

<script>
import TodoItem from './TodoItem'

export default {

  components:{
    'todo-item': TodoItem,
  },
  data(){
    return {
      value: '',
      items: [],
    }
  },

  methods:{
    add(){
      if(this.value == '') return;
      this.items.push(this.value);
      this.value = '';
    },
    remove: function(index){
        this.items.splice(index, 1);
    },
  },
}
</script>


<style scoped>

</style>
