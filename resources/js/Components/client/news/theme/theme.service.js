import { mapState } from "vuex";
import defaultService from '../../default.service';

const data = () => {
  return {
    news: [],
  }
}

export default {
  mixins: [defaultService],
  data,
  props: {
    // datas: {
    //   type: Array,
    //   default: [],
    //   require: true,
    // },
  },
  computed: {
    ...mapState({
      topics: (state) => state.topics,
      datas: state => state.news || [],
    }),
  },
  methods: {
    to(item) {
      this.$router.push(
        `/danh-muc/${item.topic_id}/${item.topic_name}/${item.id}/${item.title}`
      );
    },
    getUrl(item) {
      return `${window.location.hostname}/danh-muc/${item.topic_id}/${item.topic_name}/${item.id}/${item.title}`;
    },
    getStandardLink(img_path) {
      return this.getLink(img_path);
    },
    getViewed(viewed) {
      viewed = viewed || Math.floor(Math.random() * 10000);
      viewed = viewed >= 1000 ? `${Math.floor(viewed / 1000)}k` : viewed;
      return `${viewed}`;
    },
    
  },
};