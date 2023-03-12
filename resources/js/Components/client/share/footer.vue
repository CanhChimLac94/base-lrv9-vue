<template>
  <v-footer class="news-footer">
    <v-container>
      <v-row>
        <v-col class="text-center">
          <v-btn
            v-for="(tag, index) in hastags"
            :key="index"
            :to="`/#${tag}`"
            color="primary"
            router
            text
            plain
            v-html="`#${tag}`"
            class="text-h7"
          >
          </v-btn>
        </v-col>
      </v-row>
      <client-topic-tags />
      <v-row>
        <v-col>
          <p class="text-center copyright">
            <span>&copy; Topneww.top@gmail.com</span>
          </p>
        </v-col>
      </v-row>
    </v-container>
  </v-footer>
</template>

<script>
import ClientTopicTags from "@/Components/client/topic/tags.vue";

export default {
  components: {
    ClientTopicTags
  },
  data() {
    return {
      hastags: [
        "tin tuc",
        "top new",
        "tong hop",
        "top news",
        "khoa hoc",
        "kinh te",
        "van hoa",
        "doi song",
        "tieu dung",
        "bat dong san",
        "my pham",
        "lam dep",
        "topneww.top",
        "tin tuc tong hop",
        "tin tong hop",
      ],
      fixed: false,
    };
  },
};
</script>