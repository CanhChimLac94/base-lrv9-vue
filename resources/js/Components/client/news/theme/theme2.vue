<template>
  <v-row>
    <v-col cols="12" v-if="!datas || datas.length <= 0">
      <v-skeleton-loader
        class="mx-auto"
        max-width="300"
        type="card"
      ></v-skeleton-loader>
    </v-col>
    <v-col cols="12" class="news-theme2">
      <v-row class="new-items pointer" v-if="datas.length > 1">
        <template v-for="(item, i) in firstNews">
          <v-col cols="12" :sm="i === 0 ? 12 : 6" :key="i" v-if="true">
            <v-card outlined flat router tag="a" :href="getToLink(item)">
              <v-img
                class="white--text align-end"
                :src="getStandardLink(item.img_path)"
                :lazy-src="getStandardLink(item.img_path)"
                height="200px"
              >
              </v-img>
              <v-card-title>
                <span class="date-time">
                  <client-news-social-share
                    :url="getUrl(item)"
                    :date="getDate(item.updated_at)"
                    :viewed="getViewed(item.viewed)"
                  />
                </span>
                <p v-html="item.title" class="text-subtitle-2"></p>
              </v-card-title>
            </v-card>
          </v-col>
        </template>
      </v-row>
      <template v-for="(item, index) in datas">
        <v-row
          v-if="index > 2"
          class="new-item"
          :key="index"
          tag="a"
          :href="getToLink(item)"
        >
          <v-col
            cols="12"
            sm="6"
            md="4"
            lg="4"
            class="new-sub-item new-thumbnail"
          >
            <v-img
              :alt="item.title"
              class="thumbnail"
              :src="getThumb(item.img_path)"
              :lazy-src="getThumb(item.img_path)"
            ></v-img>
          </v-col>
          <v-col
            cols="12"
            sm="6"
            md="8"
            lg="8"
            class="new-sub-item new-content pl-1"
          >
            <div class="new-title">
              <p class="text-title" v-html="item.title"></p>
            </div>
            <div class="new-description">
              <p class="text-description text-caption" v-html="item.description"></p>
            </div>
            <div class="new-more-info">
              <client-news-social-share
                :url="getUrl(item)"
                :date="getDate(item.updated_at)"
                :viewed="getViewed(item.viewed)"
              />
            </div>
          </v-col>
        </v-row>
      </template>
    </v-col>
  </v-row>
</template>
<style>
.news-theme2 .new-items .v-card {
  border: none;
}
.news-theme2 .new-items .v-card .v-card__title,
.news-theme2 .new-items .v-card .v-card__text {
  padding-left: 0px;
  padding-right: 0px;
  color: #555;
  word-break: keep-all;
}
.row.new-item {
  color: #555;
  text-decoration: none;
}
.new-items .date-time {
  font-size: 0.7em;
  color: #8a8a8a;
  width: 100%;
}
</style>
<script>
import themeService from "./theme.service";
import ClientNewsSocialShare from "@/Components/client/news/socialShare.vue";
export default {
  components: {
    ClientNewsSocialShare
  },
  mixins: [themeService],
  data() {
    return {
      firstNews: [],
    };
  },
  methods: {
    formatData() {
      this.firstNews = [];
      this.datas.forEach((item, index) => {
        if (index <= 2) {
          this.firstNews.push(item);
        } else return;
      });
    },
  },
  mounted() {
    this.formatData();
  },
  watch: {
    datas() {
      this.formatData();
    }
  },
};
</script>