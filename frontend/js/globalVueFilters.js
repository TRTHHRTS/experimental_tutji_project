Vue.filter('formatDate', function(value) {
    if (value) {
        return moment(value).format('DD.MM.YY')
    }
});
Vue.filter('formatPhone', function(phone) {
    if (!phone) {
        return '';
    }
    return phone.replace(/[^0-9]/g, '').replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
});
Vue.filter('formatDuration', function(value) {
    if (value) {
        let momentValue = moment(value, 'HHmm');
        return momentValue.format(momentValue.hour() > 0 ? 'H ч. m мин.' : 'm мин.');
    }
});
Vue.filter('formatDateTime', function(value) {
    return moment(value).format('HH:mm DD.MM.YY');
});
Vue.filter('formatStatus', function(value) {
    return moment(value).format('HH:mm DD.MM.YY');
});
Vue.filter('formatDateTimeFromNow', function(value) {
    return moment(value).fromNow();//.format('H:m DD.MM.YY');
});
Vue.prototype.$filters = Vue.options.filters;