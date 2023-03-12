<template>
  <v-container> 

  </v-container>
</template>

<script>
import { mapState } from "vuex";
export default {
  computed: {
    ...mapState({
      news: (state) => state.news || [],
    }),
  },
};
</script>