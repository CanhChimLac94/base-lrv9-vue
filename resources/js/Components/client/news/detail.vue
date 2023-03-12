<template>
  <v-container>
    <v-row class="news-detail-contain">
      <v-col cols="12" md="3" lg="3" class="hidden-sm-and-down">
        <client-topic-tags />
        <br />
        <client-news-orther title="Nổi bật" :datas="dataHiglight" />
        <div class="ads"></div>
      </v-col>

      <v-col cols="12" md="9" lg="6" offset-lg="0">
        <v-row class="header-detail">
          <v-col class="header-detail-content">
            <v-breadcrumbs :items="stepperes">
              <template v-slot:divider>
                <v-icon color="orange draken-4">mdi-chevron-double-right</v-icon>
              </template>
              <template v-slot:item="{ item }">
                <v-breadcrumbs-item>
                  <v-btn :to="item.to" color="secondary" text plain>
                    <span v-html="item.title"></span>
                  </v-btn>
                </v-breadcrumbs-item>
              </template>
            </v-breadcrumbs>
          </v-col>
        </v-row>
        <v-row class="news-content-detail">
          <v-col
            cols="12"
            v-if="!news"
            class="background-fff contain-post text-center"
          >
            <p>bài viết không tồn tại hoặc đã bị xóa</p>
          </v-col>
          <v-col cols="12" v-if="news" class="background-fff contain-post">
            <v-row class="post-header">
              <v-col cols="12" sm="6" md="4" lg="4">
                <v-img
                  :src="news.img_path"
                  :alt="news.title"
                  width="100%"
                ></v-img>
              </v-col>
              <v-col cols="12" sm="6" md="8" lg="8">
                <h2 class="post-title title-new" v-html="news.title"></h2>
                <div class="post-info">
                  <span class="post-date">
                    <span class="fa fa-clock-o"></span>
                    {{ getDate(news.updated_at) }}
                  </span>
                </div>
                <div class="post-description" v-html="news.description"></div>
              </v-col>
            </v-row>
            <v-row class="news-content">
              <v-col cols="12">
                <div class="post-content" v-html="news.content"></div>
              </v-col>
            </v-row>
          </v-col>
        </v-row>
      </v-col>
      <v-col cols="12" lg="3" offset-lg="0">
        <div>
          <client-news-orther
            title="Bài viết cùng chuyên mục"
            :datas="other_news"
            class="orther-same-topic"
          />
          <v-row>
            <v-col class="text-center">
              <v-btn
                class="ma-2"
                outlined
                color="indigo"
                :to="
                  removeUnicode(
                    `/danh-muc/${news.topic_id || 1}/${
                      news.topic_name || 'tin-tuc'
                    }`
                  )
                "
                router
              >
                Xem thêm >>
              </v-btn>
            </v-col>
          </v-row>
        </div>
        <div class="ads"></div>
      </v-col>
    </v-row>

    <v-row>
      <v-speed-dial v-model="dialShare" left fab fixed bottom direction="top">
        <template v-slot:activator>
          <v-btn fab bottom small color="primary">
            <v-icon v-if="dialShare">mdi-close</v-icon>
            <v-icon v-else>mdi-share-variant</v-icon>
          </v-btn>
        </template>
        <v-btn
          dark
          fab
          bottom
          small
          color="blue darken-7"
          :href="`https://www.linkedin.com/shareArticle?mini=true&url=${pageUrl}`"
          target="_blank"
          rel="noopener"
          aria-label="share on linkedin"
        >
          <v-icon>mdi-linkedin</v-icon>
        </v-btn>
        <v-btn
          dark
          fab
          bottom
          small
          color="blue"
          :href="`https://www.facebook.com/sharer/sharer.php?u=${pageUrl}`"
          target="_blank"
          rel="noopener"
          aria-label="share on facebook"
        >
          <v-icon>mdi-facebook</v-icon>
        </v-btn>
        <v-btn
          dark
          fab
          bottom
          small
          color="green"
          :href="`https://wa.me/?text=Checkout%20this%20page.%20${pageUrl}`"
          target="_blank"
          rel="noopener"
          aria-label="share on whatsapp"
        >
          <v-icon>mdi-whatsapp</v-icon>
        </v-btn>
        <v-btn
          dark
          fab
          bottom
          small
          color="tertiary"
          :href="`mailto:?subject=Awesomeness!&amp;body=Checkout this page!<a href='${pageUrl}'>${pageUrl}</a>`"
          target="_blank"
          rel="noopener"
          aria-label="share on email"
        >
          <v-icon>mdi-email</v-icon>
        </v-btn>
      </v-speed-dial>
    </v-row>
  </v-container>
</template>
<style>
.news-detail-contain .img-news {
  max-width: 100%;
  max-height: unset;
}
.news-detail-contain {
  margin: 0px;
}
</style>
<script>
import defaultService from "../default.service";
import ClientNewsOrther from "@/Components/client/news/orther.vue";
import ClientTopicTags from "@/Components/client/topic/tags.vue";

export default {
  components: {
    ClientNewsOrther,
    ClientTopicTags
  },
  mixins: [defaultService],
  head: {
    title: null,
    meta: null,
  },
  data() {
    return {
      news: {},
      news_id: 0,
      news_name: "",
      other_news: [],
      dataHiglight: [],
      topic: {},
      topic_name: "",
      pageUrl: window.location.href,
      dialShare: false,
      stepperes: [],
    };
  },
  computed: {},
  methods: {
    async init() {
      this.pageUrl = window.location.href;
      this.news = window.news || {};
      this.stepperes = [
        {
          title: "Topneww.top",
          to: "/",
        },
        {
          title: this.news.topic_name,
          to: `/danh-muc/${this.news.topic_id}/${this.news.topic_name}`,
        },
      ];
      const [dataHiglight, other_news] = await Promise.all([
        this.loadHiglightNews(),
        this.news.topic_id ? this.loadByTopic(this.news.topic_id) : [],
      ]);
      this.dataHiglight = dataHiglight;
      this.other_news = other_news;
    },
    styleBackground(news) {
      return {
        "background-image": `url('${news ? this.getLink(news.img_path) : ""}')`,
      };
    },
  },
  mounted() {
    this.init();
  },
};
</script>