<template>
  <div>
    <p>title: {{ topic_name }}</p>
    <p>title: {{ topic_id }}</p>
  </div>
</template>
<script>
export default {
  data(){
    return {
      topic_name: this.$route.params.title,
      topic_id: this.$route.params.id
    }
  },
}
</script>