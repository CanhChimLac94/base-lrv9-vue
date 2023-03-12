// import moment from 'moment';
import request from 'axios';
import { mapState } from 'vuex';

const colorList = [
  '#8c84f3', '#8bb0fc', '#ffa746', '#00af9a', '#f2a1a1', '#4772e6', '#4c4948',
];

const isEmpty = (o) => {
  if (undefined === o
    || null === o
    || '' === o
    || 0 === o.length) return true;
  return false;
}

const getDate = (txtTime) => {
  let date = new Date(txtTime);
  if (isEmpty(date)) {
    date = new Date();
  }
  const f = (n) => {
    if (n < 10) return `0${n}`;
    return `${n}`;
  }
  return `${f(date.getDate())}-${f(date.getMonth() + 1)}-${date.getFullYear()}`;
  // ${f(date.getHours())}:${f(date.getMinutes())}
}

const removeUnicode = (str) => {
  str = str.toLowerCase();
  str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, "a");
  str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, "e");
  str = str.replace(/ì|í|ị|ỉ|ĩ/g, "i");
  str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, "o");
  str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u");
  str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, "y");
  str = str.replace(/đ/g, "d");
  // Some system encode vietnamese combining accent as individual utf-8 characters
  str = str.replace(/\u0300|\u0301|\u0303|\u0309|\u0323/g, ""); // Huyền sắc hỏi ngã nặng 
  str = str.replace(/\u02C6|\u0306|\u031B/g, ""); // Â, Ê, Ă, Ơ, Ư
  return str;
}

const data = () => ({
  clipped: false,
  drawer: false,
  miniVariant: false,
  right: true,
  rightDrawer: false,
  fab: false,
  page: 1,
  isFixedAppBar: false,
  loadingNews: false,
  loadingNewsFull: false,
  isSearching: false,
  toplist: [],
  toplistDetail: {},
  keyword: '',
})

export default {
  data,
  computed: {
    ...mapState({
      menus: state => state.menus,
      topics: state => state.topics,
      newNew: state => state.newss,
      tags: state => state.tags,
      higlightPost: state => state.higlightPost,
      // news: state => state.news,
    }),
  },
  methods: {
    init() {
    },
    isEmpty,
    removeUnicode,
    stopEvent(evt) {
      evt.stopPropagation();
    },
    getLink(link) {
      return link.replace("/public", "");
    },
    urlFormat(link) {
      const regex = /\/| |\?|\:|\,|\'|\./g;
      link = link.replace(regex, '-');
      link = removeUnicode(link);
      return link;
    },
    getToLink(item) {
      const regex = /\/|\?|\:|\,|\'|\.|\%|\&|\*|\(|\)|\-|\=|\+/g;
      const regex2 = /\/| /g;
      const title = item.title.replace(regex, '').replace(regex2, '-');
      let link = removeUnicode(`/tin/${title}.${item.id}.html`);
      return link;
    },
    getFullLink(link) {
      if (undefined === link
        || null === link
        || link.indexOf('https:') >= 0
        || link.indexOf('http:') >= 0) {
        return link;
      }
      return `${window.location.origin}${link}`;
    },
    styleBackground(news) {
      return {
        "background-image": `url('${news ? this.getLink(news.img_path) : ""}')`,
      };
    },
    to(url) {
      // this.$router.push(url);
      window.location = url;
    },
    onScroll(e) {
      if (typeof window === 'undefined') return
      const top = window.pageYOffset || e.target.scrollTop || 0
      this.fab = top > 20;
      this.isFixedAppBar = top > 120;
    },
    toTop() {
      this.$vuetify.goTo(0, {
        duration: 1500,
        offset: 0,
        easing: 'easeInOutCubic',
      });
    },
    getThumb(img_path) {
      return this.getLink(img_path).replace("/upload", "/upload/_thumbs");
    },
    getDate(date) {
      return getDate(date);
      // if (undefined === date || null === date || '' === date) {
      //   date = new Date();
      // }
      // return moment.utc(date).local().format('DD-MM-yyyy HH:mm');
    },
    convertToMenu(topic) {
      const items = [];
      if (topic.subs.length && topic.subs.length > 0) {
        topic.subs.forEach((tp, i) => {
          items.push(this.convertToMenu(tp));
        });
      }
      return {
        title: topic.name,
        to: `/danh-muc/${topic.id}/${this.urlFormat(topic.name)}`,
        items,
      };
    },
    async loadTags() {
      if (!isEmpty(this.tags)) {
        return;
      }
      const onSuccess = (tags) => {
        this.$store.commit('updateTags', tags);
        this.reloadMenu();
      };
      if (!isEmpty(window.tags)) {
        onSuccess(window.tags);
        window.tags = null;
        return;
      }
      // return request.get("/api/client/news/tags").then(res => res.data)
      //   .then(data => {
      //     onSuccess(data.data || []);
      //   });
      return [];
    },
    async loadTopics() {
      if (!isEmpty(this.topics)) {
        return;
      }
      const onSuccess = (topics) => {
        this.$store.commit('updateTopics', topics);
        this.reloadMenu();
      }
      if (!isEmpty(window.topics)) {
        onSuccess(window.topics);
        window.topics = null;
        return;
      }
      // return request.get("/api/client/news/topics").then(res => res.data)
      //   .then(data => {
      //     onSuccess(data.data || []);
      //   });
      return [];
    },
    async reloadMenu() {
      const subMenus = [];
      const mainMenu = [];
      const width = window.innerWidth;
      let max_menu = width <= 1263
        ? 2 : (width <= 1903 ? 4 : 7);
      this.topics.forEach((tp, index) => {
        const menu = index <= max_menu ? mainMenu : subMenus;
        menu.push(this.convertToMenu(tp));
      });
      this.updateMenus([
        ...mainMenu,
        ...(isEmpty(subMenus) ? [] : [
          {
            icon: "mdi-chart-bubble",
            title: "Khác",
            items: subMenus,
            to: "/#",
          },
        ]),
        {
          icon: "mdi-chart-bubble",
          title: "Top bình chọn",
          to: "/toplist",
          items: [],
        },
      ]);
    },
    updateMenus(menus) {
      this.$store.commit('updateMenus', menus);
    },
    async loadNews() {
      // const topicId = this.$route.params.topicId;
      // let topicName = this.$route.params.title;
      const topicId = 1;
      let topicName = "news";

      const news = this.$store.state.news;
      if (this.loadingNewsFull || news.length >= 50) {
        this.loadingNews = false;
        this.loadingNewsFull = false;
        return;
      }
      if (this.loadingNews) {
        return;
      }

      this.loadingNews = true;
      let url = `/api/client/news?page=${this.page}`;
      if (undefined !== topicId && null !== topicId
        && undefined !== topicName && null !== topicName) {
        topicName = topicName.replace('?', '');
        url = `/api/client/danh-muc/${topicName}_${topicId}?page=${this.page}`;
      } else if (this.page <= 1 && !isEmpty(window.newsData)) {
        this.$store.commit('updateNews', window.newsData || []);
        window.newsData = null;
        this.loadingNews = false;
        this.page++;
        return;
      }
      // request.get(url).then((news) => {
      //   this.loadingNews = false;
      //   news = news.data.data || [];
      //   if (this.page > 1) {
      //     const currentNewsData = this.$store.state.news;
      //     news = [
      //       ...currentNewsData,
      //       ...news,
      //     ];
      //   }
      //   if (news.length > 0) {
      //     this.page++;
      //   } else {
      //     this.loadingNewsFull = true;
      //   }
      //   this.$store.commit('updateNews', news || []);
      //   // console.log('\n-----newss\n------\n', this.$store.state.news);
      // });
    },
    async searchNews(keyword) {
      keyword = keyword.trim();
      if (keyword === this.keyword) {
        return [];
      }
      // const res = await request.post(`/api/client/news/filter`, { keyword });
      // return res.data;
      return [];
    },
    async loadHiglightNews() {
      if (!isEmpty(this.higlightPost)) {
        return this.higlightPost;
      }
      // const res = await request.get(`/api/client/news/higlight`);
      const res = {
        data: []
      };
      this.$store.commit('higlightPost', res.data);
      return res.data;
    },
    async loadNewsNew() {
      if (!isEmpty(this.newNew)) {
        return this.newNew;
      }
      // const res = await request.get('/api/client/news/news');
      const res = {
        data: []
      };
      this.$store.commit('updateNewss', res.data);
      return res.data;
    },
    async loadByTopic(topic_id) {
      // return request.post('/api/client/news/bytopic', {
      //   topic_id
      // }).then(res => res.data);
      return [];
    },
    async loadTopList() {
      // return request.get(`/api/client/toplist?page=1`).then((res) => {
      //   this.toplist = res.data;
      // });
      return [];
    },
    async loadToplistDetail(id) {
      if (!isEmpty(window.post)) {
        this.toplistDetail = window.post;
        window.post = null;
        return;
      }
      if (null === id || undefined === id) {
        return;
      }
      // return request.get(`/api/client/toplist/detail?top=${id}`, { id }).then((res) => {
      //   this.toplistDetail = res.data;
      // });
      return [];
    },
    formatToplistContent() {
      let list = [];
      if (!isEmpty(this.toplistDetail.content)) {
        list = this.toplistDetail.content.list || [];
      }
      list.forEach((item, index) => {
        item.color = this.colorAt(index);
        list[index] = item;
      });
      return list;
    },
    randomColor() {
      const item = colors[Math.floor(Math.random() * colors.length)];
      return item;
    },
    colorAt(index) {
      const length = colorList.length;
      return colorList[index % length];
    },

  },
  mounted() {
    this.page = 1;
    this.loadingNewsFull = false;
  },
}