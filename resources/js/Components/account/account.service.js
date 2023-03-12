import { mapState } from 'vuex'
const $ = require('axios');

const data = () => ({
  user_name: '',
  password: '',
  register: {
    txtName: '',
    txtFullName: '',
    txtEmail: '',
    txtPass: '',
  },
  valid: false,
  nameRules: [
    v => !!v || 'User name is required',
    // v => (v && v.length <= 10) || 'Name must be less than 10 characters',
  ],
  paswordRules: [
    v => !!v || 'User name is required',
    // v => (v && v.length <= 10) || 'Name must be less than 10 characters',
  ],
  isLoadding: false,

});

export default {
  data,
  
  computed: {
    ...mapState({
      auth: state => state.auth
    }),

  },
  methods: {
    init() {
      this.$refs.form.validate();
      console.log('init login');
    },
    login() {
      this.isLoadding = true;
      // this.$auth.login({
      //   txtUserName: this.user_name,
      //   txtPassWord: this.password
      // }).then((res) => {
      //   console.log('Logined', { res });
      //   if(res.data.status !== 'ok'){
      //     return;
      //   }
      //   this.$router.push(res.data.next);
      // });
      return this.$auth.loginWith('laravelSanctum', {
        data: {
          txtUserName: this.user_name,
          txtPassWord: this.password
        }
      }).then((res) => {
        // this.isLoadding = false;
      }).catch((error) => {
        // this.isLoadding = false;
      });
    },
    logout() {
      this.$auth.logout();
    },
    submitRegister() {
      const utl = `/Account/Register`;
      $.post(url, this.register).then((res) => {
        this.user_name = this.register.txtName;
        this.password = this.register.txtPass;
        this.login();
      }).catch((error) => {

      });
    },
    submitLogin() {
      this.$refs.form.validate();
      if (!this.valid) {
        return;
      }
      this.login();
      // this.login().then((res) => {
      //   console.log('logged ok');
      //   // this.$router.push(res.data.url);
      //   this.isLoadding = false;
      // }).catch((err) => {
      //   // this.valid = false;
      //   this.isLoadding = false;
      // });
    },
  },
  mounted() {
    if (this.auth) {
      this.$router.push('/');
      // console.log('loged:', this.$router.push('/'));
    }
  },

}