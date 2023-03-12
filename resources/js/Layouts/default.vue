<template>
  <v-app light>
    <client-share-header />
    <v-main class="main-wrapper">
      <slot />
      <v-btn v-scroll="onScroll" v-show="fab" fab dark fixed bottom small right color="primary" @click="toTop">
        <v-icon>mdi-chevron-up</v-icon>
      </v-btn>
    </v-main>
    <client-share-footer />
  </v-app>
</template>
<style >
a {
  text-decoration: none;
}
</style>
<script>
import layOutService from "@/Components/client/default.service";
import ClientShareHeader from "@/Components/client/share/header.vue";
import ClientShareFooter from "@/Components/client/share/footer.vue";

export default {
  mixins: [layOutService],
  components: {
    ClientShareHeader,
    ClientShareFooter,
  }
};
</script>
