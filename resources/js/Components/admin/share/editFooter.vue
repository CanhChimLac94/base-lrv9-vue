<template>
  <v-row>
    <v-col>
      <v-app-bar class="actions-footer">
        <v-spacer />
        <v-btn @click="save"> Save </v-btn>
      </v-app-bar>
    </v-col>
  </v-row>
</template>
<script>
export default {
  methods: {
    save(evt) {
      this.$emit("save", evt);
    },
  },
};
</script>