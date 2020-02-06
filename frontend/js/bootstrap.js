import 'moment/locale/ru';
import VueResource from 'vue-resource/dist/vue-resource.esm';
import Element from 'element-ui';
import locale from 'element-ui/lib/locale/lang/ru-RU';
import 'element-ui/lib/theme-chalk/index.css';
// наши собственные стили
import '../sass/style.scss';
// шрифты
import "roboto-fontface/css/roboto/sass/roboto-fontface.scss";

import 'file-loader?name=[name].[ext]!../index.php';
import 'file-loader?name=[name].[ext]!../.htaccess';
import 'file-loader?name=[name].[ext]!../favicon.ico';
import 'file-loader?name=[name].[ext]!../robots.txt';

import VueLocalStorage from 'vue-ls';
import VueCookie from 'vue-cookie';

Vue.use(VueResource);
Vue.use(Element, { locale });
Vue.use(VueLocalStorage, {namespace: 'tutji__'});
Vue.use(VueCookie);

Vue.http.interceptors.push((request, next) => {
    request.headers.set("X-CSRF-TOKEN", window.token);
    next();
});
Vue.http.options.emulateJSON = true;