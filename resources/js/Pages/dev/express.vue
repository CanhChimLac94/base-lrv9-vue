<template>
  <div>
    <v-btn @click="loadHtml">Load</v-btn>
    <div v-html="html"></div>
  </div>
</template>
<script>
import $ from "axios";
export default {
  layout: "dev",
  data() {
    return {
      html: "",
    };
  },
  methods: {
    loadHtml() {
      $.post("/api/new/getVNExpressHome").then((res) => {
        this.html = res.data[0].content;
        // console.log("TTT", { data: res.data });
      });
    },
  },
  mounted() {
    this.loadHtml();
  },
};
</script>