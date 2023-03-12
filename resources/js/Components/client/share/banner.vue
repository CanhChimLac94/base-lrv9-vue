<template>
  <v-row class="banner-head">
    <v-col>
      <v-img src="/upload/banners/Bannerbantin.top.svg">
        <v-avatar :size="avartaSize" class="mt-4">
          <img class="style-scope yt-img-shadow" src="/upload/logos/bantin.logo.svg" alt="logo topneww.top" />
        </v-avatar>
        <client-share-search class="hidden-sm-and-down" />
      </v-img>
    </v-col>
  </v-row>
</template>
<style>
.row.banner-head {
  padding: 0px;
}

.row.banner-head .col {
  padding-top: 0px;
  padding-bottom: 0px;
}
</style>
<script>
import ClientShareSearch from "@/Components/client/share/search.vue";

export default {
  components: {
    ClientShareSearch
  },
  computed: {
    avartaSize() {
      // console.log("this.$vuetify", this.$vuetify.breakpoint);
      // switch (this.$vuetify.breakpoint.name) {
      //   case "xs":
      //     return 40;
      //   case "sm":
      //     return 60;
      //   case "md":
      //     return 70;
      // }
      return 80;
    },
  },
};
</script>