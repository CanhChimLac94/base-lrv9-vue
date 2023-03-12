<template>
  <layout>
    <v-container v-scroll="onScroll">
      <v-row class="container-news">
        <v-col cols="12" md="3" lg="3">
          <client-news-orther title="News" :datas="datasNeww" />
        </v-col>
        <v-col cols="12" md="6" lg="6" offset-lg="0">
          <client-news-theme2 />
          <p ref="dataNews"></p>
          <!-- :datas="news" -->
        </v-col>
        <v-col cols="12" md="3" lg="3" offset-lg="0">
          <client-topic-tags />
          <br />
          <client-news-orther title="Nổi bật" :datas="dataHiglight" />
        </v-col>
      </v-row>
      <v-row v-if="loadingNews">
        <v-progress-linear indeterminate color="cyan"></v-progress-linear>
      </v-row>
    </v-container>
  </layout>
</template>

<style>
.container-news {
  margin-left: 0px;
  margin-right: 0px;
}
</style>

<script>
import { mapState } from "vuex";
import defaultService from "../default.service";

import Layout from "@/Layouts/default.vue";

import ClientNewsOrther from "@/Components/client/news/orther.vue";
import ClientNewsTheme2 from "@/Components/client/news/theme/theme2.vue";
import ClientTopicTags from "@/Components/client/topic/tags.vue";

export default {
  name: "ClientNews",
  mixins: [defaultService],
  components: {
    Layout,
    ClientNewsOrther,
    ClientNewsTheme2,
    ClientTopicTags
  },
  computed: {
    ...mapState({
      // news: (state) => state.news || [],
    }),
  },
  data() {
    return {
      datasNeww: [],
      dataHiglight: [],
    };
  },
  methods: {
    async init() {
      this.page = 1;
      this.loadNews();
      const [neww, higlight] = await Promise.all([
        this.loadNewsNew(),
        this.loadHiglightNews(),
      ]);
      this.datasNeww = neww;
      this.dataHiglight = higlight;
    },
    onScroll(evt) {
      const scrollHeight =
        Math.max(
          window.pageYOffset,
          document.documentElement.scrollTop,
          document.body.scrollTop
        ) + window.innerHeight;
      const height = this.$refs.dataNews.offsetTop;
      const bottomOfWindow = scrollHeight >= height;
      // document.documentElement.offsetHeight;
      if (!bottomOfWindow) {
        return;
      }
      this.loadNews();
    },
  },
  mounted() {
    this.init();
  },
  updated() { },
};
</script>