<template>
  <v-app dark>
    <v-main>
      <v-container>
        <Nuxt />
      </v-container>
    </v-main>
    <v-footer :absolute="!fixed" app>
    </v-footer>
  </v-app>
</template>

<script>
export default {
  head: {
    title: 'dev'
  },
  data() {
    return {
      clipped: false,
      drawer: false,
      fixed: false,
      miniVariant: false,
    };
  },
};
</script>
