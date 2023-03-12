<template>
  <div class="text-center">
    <v-app-bar>
      <p>
        Hiển thị {{ startItemView }} tới {{ endItemView }} của
        {{ totalItem }} mục
      </p>
      <v-spacer />
      <v-pagination
        :total-visible="4"
        :value="curentPage"
        :length="totalPage"
        @input="nextPage"
      ></v-pagination>
    </v-app-bar>
  </div>
</template>
<script>
export default {
  props: {
    curentPage: {
      type: Number,
      default: 1,
    },
    totalPage: {
      type: Number,
      default: 1,
    },
    startItemView: {
      type: Number,
      default: 1,
    },
    endItemView: {
      type: Number,
      default: 1,
    },
    totalItem: {
      type: Number,
      default: 1,
    },
  },
  methods: {
    nextPage(evt) {
      this.$emit("curentPage", evt);
      this.$emit("nextPage", evt);
    },
  },
};
</script>