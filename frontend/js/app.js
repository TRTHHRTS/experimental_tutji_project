// Подключаем все основные либы для работы
import VueRouter from 'vue-router';
import defaultMixins from './mixin';
import routes from './routes';
import VueI18n from 'vue-i18n';
import messages from './lang/index';

import './bootstrap';
import './globalVueFilters';
import store from './store';

const App = () => import('./App.vue');

Vue.mixin(defaultMixins);
Vue.use(VueRouter);
Vue.use(VueI18n);

const router = new VueRouter({
    routes: routes
});
const i18n = new VueI18n({
    locale: 'ru',
    messages
});
Vue.prototype.$locale = {
    change (language) {
        i18n.locale = language;
    },
    current () {
        return i18n.locale;
    }
};

const app = new Vue({
    el: '#app',
    i18n,
    store,
    router,
    data() {
        return {
            createdPromise: null,
        }
    },
    methods: {
        /**
         * Установить новый токен
         */
        async getNewToken() {
            window.token = await this.get('/auth/token');
        },
        /**
         * Показать подсказу по cookie
         */
        showCookiePromt() {
            const cookiePolicyReaded = this.$cookie.get('cookiePolicyReaded') === 'true';
            if (!cookiePolicyReaded) {
                this.$notify.info({
                    title: this.$t('p.cookie.promtTitle'),
                    message: this.$t('p.cookie.promt'),
                    dangerouslyUseHTMLString: true,
                    duration: 0,
                    offset: 100,
                    onClose: () => {
                        this.$cookie.set('cookiePolicyReaded', true, 10080);
                    }
                });
            }
        },
        /**
         * Промис, сигнализирующий об окончании загрузки начальных данных, используетсяв компонентах.
         * Так как Vue не выполняет хуки асинхронно.
         * @returns {Promise<void>}
         */
        async waitAppDataLoading() {
            if (this.createdPromise) {
                await this.createdPromise;
            }
        }
    },
    computed: mapState(['categories', 'pageFilter']),
    async created() {
        this.createdPromise = (async resolve => {
            this.showCookiePromt();

            await this.getNewToken();
            const lang = this.$cookie.get('lang');
            this.$locale.change(lang);

            this.$store.commit('initDataLoading', true);
            let loading = this.$loading({
                lock: true,
                text: this.$t('loading'),
                background: 'rgba(0, 0, 0, 0.9)'
            });

            let response = await this.get('/start');
            if (response.user) {
                this.$store.commit('setUserInfo', response.user);
            }
            this.$store.commit('setCategories', response.categories);
            // устанавливаем в фильтре по-умолчанию, что выбраны все категории, чтобы поиск сразу находил максимально результатов
            this.pageFilter.selectAll = true;
            _.forEach(this.categories, (value, key) => {
                this.pageFilter.CATS.push(value.id);
            });
            this.$store.commit('setAgings', response.agings);
            this.$store.commit('setAuditories', response.auditories);
            this.$store.commit('setCountries', response.countries);
            this.$store.commit('setStatuses', response.statuses);
            this.$store.commit('setNews', response.news);
            this.$store.commit('addStates', { code: 'RU', states: response.states});
            this.$store.commit('setSystemProperties', response.systemProperties);
            this.$store.commit('setReserveStatuses', response.reserveStatuses);
            this.$store.commit('setWithdrawalStatuses', response.withdrawalStatuses);
            this.$store.commit('initDataLoading', false);
            loading.close();
        })();
    },
    render: h => h(App)
});

/*
$(document).ready(() => {
    $(window).scroll(() => {
        if ($(this).scrollTop() > 100) {
            $('.scrollup').fadeIn();
        } else {
            $('.scrollup').fadeOut();
        }
    });
    $('.scrollup').click(() => {
        $("html, body").animate({
            scrollTop: 0
        }, 600);
        return false;
    });
});*/